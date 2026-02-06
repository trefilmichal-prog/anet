<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/settings_repository.php';
require_once dirname(__DIR__) . '/includes/festival_content.php';
require_once dirname(__DIR__) . '/includes/upload.php';

require_admin_login();

$pinMessage = '';
$pinError = '';
$festivalMessage = '';
$festivalError = '';
$menuMessage = '';
$menuError = '';
$fontMessage = '';
$fontError = '';
$logoMessage = '';
$logoError = '';
$brandMessage = '';
$brandError = '';
$menuDefaults = get_admin_menu_defaults();
$festivalText = get_setting('festival_page_text', get_default_festival_page_text());
$adminMenuEnabledValue = get_setting('admin_menu_bg_enabled', $menuDefaults['admin_menu_bg_enabled']);
$adminMenuEnabled = $adminMenuEnabledValue === '1';
$adminMenuColor = get_setting('admin_menu_bg_color', $menuDefaults['admin_menu_bg_color']);
$adminMenuOpacity = get_setting('admin_menu_bg_opacity', $menuDefaults['admin_menu_bg_opacity']);
$siteFontFamily = get_setting('site_font_family', get_site_font_family_default());
$siteLogoPath = get_setting('site_logo_path', '');
$siteLogoType = get_setting('site_logo_type', 'image');
$siteLogoText = get_setting('site_logo_text', '');
$brandDefaults = array(
    'type' => 'svg',
    'value' => 'inline'
);
$brandConfigPath = dirname(__DIR__) . '/includes/brand-config.php';
if (file_exists($brandConfigPath)) {
    $brandConfigFallback = require $brandConfigPath;
    if (is_array($brandConfigFallback)) {
        if (isset($brandConfigFallback['type'])) {
            $brandDefaults['type'] = $brandConfigFallback['type'];
        }
        if (isset($brandConfigFallback['value'])) {
            $brandDefaults['value'] = $brandConfigFallback['value'];
        }
    }
}
$brandTypeValue = get_setting('brand_type', '');
$brandValueValue = get_setting('brand_value', '');
$brandType = $brandTypeValue !== '' ? $brandTypeValue : $brandDefaults['type'];
$brandValue = $brandValueValue !== '' ? $brandValueValue : $brandDefaults['value'];
$brandLeftDesktopValue = get_setting('brand_left_desktop', '');
$brandLeftMobileValue = get_setting('brand_left_mobile', '');
$legacyBrandPositionDesktop = get_setting('brand_position_desktop', '');
$legacyBrandPositionMobile = get_setting('brand_position_mobile', '');
$legacyBrandPositionDesktopLeftPctValue = get_setting('brand_position_desktop_left_pct', '');
$legacyBrandPositionMobileLeftPctValue = get_setting('brand_position_mobile_left_pct', '');

function normalize_brand_left_input($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }
    if (!preg_match('/^\\d+(?:\\.\\d+)?%?$/', $value)) {
        return null;
    }
    $value = rtrim($value, '%');
    if ($value === '' || !is_numeric($value)) {
        return null;
    }
    $numeric = (float) $value;
    if ($numeric < 0 || $numeric > 100) {
        return null;
    }
    $normalized = rtrim(rtrim(sprintf('%.4F', $numeric), '0'), '.');
    if ($normalized === '') {
        $normalized = '0';
    }
    return $normalized . '%';
}

function resolve_brand_left_display($currentValue, $legacyPosition, $legacyLeftPctValue)
{
    $normalized = normalize_brand_left_input($currentValue);
    if ($normalized !== null && $normalized !== '') {
        return $normalized;
    }
    if ($legacyPosition === 'left') {
        $legacyNormalized = normalize_brand_left_input($legacyLeftPctValue);
        if ($legacyNormalized !== null && $legacyNormalized !== '') {
            return $legacyNormalized;
        }
    }
    return '';
}

$brandLeftDesktopDisplay = resolve_brand_left_display(
    $brandLeftDesktopValue,
    $legacyBrandPositionDesktop,
    $legacyBrandPositionDesktopLeftPctValue
);
$brandLeftMobileDisplay = resolve_brand_left_display(
    $brandLeftMobileValue,
    $legacyBrandPositionMobile,
    $legacyBrandPositionMobileLeftPctValue
);

