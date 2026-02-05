<?php
require_once __DIR__ . '/auth.php';
require_admin_login();
?>
<!doctype html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Dashboard administrace</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <main class="admin-shell">
        <section class="admin-card">
            <h1>Dashboard</h1>
            <ul class="admin-nav">
                <li><a href="backgrounds.php">Pozadí</a></li>
                <li><a href="news.php">Aktuality</a></li>
                <li><a href="program.php">Program</a></li>
                <li><a href="artists.php">Umělci</a></li>
                <li><a href="settings.php">Nastavení PIN</a></li>
                <li><a href="logout.php">Odhlásit se</a></li>
            </ul>
        </section>
    </main>
</body>
</html>
