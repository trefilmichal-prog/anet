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
        $editStmt = $db->prepare('SELECT * FROM artists WHERE id = :id');
        $editStmt->execute(array(':id' => $editId));
        $editRow = $editStmt->fetch();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';
    $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
    $sortOrder = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 0;

    try {
        if ($name === '') {
            throw new RuntimeException('Jméno je povinné.');
        }

        $existingImage = '';
        if ($id > 0) {
            $existingStmt = $db->prepare('SELECT image FROM artists WHERE id = :id');
            $existingStmt->execute(array(':id' => $id));
            $existingImage = (string) $existingStmt->fetchColumn();
        }

        $uploadedImage = handle_image_upload('image_file', 'artists');
        $image = $uploadedImage !== null ? $uploadedImage : $existingImage;

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE artists SET name = :name, role = :role, bio = :bio, image = :image, sort_order = :sort_order WHERE id = :id');
            $stmt->execute(array(
                ':name' => $name,
                ':role' => $role,
                ':bio' => $bio,
                ':image' => $image,
                ':sort_order' => $sortOrder,
                ':id' => $id
            ));
            $message = 'Umělec upraven.';
        } else {
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

        $editRow = null;
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
    }
}

$rows = $db->query('SELECT id, name, role, image, sort_order FROM artists ORDER BY sort_order ASC, id DESC')->fetchAll();
?>
<!doctype html>
<html lang="cs">
<head>
    <meta charset="utf-8">
    <title>Umělci</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<main class="admin-shell">
    <section class="admin-card">
        <ul class="admin-nav"><li><a href="dashboard.php">← Zpět na dashboard</a></li></ul>
        <h1>Umělci</h1>
        <?php if ($message): ?><p class="admin-alert admin-alert--success"><?php echo h($message); ?></p><?php endif; ?>
        <?php if ($error): ?><p class="admin-alert admin-alert--error"><?php echo h($error); ?></p><?php endif; ?>

        <form class="admin-form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $editRow ? (int) $editRow['id'] : 0; ?>">
            <label>Jméno<input type="text" name="name" required value="<?php echo h($editRow ? $editRow['name'] : ''); ?>"></label>
            <label>Role<input type="text" name="role" value="<?php echo h($editRow ? $editRow['role'] : ''); ?>"></label>
            <label>Bio<textarea name="bio"><?php echo h($editRow ? $editRow['bio'] : ''); ?></textarea></label>
            <?php if ($editRow && !empty($editRow['image'])): ?>
                <p class="admin-muted">Současný obrázek: <code><?php echo h($editRow['image']); ?></code></p>
            <?php endif; ?>
            <label>Obrázek (JPG/PNG/WEBP, max 5 MB)<input type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></label>
            <label>Pořadí<input type="number" name="sort_order" value="<?php echo $editRow ? (int) $editRow['sort_order'] : 0; ?>"></label>
            <button class="admin-button" type="submit"><?php echo $editRow ? 'Upravit umělce' : 'Uložit umělce'; ?></button>
        </form>
    </section>

    <section class="admin-card">
        <h2>Seznam</h2>
        <table class="admin-table">
            <thead><tr><th>ID</th><th>Jméno</th><th>Role</th><th>Obrázek</th><th>Pořadí</th><th>Akce</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td>#<?php echo (int) $row['id']; ?></td>
                    <td><?php echo h($row['name']); ?></td>
                    <td><?php echo h($row['role']); ?></td>
                    <td><?php echo h($row['image']); ?></td>
                    <td><?php echo (int) $row['sort_order']; ?></td>
                    <td><a href="artists.php?edit=<?php echo (int) $row['id']; ?>">Upravit</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>
</body>
</html>
