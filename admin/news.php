<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/upload.php';
require_admin_login();

$db = get_db();
$message = '';
$error = '';
$editRow = null;

if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    if ($editId > 0) {
        $editStmt = $db->prepare('SELECT * FROM news WHERE id = :id');
        $editStmt->execute(array(':id' => $editId));
        $editRow = $editStmt->fetch();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $body = isset($_POST['body']) ? trim($_POST['body']) : '';
    $publishedAt = isset($_POST['published_at']) ? trim($_POST['published_at']) : '';
    $sortOrder = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 0;

    try {
        if ($title === '') {
            throw new RuntimeException('Nadpis je povinný.');
        }

        $existingImage = '';
        if ($id > 0) {
            $existingStmt = $db->prepare('SELECT image FROM news WHERE id = :id');
            $existingStmt->execute(array(':id' => $id));
            $existingImage = (string) $existingStmt->fetchColumn();
        }

        $uploadedImage = handle_image_upload('image_file', 'news');
        $image = $uploadedImage !== null ? $uploadedImage : $existingImage;

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE news SET title = :title, body = :body, image = :image, published_at = :published_at, sort_order = :sort_order WHERE id = :id');
            $stmt->execute(array(
                ':title' => $title,
                ':body' => $body,
                ':image' => $image,
                ':published_at' => $publishedAt,
                ':sort_order' => $sortOrder,
                ':id' => $id
            ));
            $message = 'Aktualita upravena.';
        } else {
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

        $editRow = null;
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
    }
}

$rows = $db->query('SELECT id, title, image, published_at, sort_order FROM news ORDER BY sort_order ASC, id DESC')->fetchAll();
?>
<!doctype html>
<html lang="cs">
<head><meta charset="utf-8"><title>Aktuality</title></head>
<body>
<p><a href="dashboard.php">← Zpět na dashboard</a></p>
<h1>Aktuality</h1>
<?php if ($message): ?><p style="color:green;"><?php echo h($message); ?></p><?php endif; ?>
<?php if ($error): ?><p style="color:red;"><?php echo h($error); ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $editRow ? (int) $editRow['id'] : 0; ?>">
    <label>Nadpis<br><input type="text" name="title" required value="<?php echo h($editRow ? $editRow['title'] : ''); ?>"></label><br>
    <label>Text<br><textarea name="body"><?php echo h($editRow ? $editRow['body'] : ''); ?></textarea></label><br>
    <?php if ($editRow && !empty($editRow['image'])): ?>
        <p>Současný obrázek: <code><?php echo h($editRow['image']); ?></code></p>
    <?php endif; ?>
    <label>Obrázek (JPG/PNG/WEBP, max 5 MB)<br><input type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></label><br>
    <label>Publikováno (YYYY-MM-DD HH:MM)<br><input type="text" name="published_at" value="<?php echo h($editRow ? $editRow['published_at'] : ''); ?>"></label><br>
    <label>Pořadí<br><input type="number" name="sort_order" value="<?php echo $editRow ? (int) $editRow['sort_order'] : 0; ?>"></label><br>
    <button type="submit"><?php echo $editRow ? 'Upravit aktualitu' : 'Uložit aktualitu'; ?></button>
</form>
<h2>Seznam</h2>
<ul>
<?php foreach ($rows as $row): ?>
    <li>#<?php echo (int) $row['id']; ?> | <?php echo h($row['title']); ?> | <?php echo h($row['published_at']); ?> | obrázek <?php echo h($row['image']); ?> | pořadí <?php echo (int) $row['sort_order']; ?> | <a href="news.php?edit=<?php echo (int) $row['id']; ?>">Upravit</a></li>
<?php endforeach; ?>
</ul>
</body>
</html>
