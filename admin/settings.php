<?php
require_once __DIR__ . '/auth.php';
require_admin_login();

$message = '';
$error = '';

try {
    $db = get_db();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPin = isset($_POST['current_pin']) ? trim($_POST['current_pin']) : '';
    $newPin = isset($_POST['new_pin']) ? trim($_POST['new_pin']) : '';
    $confirmPin = isset($_POST['confirm_pin']) ? trim($_POST['confirm_pin']) : '';

    $stmt = $db->prepare('SELECT value FROM settings WHERE key = :key');
    $stmt->execute(array(':key' => 'admin_pin_hash'));
    $pinHash = $stmt->fetchColumn();

    if (!$pinHash || !password_verify($currentPin, $pinHash)) {
        $error = 'Aktuální PIN není správný.';
    } elseif ($newPin === '' || strlen($newPin) < 4) {
        $error = 'Nový PIN musí mít alespoň 4 znaky.';
    } elseif ($newPin !== $confirmPin) {
        $error = 'Nový PIN a potvrzení se neshodují.';
    } else {
        $newHash = password_hash($newPin, PASSWORD_DEFAULT);
        $update = $db->prepare('UPDATE settings SET value = :value WHERE key = :key');
        $update->execute(array(
            ':value' => $newHash,
            ':key' => 'admin_pin_hash'
        ));
        $message = 'PIN byl úspěšně změněn.';
    }
    }
} catch (RuntimeException $e) {
    error_log('Admin settings DB failed: ' . $e->getMessage());
    $error = $e->getMessage();
} catch (Exception $e) {
    error_log('Admin settings init failed: ' . $e->getMessage());
    $error = 'Nepodařilo se načíst nastavení. Zkuste to prosím znovu.';
}

$adminPageTitle = 'Nastavení PIN';
require_once __DIR__ . '/partials/header.php';
?>
<section class="admin-shell">
    <section class="admin-card">
        <h1>Nastavení PIN</h1>
        <?php if ($message): ?><p class="admin-alert admin-alert--success"><?php echo h($message); ?></p><?php endif; ?>
        <?php if ($error): ?><p class="admin-alert admin-alert--error"><?php echo h($error); ?></p><?php endif; ?>

        <form class="admin-form" method="post">
            <label>Aktuální PIN<input type="password" name="current_pin" required></label>
            <label>Nový PIN<input type="password" name="new_pin" required></label>
            <label>Potvrdit nový PIN<input type="password" name="confirm_pin" required></label>
            <button class="admin-button" type="submit">Změnit PIN</button>
        </form>
    </section>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
