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
    } catch (Exception $e) {
        $error = resolve_admin_form_error($e);
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

        <form class="admin-form admin-form--two-column" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $editRow ? (int) $editRow['id'] : 0; ?>">

            <div class="admin-field admin-field--full">
                <label for="news-title">Nadpis</label>
                <input id="news-title" type="text" name="title" required maxlength="180" value="<?php echo h($editRow ? $editRow['title'] : ''); ?>">
                <p class="admin-help">Krátký a výstižný nadpis aktuality.</p>
            </div>

            <div class="admin-field admin-field--full">
                <label for="news-body">Text</label>
                <textarea id="news-body" name="body"><?php echo h($editRow ? $editRow['body'] : ''); ?></textarea>
                <p class="admin-help">Volitelný detailní popis.</p>
            </div>

            <div class="admin-field">
                <label for="news-published-at">Publikováno</label>
                <input id="news-published-at" type="text" name="published_at" placeholder="YYYY-MM-DD HH:MM" pattern="\d{4}-\d{2}-\d{2}(\s\d{2}:\d{2})?" value="<?php echo h($editRow ? $editRow['published_at'] : ''); ?>">
                <p class="admin-help">Např. 2025-05-21 18:30.</p>
            </div>

            <div class="admin-field">
                <label for="news-sort-order">Pořadí</label>
                <input id="news-sort-order" type="number" name="sort_order" value="<?php echo $editRow ? (int) $editRow['sort_order'] : 0; ?>">
                <p class="admin-help">Nižší číslo se zobrazí dříve.</p>
            </div>

            <div class="admin-field admin-field--full">
                <label for="news-image-file">Obrázek</label>
                <input id="news-image-file" type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                <p class="admin-help">JPG/PNG/WEBP, max. 5 MB. <?php if ($editRow && !empty($editRow['image'])): ?>Současný: <code><?php echo h($editRow['image']); ?></code><?php endif; ?></p>
            </div>

            <div class="admin-form__actions">
                <button class="admin-button admin-button--primary" type="submit"><?php echo $editRow ? 'Upravit aktualitu' : 'Uložit aktualitu'; ?></button>
            </div>
        </form>
    </section>

    <section class="admin-card">
        <h2>Seznam</h2>
        <?php if (!$rows): ?>
            <p class="admin-empty-state">Zatím bez položek.</p>
        <?php else: ?>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead><tr><th>ID</th><th>Název</th><th>Datum/pořadí</th><th>Akce</th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td data-label="ID">#<?php echo (int) $row['id']; ?></td>
                            <td data-label="Název"><?php echo h($row['title']); ?></td>
                            <td data-label="Datum/pořadí"><?php echo h($row['published_at']); ?> / <?php echo (int) $row['sort_order']; ?></td>
                            <td data-label="Akce"><a class="admin-button admin-button--secondary" href="news.php?edit=<?php echo (int) $row['id']; ?>">Upravit</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
