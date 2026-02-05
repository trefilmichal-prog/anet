<?php
require_once __DIR__ . '/auth.php';
require_admin_login();

$adminPageTitle = 'Dashboard administrace';
require_once __DIR__ . '/partials/header.php';
?>

<section class="admin-shell">
    <section class="admin-card">
        <h1>Dashboard</h1>
        <p>Vyberte sekci v horní navigaci.</p>
    </section>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