try {
    $db = get_db();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? trim($_POST['action']) : '';

        if ($action === 'change_pin') {
            $currentPin = isset($_POST['current_pin']) ? trim($_POST['current_pin']) : '';
            $newPin = isset($_POST['new_pin']) ? trim($_POST['new_pin']) : '';
            $confirmPin = isset($_POST['confirm_pin']) ? trim($_POST['confirm_pin']) : '';

            $stmt = $db->prepare('SELECT value FROM settings WHERE key = :key');
            $stmt->execute(array(':key' => 'admin_pin_hash'));
            $pinHash = $stmt->fetchColumn();

            if (!$pinHash || !password_verify($currentPin, $pinHash)) {
                $pinError = 'Aktuální PIN není správný.';
            } elseif ($newPin === '' || strlen($newPin) < 4) {
                $pinError = 'Nový PIN musí mít alespoň 4 znaky.';
            } elseif ($newPin !== $confirmPin) {
                $pinError = 'Nový PIN a potvrzení se neshodují.';
            } else {
                $newHash = password_hash($newPin, PASSWORD_DEFAULT);
                $update = $db->prepare('UPDATE settings SET value = :value WHERE key = :key');
                $update->execute(array(
                    ':value' => $newHash,
                    ':key' => 'admin_pin_hash'
                ));
                $pinMessage = 'PIN byl úspěšně změněn.';
            }
        } elseif ($action === 'save_festival_text') {
            $festivalText = isset($_POST['festival_text']) ? trim($_POST['festival_text']) : '';

            if ($festivalText === '') {
                $festivalError = 'Text O festivalu nesmí být prázdný.';
                $festivalText = get_setting('festival_page_text', get_default_festival_page_text());
            } else {
                set_setting('festival_page_text', $festivalText);
                $festivalMessage = 'Text O festivalu byl uložen.';
            }
        } elseif ($action === 'save_admin_menu') {
            $adminMenuEnabled = isset($_POST['admin_menu_bg_enabled']) ? '1' : '0';
            $adminMenuColorInput = isset($_POST['admin_menu_bg_color']) ? trim($_POST['admin_menu_bg_color']) : '';
            $adminMenuOpacityInput = isset($_POST['admin_menu_bg_opacity']) ? trim($_POST['admin_menu_bg_opacity']) : '';

            $adminMenuColorNormalized = normalize_admin_menu_color($adminMenuColorInput, $menuRgb);
            $adminMenuOpacityNormalized = normalize_admin_menu_opacity($adminMenuOpacityInput);

            if ($adminMenuColorNormalized === '') {
                $menuError = 'Barva pozadí musí být ve formátu HEX (#rrggbb) nebo rgb(r,g,b).';
            } elseif ($adminMenuOpacityNormalized === '') {
                $menuError = 'Průhlednost musí být číslo 0–1 nebo 0–100.';
            } else {
                set_setting('admin_menu_bg_enabled', $adminMenuEnabled);
                set_setting('admin_menu_bg_color', $adminMenuColorNormalized);
                set_setting('admin_menu_bg_opacity', $adminMenuOpacityNormalized);
                $menuMessage = 'Nastavení pozadí menu bylo uloženo.';
            }

            $adminMenuEnabled = $adminMenuEnabled === '1';
            $adminMenuColor = $adminMenuColorNormalized !== '' ? $adminMenuColorNormalized : $adminMenuColorInput;
            $adminMenuOpacity = $adminMenuOpacityNormalized !== '' ? $adminMenuOpacityNormalized : $adminMenuOpacityInput;
        } elseif ($action === 'save_site_font_family') {
            $siteFontFamilyInput = isset($_POST['site_font_family']) ? trim($_POST['site_font_family']) : '';
            $siteFontFamilyNormalized = $siteFontFamilyInput === '' ? get_site_font_family_default() : normalize_site_font_family($siteFontFamilyInput);

            if ($siteFontFamilyInput !== '' && $siteFontFamilyNormalized === '') {
                $fontError = 'Font musí obsahovat pouze písmena, čísla, mezery, čárky nebo uvozovky.';
                $siteFontFamily = $siteFontFamilyInput;
            } else {
                set_setting('site_font_family', $siteFontFamilyNormalized);
                $siteFontFamily = $siteFontFamilyNormalized;
                $fontMessage = $siteFontFamilyInput === '' ? 'Font byl obnoven na výchozí hodnotu.' : 'Font byl úspěšně uložen.';
            }
        } elseif ($action === 'save_site_logo') {
            $siteLogoTypeInput = isset($_POST['site_logo_type']) ? trim($_POST['site_logo_type']) : '';
            $siteLogoTextInput = isset($_POST['site_logo_text']) ? trim($_POST['site_logo_text']) : '';
            $allowedLogoTypes = array('image', 'text');

            if (!in_array($siteLogoTypeInput, $allowedLogoTypes, true)) {
                $logoError = 'Vyberte typ loga (obrázek nebo text).';
            } elseif (strlen($siteLogoTextInput) > 120) {
                $logoError = 'Text loga může mít maximálně 120 znaků.';
            } else {
                set_setting('site_logo_type', $siteLogoTypeInput);
                set_setting('site_logo_text', $siteLogoTextInput);
                $siteLogoType = $siteLogoTypeInput;
                $siteLogoText = $siteLogoTextInput;

                if ($siteLogoTypeInput === 'image') {
                    $uploadedLogo = handle_image_upload('site_logo_file', 'logo');
                    if ($uploadedLogo !== null && $uploadedLogo !== '') {
                        set_setting('site_logo_path', $uploadedLogo);
                        $siteLogoPath = $uploadedLogo;
                    }
                }

                $logoMessage = 'Logo bylo uloženo.';
            }
        } elseif ($action === 'remove_site_logo') {
            set_setting('site_logo_path', '');
            set_setting('site_logo_type', '');
            set_setting('site_logo_text', '');
            $siteLogoPath = '';
            $siteLogoType = '';
            $siteLogoText = '';
            $logoMessage = 'Logo bylo odstraněno.';
        } elseif ($action === 'save_brand_settings') {
            $brandTypeInput = isset($_POST['brand_type']) ? trim($_POST['brand_type']) : '';
            $brandValueInput = isset($_POST['brand_value']) ? trim($_POST['brand_value']) : '';
            $allowedBrandTypes = array('text', 'image', 'svg');

            if (!in_array($brandTypeInput, $allowedBrandTypes, true)) {
                $brandError = 'Vyberte typ brandu (text, image nebo svg).';
            } else {
                set_setting('brand_type', $brandTypeInput);
                set_setting('brand_value', $brandValueInput);
                $brandType = $brandTypeInput;
                $brandValue = $brandValueInput;
                if ($brandValueInput === '') {
                    $brandMessage = 'Brand byl uložen. Prázdná hodnota použije fallback nastavení.';
                } else {
                    $brandMessage = 'Brand byl uložen.';
                }
            }
        } elseif ($action === 'save_brand_position') {
            $brandLeftDesktopInput = isset($_POST['brand_left_desktop']) ? trim($_POST['brand_left_desktop']) : '';
            $brandLeftMobileInput = isset($_POST['brand_left_mobile']) ? trim($_POST['brand_left_mobile']) : '';
            $brandLeftDesktopNormalized = normalize_brand_left_input($brandLeftDesktopInput);
            $brandLeftMobileNormalized = normalize_brand_left_input($brandLeftMobileInput);

            if ($brandLeftDesktopNormalized === null || $brandLeftMobileNormalized === null) {
                $brandError = 'Pozice brandu musí být číslo 0–100 (případně s %).';
                $brandLeftDesktopDisplay = $brandLeftDesktopInput;
                $brandLeftMobileDisplay = $brandLeftMobileInput;
            } else {
                set_setting('brand_left_desktop', $brandLeftDesktopNormalized);
                set_setting('brand_left_mobile', $brandLeftMobileNormalized);
                $brandLeftDesktopDisplay = $brandLeftDesktopNormalized;
                $brandLeftMobileDisplay = $brandLeftMobileNormalized;
                $brandMessage = 'Pozice brandu byla uložena.';
            }

        } elseif ($action === 'remove_brand_settings') {
            set_setting('brand_type', '');
            set_setting('brand_value', '');
            $brandType = $brandDefaults['type'];
            $brandValue = $brandDefaults['value'];
            $brandMessage = 'Brand byl odstraněn. Použijí se výchozí hodnoty.';
        }
    }
} catch (RuntimeException $e) {
    error_log('Admin settings DB failed: ' . $e->getMessage());
    $pinError = $e->getMessage();
    $festivalError = $e->getMessage();
    $menuError = $e->getMessage();
    $fontError = $e->getMessage();
    $logoError = $e->getMessage();
    $brandError = $e->getMessage();
} catch (Exception $e) {
    error_log('Admin settings init failed: ' . $e->getMessage());
    $pinError = 'Nepodařilo se načíst nastavení. Zkuste to prosím znovu.';
    $festivalError = 'Nepodařilo se načíst nastavení. Zkuste to prosím znovu.';
    $menuError = 'Nepodařilo se načíst nastavení. Zkuste to prosím znovu.';
    $fontError = 'Nepodařilo se načíst nastavení. Zkuste to prosím znovu.';
    $logoError = 'Nepodařilo se načíst nastavení. Zkuste to prosím znovu.';
    $brandError = 'Nepodařilo se načíst nastavení. Zkuste to prosím znovu.';
}

