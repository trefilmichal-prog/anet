<?php
require_once __DIR__ . '/auth.php';
require_admin_login();

$db = get_db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pageKey = isset($_POST['page_key']) ? trim($_POST['page_key']) : '';
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';

    if ($pageKey !== '' && $image !== '') {
        $stmt = $db->prepare('INSERT INTO backgrounds(page_key, image, updated_at) VALUES(:page_key, :image, :updated_at)');
        $stmt->execute(array(
            ':page_key' => $pageKey,
            ':image' => $image,
            ':updated_at' => date('c')
        ));
        $message = 'Pozadí uloženo.';
    }
}

$rows = $db->query('SELECT id, page_key, image, updated_at FROM backgrounds ORDER BY id DESC')->fetchAll();
?>
<!doctype html>
<html lang="cs">
<head><meta charset="utf-8"><title>Pozadí</title></head>
<body>
<p><a href="dashboard.php">← Zpět na dashboard</a></p>
<h1>Pozadí</h1>
<?php if ($message): ?><p style="color:green;"><?php echo h($message); ?></p><?php endif; ?>
<form method="post">
    <label>Klíč stránky<br><input type="text" name="page_key" required></label><br>
    <label>Cesta/URL obrázku<br><input type="text" name="image" required></label><br>
    <button type="submit">Uložit pozadí</button>
</form>
<h2>Seznam</h2>
<ul>
<?php foreach ($rows as $row): ?>
    <li>#<?php echo (int) $row['id']; ?> | <?php echo h($row['page_key']); ?> | <?php echo h($row['image']); ?> | <?php echo h($row['updated_at']); ?></li>
<?php endforeach; ?>
</ul>
</body>
</html>
