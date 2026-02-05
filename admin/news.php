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
$adminPageTitle = 'Aktuality';
require_once __DIR__ . '/partials/header.php';
?>
<section class="admin-shell">
    <section class="admin-card">
        <h1>Aktuality</h1>
        <?php if ($message): ?><p class="admin-alert admin-alert--success"><?php echo h($message); ?></p><?php endif; ?>
        <?php if ($error): ?><p class="admin-alert admin-alert--error"><?php echo h($error); ?></p><?php endif; ?>

        <form class="admin-form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $editRow ? (int) $editRow['id'] : 0; ?>">
            <label>Nadpis<input type="text" name="title" required value="<?php echo h($editRow ? $editRow['title'] : ''); ?>"></label>
            <label>Text<textarea name="body"><?php echo h($editRow ? $editRow['body'] : ''); ?></textarea></label>
            <?php if ($editRow && !empty($editRow['image'])): ?>
                <p class="admin-muted">Současný obrázek: <code><?php echo h($editRow['image']); ?></code></p>
            <?php endif; ?>
            <label>Obrázek (JPG/PNG/WEBP, max 5 MB)<input type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></label>
            <label>Publikováno (YYYY-MM-DD HH:MM)<input type="text" name="published_at" value="<?php echo h($editRow ? $editRow['published_at'] : ''); ?>"></label>
            <label>Pořadí<input type="number" name="sort_order" value="<?php echo $editRow ? (int) $editRow['sort_order'] : 0; ?>"></label>
            <button class="admin-button" type="submit"><?php echo $editRow ? 'Upravit aktualitu' : 'Uložit aktualitu'; ?></button>
        </form>
    </section>

    <section class="admin-card">
        <h2>Seznam</h2>
        <table class="admin-table">
            <thead><tr><th>ID</th><th>Nadpis</th><th>Publikováno</th><th>Obrázek</th><th>Pořadí</th><th>Akce</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td>#<?php echo (int) $row['id']; ?></td>
                    <td><?php echo h($row['title']); ?></td>
                    <td><?php echo h($row['published_at']); ?></td>
                    <td><?php echo h($row['image']); ?></td>
                    <td><?php echo (int) $row['sort_order']; ?></td>
                    <td><a href="news.php?edit=<?php echo (int) $row['id']; ?>">Upravit</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