$adminPageTitle = 'Nastavení';
require_once __DIR__ . '/partials/header.php';
?>
<section class="admin-shell">
    <section class="admin-card">
        <h1>Nastavení PIN</h1>
        <?php if ($pinMessage): ?><p class="admin-alert admin-alert--success"><?php echo h($pinMessage); ?></p><?php endif; ?>
        <?php if ($pinError): ?><p class="admin-alert admin-alert--error"><?php echo h($pinError); ?></p><?php endif; ?>

        <form class="admin-form" method="post">
            <input type="hidden" name="action" value="change_pin">
            <label>Aktuální PIN<input type="password" name="current_pin" required></label>
            <label>Nový PIN<input type="password" name="new_pin" required></label>
            <label>Potvrdit nový PIN<input type="password" name="confirm_pin" required></label>
            <button class="admin-button" type="submit">Změnit PIN</button>
        </form>
    </section>

    <section class="admin-card">
        <h1>Text O festivalu</h1>
        <?php if ($festivalMessage): ?><p class="admin-alert admin-alert--success"><?php echo h($festivalMessage); ?></p><?php endif; ?>
        <?php if ($festivalError): ?><p class="admin-alert admin-alert--error"><?php echo h($festivalError); ?></p><?php endif; ?>

        <form class="admin-form" method="post">
            <input type="hidden" name="action" value="save_festival_text">
            <label>Obsah stránky O festivalu
                <textarea name="festival_text" rows="14" required><?php echo h($festivalText); ?></textarea>
            </label>
            <button class="admin-button" type="submit">Uložit text</button>
        </form>
    </section>

    <section class="admin-card">
        <h1>Pozadí menu administrace</h1>
        <?php if ($menuMessage): ?><p class="admin-alert admin-alert--success"><?php echo h($menuMessage); ?></p><?php endif; ?>
        <?php if ($menuError): ?><p class="admin-alert admin-alert--error"><?php echo h($menuError); ?></p><?php endif; ?>

        <form class="admin-form" method="post">
            <input type="hidden" name="action" value="save_admin_menu">
            <label class="admin-field">
                <span>Povolit vlastní pozadí menu</span>
                <input type="checkbox" name="admin_menu_bg_enabled" value="1" <?php echo $adminMenuEnabled ? 'checked' : ''; ?>>
            </label>
            <label class="admin-field">
                <span>Barva pozadí (HEX nebo rgb)</span>
                <input type="text" name="admin_menu_bg_color" value="<?php echo h($adminMenuColor); ?>" required>
                <span class="admin-help">Příklad: #160c23 nebo rgb(22,12,35)</span>
            </label>
            <label class="admin-field">
                <span>Průhlednost (0–1 nebo 0–100)</span>
                <input type="text" name="admin_menu_bg_opacity" value="<?php echo h($adminMenuOpacity); ?>" required>
                <span class="admin-help">Příklad: 0.95 nebo 95</span>
            </label>
            <button class="admin-button" type="submit">Uložit nastavení menu</button>
        </form>
    </section>

    <section class="admin-card">
        <h1>Font webu</h1>
        <?php if ($fontMessage): ?><p class="admin-alert admin-alert--success"><?php echo h($fontMessage); ?></p><?php endif; ?>
        <?php if ($fontError): ?><p class="admin-alert admin-alert--error"><?php echo h($fontError); ?></p><?php endif; ?>

        <form class="admin-form" method="post">
            <input type="hidden" name="action" value="save_site_font_family">
            <label class="admin-field">
                <span>Font-family</span>
                <input type="text" name="site_font_family" value="<?php echo h($siteFontFamily); ?>">
                <span class="admin-help">Příklad: "Times New Roman", serif</span>
            </label>
            <button class="admin-button" type="submit">Uložit font</button>
        </form>
    </section>

    <section class="admin-card">
        <h1>Logo webu</h1>
        <?php if ($logoMessage): ?><p class="admin-alert admin-alert--success"><?php echo h($logoMessage); ?></p><?php endif; ?>
        <?php if ($logoError): ?><p class="admin-alert admin-alert--error"><?php echo h($logoError); ?></p><?php endif; ?>

        <form class="admin-form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="save_site_logo">
            <?php if ($siteLogoPath !== ''): ?>
                <div class="admin-field">
                    <span>Aktuální logo</span>
                    <img class="admin-logo-preview" src="<?php echo h($siteLogoPath); ?>" alt="Aktuální logo webu">
                </div>
            <?php endif; ?>
            <label class="admin-field">
                <span>Typ loga</span>
                <select name="site_logo_type" required>
                    <option value="image"<?php echo $siteLogoType === 'image' || $siteLogoType === '' ? ' selected' : ''; ?>>Obrázek</option>
                    <option value="text"<?php echo $siteLogoType === 'text' ? ' selected' : ''; ?>>Text</option>
                </select>
            </label>
            <label class="admin-field">
                <span>Text loga</span>
                <input type="text" name="site_logo_text" value="<?php echo h($siteLogoText); ?>" maxlength="120">
                <span class="admin-help">Použije se jen pro textové logo. Maximum 120 znaků.</span>
            </label>
            <label class="admin-field">
                <span>Nahrát nové logo (PNG, JPG, WebP)</span>
                <input type="file" name="site_logo_file" accept=".png,.jpg,.jpeg,.webp,image/png,image/jpeg,image/webp">
                <span class="admin-help">Doporučená výška loga do 60 px.</span>
            </label>
            <div class="admin-field">
                <button class="admin-button" type="submit">Uložit logo</button>
                <button class="admin-button admin-button--secondary" type="submit" name="action" value="remove_site_logo">Odstranit logo</button>
            </div>
        </form>
    </section>

    <section class="admin-card">
        <h1>Brand webu</h1>
        <?php if ($brandMessage): ?><p class="admin-alert admin-alert--success"><?php echo h($brandMessage); ?></p><?php endif; ?>
        <?php if ($brandError): ?><p class="admin-alert admin-alert--error"><?php echo h($brandError); ?></p><?php endif; ?>

        <form class="admin-form" method="post">
            <label class="admin-field">
                <span>Typ brandu</span>
                <select name="brand_type" required>
                    <option value="text"<?php echo $brandType === 'text' ? ' selected' : ''; ?>>Text</option>
                    <option value="image"<?php echo $brandType === 'image' ? ' selected' : ''; ?>>Image</option>
                    <option value="svg"<?php echo $brandType === 'svg' ? ' selected' : ''; ?>>SVG</option>
                </select>
            </label>
            <label class="admin-field">
                <span>Hodnota brandu</span>
                <input type="text" name="brand_value" value="<?php echo h($brandValue); ?>">
                <span class="admin-help">Text: název značky, Image: URL/relativní cesta, SVG: "inline" nebo cesta k SVG souboru.</span>
            </label>
            <label class="admin-field">
                <span>Posun vlevo (Desktop)</span>
                <input type="text" name="brand_left_desktop" value="<?php echo h($brandLeftDesktopDisplay); ?>">
                <span class="admin-help">Zadejte číslo 0–100 (uloží se jako procento, např. 10 → 10%). Nechte prázdné pro střed.</span>
            </label>
            <label class="admin-field">
                <span>Posun vlevo (Mobil)</span>
                <input type="text" name="brand_left_mobile" value="<?php echo h($brandLeftMobileDisplay); ?>">
                <span class="admin-help">Zadejte číslo 0–100 (uloží se jako procento, např. 6 → 6%). Nechte prázdné pro střed.</span>
            </label>
            <div class="admin-field">
                <button class="admin-button" type="submit" name="action" value="save_brand_settings">Uložit brand</button>
                <button class="admin-button" type="submit" name="action" value="save_brand_position">Uložit pozici brandu</button>
                <button class="admin-button admin-button--secondary" type="submit" name="action" value="remove_brand_settings">Odstranit brand</button>
            </div>
        </form>
    </section>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
