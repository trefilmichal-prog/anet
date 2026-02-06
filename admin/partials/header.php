<?php
require_once dirname(dirname(__DIR__)) . '/includes/settings_repository.php';

$adminPageTitle = isset($adminPageTitle) ? (string) $adminPageTitle : 'Administrace';
$adminShowNavigation = isset($adminShowNavigation) ? (bool) $adminShowNavigation : true;
$currentScript = basename(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '');
$menuDefaults = get_admin_menu_defaults();
$siteFontFamilyValue = get_setting('site_font_family', get_site_font_family_default());
$siteFontFamilyNormalized = normalize_site_font_family($siteFontFamilyValue);
if ($siteFontFamilyNormalized === '') {
    $siteFontFamilyNormalized = get_site_font_family_default();
}
$menuEnabledValue = get_setting('admin_menu_bg_enabled', $menuDefaults['admin_menu_bg_enabled']);
$menuEnabled = $menuEnabledValue === '1';
$menuColorValue = get_setting('admin_menu_bg_color', $menuDefaults['admin_menu_bg_color']);
$menuOpacityValue = get_setting('admin_menu_bg_opacity', $menuDefaults['admin_menu_bg_opacity']);
$menuRgb = null;
$menuColorNormalized = normalize_admin_menu_color($menuColorValue, $menuRgb);
if ($menuColorNormalized === '') {
    $menuColorNormalized = normalize_admin_menu_color($menuDefaults['admin_menu_bg_color'], $menuRgb);
}
$menuOpacityNormalized = normalize_admin_menu_opacity($menuOpacityValue);
if ($menuOpacityNormalized === '') {
    $menuOpacityNormalized = normalize_admin_menu_opacity($menuDefaults['admin_menu_bg_opacity']);
}
$adminHeaderStyle = '';
if ($menuEnabled && $menuRgb !== null && $menuOpacityNormalized !== '') {
    $menuRgba = 'rgba(' . $menuRgb['r'] . ', ' . $menuRgb['g'] . ', ' . $menuRgb['b'] . ', ' . $menuOpacityNormalized . ')';
    $adminHeaderStyle = ' style="--admin-menu-bg: ' . h($menuRgba) . ';"';
}
$adminHeaderClass = 'admin-header' . ($menuEnabled ? '' : ' admin-header--no-menu-bg');
$adminBodyStyle = ' style="--site-font-family: ' . h($siteFontFamilyNormalized) . ';"';
$adminNavItems = array(
    'dashboard.php' => 'Dashboard',
    'backgrounds.php' => 'Pozadí',
    'menu.php' => 'Menu',
    'news.php' => 'Aktuality',
    'program.php' => 'Program',
    'artists.php' => 'Umělci',
    'settings.php' => 'Nastavení'
);
?>
<!doctype html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title><?php echo h($adminPageTitle); ?></title>
    <link rel="stylesheet" href="admin.css">
</head>
<body<?php echo $adminBodyStyle; ?>>
<header class="<?php echo h($adminHeaderClass); ?>"<?php echo $adminHeaderStyle; ?>>
    <div class="admin-content admin-header__inner">
        <p class="admin-title">Administrace</p>
        <?php if ($adminShowNavigation): ?>
            <nav aria-label="Primární navigace administrace">
                <ul class="admin-nav admin-nav--primary">
                    <?php foreach ($adminNavItems as $file => $label): ?>
                        <li>
                            <a class="<?php echo $currentScript === $file ? 'is-active' : ''; ?>" href="<?php echo h($file); ?>"><?php echo h($label); ?></a>
                        </li>
                    <?php endforeach; ?>
                    <li><a href="logout.php">Odhlásit se</a></li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</header>
<main class="admin-main">
    <div class="admin-content">
