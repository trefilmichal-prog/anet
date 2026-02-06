<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/settings_repository.php';
require_once dirname(__DIR__) . '/includes/festival_content.php';

require_admin_login();

$pinMessage = '';
$pinError = '';
$festivalMessage = '';
$festivalError = '';
$festivalText = get_setting('festival_page_text', get_default_festival_page_text());

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
        }
    }
} catch (RuntimeException $e) {
    error_log('Admin settings DB failed: ' . $e->getMessage());
    $pinError = $e->getMessage();
    $festivalError = $e->getMessage();
} catch (Exception $e) {
    error_log('Admin settings init failed: ' . $e->getMessage());
    $pinError = 'Nepodařilo se načíst nastavení. Zkuste to prosím znovu.';
    $festivalError = 'Nepodařilo se načíst nastavení. Zkuste to prosím znovu.';
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
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
