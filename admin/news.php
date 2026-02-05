<?php
require_once __DIR__ . '/auth.php';
require_admin_login();

$db = get_db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $body = isset($_POST['body']) ? trim($_POST['body']) : '';
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';
    $publishedAt = isset($_POST['published_at']) ? trim($_POST['published_at']) : '';
    $sortOrder = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 0;

    if ($title !== '') {
        $stmt = $db->prepare('INSERT INTO news(title, body, image, published_at, sort_order) VALUES(:title, :body, :image, :published_at, :sort_order)');
        $stmt->execute(array(
            ':title' => $title,
            ':body' => $body,
            ':image' => $image,
            ':published_at' => $publishedAt,
            ':sort_order' => $sortOrder
        ));
        $message = 'Aktualita uložena.';
    }
}

$rows = $db->query('SELECT id, title, published_at, sort_order FROM news ORDER BY sort_order ASC, id DESC')->fetchAll();
?>
<!doctype html>
<html lang="cs">
<head><meta charset="utf-8"><title>Aktuality</title></head>
<body>
<p><a href="dashboard.php">← Zpět na dashboard</a></p>
<h1>Aktuality</h1>
<?php if ($message): ?><p style="color:green;"><?php echo h($message); ?></p><?php endif; ?>
<form method="post">
    <label>Nadpis<br><input type="text" name="title" required></label><br>
    <label>Text<br><textarea name="body"></textarea></label><br>
    <label>Obrázek (cesta/URL)<br><input type="text" name="image"></label><br>
    <label>Publikováno (YYYY-MM-DD HH:MM)<br><input type="text" name="published_at"></label><br>
    <label>Pořadí<br><input type="number" name="sort_order" value="0"></label><br>
    <button type="submit">Uložit aktualitu</button>
</form>
<h2>Seznam</h2>
<ul>
<?php foreach ($rows as $row): ?>
    <li>#<?php echo (int) $row['id']; ?> | <?php echo h($row['title']); ?> | <?php echo h($row['published_at']); ?> | pořadí <?php echo (int) $row['sort_order']; ?></li>
<?php endforeach; ?>
</ul>
</body>
</html>
