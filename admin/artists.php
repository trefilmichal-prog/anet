<?php
require_once __DIR__ . '/auth.php';
require_admin_login();

$db = get_db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';
    $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';
    $sortOrder = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 0;

    if ($name !== '') {
        $stmt = $db->prepare('INSERT INTO artists(name, role, bio, image, sort_order) VALUES(:name, :role, :bio, :image, :sort_order)');
        $stmt->execute(array(
            ':name' => $name,
            ':role' => $role,
            ':bio' => $bio,
            ':image' => $image,
            ':sort_order' => $sortOrder
        ));
        $message = 'Umělec uložen.';
    }
}

$rows = $db->query('SELECT id, name, role, sort_order FROM artists ORDER BY sort_order ASC, id DESC')->fetchAll();
?>
<!doctype html>
<html lang="cs">
<head><meta charset="utf-8"><title>Umělci</title></head>
<body>
<p><a href="dashboard.php">← Zpět na dashboard</a></p>
<h1>Umělci</h1>
<?php if ($message): ?><p style="color:green;"><?php echo h($message); ?></p><?php endif; ?>
<form method="post">
    <label>Jméno<br><input type="text" name="name" required></label><br>
    <label>Role<br><input type="text" name="role"></label><br>
    <label>Bio<br><textarea name="bio"></textarea></label><br>
    <label>Obrázek (cesta/URL)<br><input type="text" name="image"></label><br>
    <label>Pořadí<br><input type="number" name="sort_order" value="0"></label><br>
    <button type="submit">Uložit umělce</button>
</form>
<h2>Seznam</h2>
<ul>
<?php foreach ($rows as $row): ?>
    <li>#<?php echo (int) $row['id']; ?> | <?php echo h($row['name']); ?> | <?php echo h($row['role']); ?> | pořadí <?php echo (int) $row['sort_order']; ?></li>
<?php endforeach; ?>
</ul>
</body>
</html>
