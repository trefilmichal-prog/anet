<?php
require_once __DIR__ . '/auth.php';

if (admin_is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin = isset($_POST['pin']) ? trim($_POST['pin']) : '';

    $db = get_db();
    $stmt = $db->prepare('SELECT value FROM settings WHERE key = :key');
    $stmt->execute(array(':key' => 'admin_pin_hash'));
    $pinHash = $stmt->fetchColumn();

    if ($pinHash && password_verify($pin, $pinHash)) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Neplatný PIN.';
}
?>
<!doctype html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Admin přihlášení</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <main class="admin-shell">
        <section class="admin-card">
            <h1>Administrace</h1>
            <h2>Přihlášení PINem</h2>

            <?php if ($error): ?>
                <p class="admin-alert admin-alert--error"><?php echo h($error); ?></p>
            <?php endif; ?>

            <form class="admin-form" method="post" action="">
                <label for="pin">PIN
                    <input type="password" name="pin" id="pin" required>
                </label>
                <button class="admin-button" type="submit">Přihlásit</button>
            </form>
        </section>
    </main>
</body>
</html>
