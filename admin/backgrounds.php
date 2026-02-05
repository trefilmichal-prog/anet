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
        $editStmt = $db->prepare('SELECT * FROM backgrounds WHERE id = :id');
        $editStmt->execute(array(':id' => $editId));
        $editRow = $editStmt->fetch();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $pageKey = isset($_POST['page_key']) ? trim($_POST['page_key']) : '';

    try {
        if ($pageKey === '') {
            throw new RuntimeException('Klíč stránky je povinný.');
        }

        $existingImage = '';
        if ($id > 0) {
            $existingStmt = $db->prepare('SELECT image FROM backgrounds WHERE id = :id');
            $existingStmt->execute(array(':id' => $id));
            $existingImage = (string) $existingStmt->fetchColumn();
        }

        $uploadedImage = handle_image_upload('image_file', 'backgrounds');
        $image = $uploadedImage !== null ? $uploadedImage : $existingImage;

        if ($image === '') {
            throw new RuntimeException('Obrázek je povinný.');
        }

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE backgrounds SET page_key = :page_key, image = :image, updated_at = :updated_at WHERE id = :id');
            $stmt->execute(array(
                ':page_key' => $pageKey,
                ':image' => $image,
                ':updated_at' => date('c'),
                ':id' => $id
            ));
            $message = 'Pozadí upraveno.';
        } else {
            $stmt = $db->prepare('INSERT INTO backgrounds(page_key, image, updated_at) VALUES(:page_key, :image, :updated_at)');
            $stmt->execute(array(
                ':page_key' => $pageKey,
                ':image' => $image,
                ':updated_at' => date('c')
            ));
            $message = 'Pozadí uloženo.';
        }

        $editRow = null;
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
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
<?php if ($error): ?><p style="color:red;"><?php echo h($error); ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $editRow ? (int) $editRow['id'] : 0; ?>">
    <label>Klíč stránky<br><input type="text" name="page_key" required value="<?php echo h($editRow ? $editRow['page_key'] : ''); ?>"></label><br>
    <?php if ($editRow && !empty($editRow['image'])): ?>
        <p>Současný obrázek: <code><?php echo h($editRow['image']); ?></code></p>
    <?php endif; ?>
    <label>Obrázek (JPG/PNG/WEBP, max 5 MB)<br><input type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></label><br>
    <button type="submit"><?php echo $editRow ? 'Upravit pozadí' : 'Uložit pozadí'; ?></button>
</form>
<h2>Seznam</h2>
<ul>
<?php foreach ($rows as $row): ?>
    <li>#<?php echo (int) $row['id']; ?> | <?php echo h($row['page_key']); ?> | <?php echo h($row['image']); ?> | <?php echo h($row['updated_at']); ?> | <a href="backgrounds.php?edit=<?php echo (int) $row['id']; ?>">Upravit</a></li>
<?php endforeach; ?>
</ul>
</body>
</html>
