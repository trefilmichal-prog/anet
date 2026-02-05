<?php
require_once __DIR__ . '/auth.php';
require_admin_login();

$db = get_db();
$message = '';
$error = '';

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
?>
<!doctype html>
<html lang="cs">
<head><meta charset="utf-8"><title>Nastavení PIN</title></head>
<body>
<p><a href="dashboard.php">← Zpět na dashboard</a></p>
<h1>Nastavení PIN</h1>
<?php if ($message): ?><p style="color:green;"><?php echo h($message); ?></p><?php endif; ?>
<?php if ($error): ?><p style="color:red;"><?php echo h($error); ?></p><?php endif; ?>

<form method="post">
    <label>Aktuální PIN<br><input type="password" name="current_pin" required></label><br>
    <label>Nový PIN<br><input type="password" name="new_pin" required></label><br>
    <label>Potvrdit nový PIN<br><input type="password" name="confirm_pin" required></label><br>
    <button type="submit">Změnit PIN</button>
</form>
</body>
</html>
