<?php
$adminPageTitle = isset($adminPageTitle) ? (string) $adminPageTitle : 'Administrace';
$adminShowNavigation = isset($adminShowNavigation) ? (bool) $adminShowNavigation : true;
$currentScript = basename(isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '');
$adminNavItems = array(
    'dashboard.php' => 'Dashboard',
    'backgrounds.php' => 'Pozadí',
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
<body>
<header class="admin-header">
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
