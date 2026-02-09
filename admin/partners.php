<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/upload.php';
require_admin_login();

$message = '';
$error = '';
$editRow = null;
$rows = array();

try {
    $db = get_db();

    if (isset($_GET['edit'])) {
        $editId = (int) $_GET['edit'];
        if ($editId > 0) {
            $editStmt = $db->prepare('SELECT * FROM partners WHERE id = :id');
            $editStmt->execute(array(':id' => $editId));
            $editRow = $editStmt->fetch();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? trim($_POST['action']) : 'save';
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $url = isset($_POST['url']) ? trim($_POST['url']) : '';
        $sortOrder = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 0;

        try {
            if ($action === 'delete') {
                if ($id <= 0) {
                    throw new RuntimeException('Nepodařilo se určit položku ke smazání.');
                }

                $deleteStmt = $db->prepare('DELETE FROM partners WHERE id = :id');
                $deleteStmt->execute(array(':id' => $id));
                $message = 'Partner byl odstraněn.';
                $editRow = null;
            } else {
                if ($name === '') {
                    throw new RuntimeException('Název partnera je povinný.');
                }

                if ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) {
                    throw new RuntimeException('Odkaz musí být platná URL adresa.');
                }

                $existingImage = '';
                if ($id > 0) {
                    $existingStmt = $db->prepare('SELECT image FROM partners WHERE id = :id');
                    $existingStmt->execute(array(':id' => $id));
                    $existingImage = (string) $existingStmt->fetchColumn();
                }

                $uploadedImage = handle_image_upload('image_file', 'partners');
                $image = $uploadedImage !== null ? $uploadedImage : $existingImage;

                if ($image === '') {
                    throw new RuntimeException('Logo partnera je povinné.');
                }

                if ($id > 0) {
                    $stmt = $db->prepare('UPDATE partners SET name = :name, url = :url, image = :image, sort_order = :sort_order WHERE id = :id');
                    $stmt->execute(array(
                        ':name' => $name,
                        ':url' => $url,
                        ':image' => $image,
                        ':sort_order' => $sortOrder,
                        ':id' => $id
                    ));
                    $message = 'Partner upraven.';
                } else {
                    $stmt = $db->prepare('INSERT INTO partners(name, url, image, sort_order) VALUES(:name, :url, :image, :sort_order)');
                    $stmt->execute(array(
                        ':name' => $name,
                        ':url' => $url,
                        ':image' => $image,
                        ':sort_order' => $sortOrder
                    ));
                    $message = 'Partner uložen.';
                }

                $editRow = null;
            }
        } catch (Exception $e) {
            $error = resolve_admin_form_error($e);
        }
    }

    $rows = $db->query('SELECT id, name, url, image, sort_order FROM partners ORDER BY sort_order ASC, id DESC')->fetchAll();
} catch (RuntimeException $e) {
    error_log('Admin partners DB failed: ' . $e->getMessage());
    if ($error === '') {
        $error = $e->getMessage();
    }
    $rows = array();
} catch (Exception $e) {
    error_log('Admin partners init failed: ' . $e->getMessage());
    if ($error === '') {
        $error = 'Nepodařilo se načíst data. Zkuste to prosím znovu.';
    }
    $rows = array();
}
$adminPageTitle = 'Partneři';
require_once __DIR__ . '/partials/header.php';
?>
<section class="admin-shell">
    <section class="admin-card">
        <h1>Partneři</h1>
        <?php if ($message): ?><p class="admin-alert admin-alert--success"><?php echo h($message); ?></p><?php endif; ?>
        <?php if ($error): ?><p class="admin-alert admin-alert--error"><?php echo h($error); ?></p><?php endif; ?>

        <form class="admin-form admin-form--two-column" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $editRow ? (int) $editRow['id'] : 0; ?>">

            <div class="admin-field">
                <label for="partner-name">Název</label>
                <input id="partner-name" type="text" name="name" required maxlength="150" value="<?php echo h($editRow ? $editRow['name'] : ''); ?>">
                <p class="admin-help">Název partnera (slouží i jako popisek obrázku).</p>
            </div>

            <div class="admin-field">
                <label for="partner-url">Odkaz</label>
                <input id="partner-url" type="url" name="url" maxlength="255" value="<?php echo h($editRow ? $editRow['url'] : ''); ?>">
                <p class="admin-help">Volitelná URL adresa partnera.</p>
            </div>

            <div class="admin-field">
                <label for="partner-sort-order">Pořadí</label>
                <input id="partner-sort-order" type="number" name="sort_order" value="<?php echo $editRow ? (int) $editRow['sort_order'] : 0; ?>">
                <p class="admin-help">Nižší číslo se zobrazí dříve.</p>
            </div>

            <div class="admin-field">
                <label for="partner-image-file">Logo</label>
                <input id="partner-image-file" type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                <p class="admin-help">JPG/PNG/WEBP, max. 5 MB. <?php if ($editRow && !empty($editRow['image'])): ?>Současné: <code><?php echo h($editRow['image']); ?></code><?php endif; ?></p>
            </div>

            <div class="admin-form__actions">
                <button class="admin-button admin-button--primary" type="submit"><?php echo $editRow ? 'Upravit partnera' : 'Uložit partnera'; ?></button>
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
                    <thead><tr><th>ID</th><th>Název</th><th>Pořadí</th><th>Akce</th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td data-label="ID">#<?php echo (int) $row['id']; ?></td>
                            <td data-label="Název">
                                <?php echo h($row['name']); ?>
                                <?php if (!empty($row['url'])): ?>
                                    <span class="admin-muted">(<?php echo h($row['url']); ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td data-label="Pořadí"><?php echo (int) $row['sort_order']; ?></td>
                            <td data-label="Akce">
                                <div class="admin-actions">
                                    <a class="admin-button admin-button--secondary" href="partners.php?edit=<?php echo (int) $row['id']; ?>">Upravit</a>
                                    <form method="post" onsubmit="return confirm('Opravdu chcete tohoto partnera odstranit?');">
                                        <input type="hidden" name="id" value="<?php echo (int) $row['id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button class="admin-button admin-button--danger" type="submit">Odstranit</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
