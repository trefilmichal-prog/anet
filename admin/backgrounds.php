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
        } catch (Exception $e) {
            $error = resolve_admin_form_error($e);
        }
    }

    $rows = $db->query('SELECT id, page_key, image, updated_at FROM backgrounds ORDER BY id DESC')->fetchAll();
} catch (RuntimeException $e) {
    error_log('Admin backgrounds DB failed: ' . $e->getMessage());
    if ($error === '') {
        $error = $e->getMessage();
    }
    $rows = array();
} catch (Exception $e) {
    error_log('Admin backgrounds init failed: ' . $e->getMessage());
    if ($error === '') {
        $error = 'Nepodařilo se načíst data. Zkuste to prosím znovu.';
    }
    $rows = array();
}
$adminPageTitle = 'Pozadí';
require_once __DIR__ . '/partials/header.php';
?>
<section class="admin-shell">
    <section class="admin-card">
        <h1>Pozadí</h1>
        <?php if ($message): ?><p class="admin-alert admin-alert--success"><?php echo h($message); ?></p><?php endif; ?>
        <?php if ($error): ?><p class="admin-alert admin-alert--error"><?php echo h($error); ?></p><?php endif; ?>

        <form class="admin-form admin-form--two-column" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $editRow ? (int) $editRow['id'] : 0; ?>">

            <div class="admin-field">
                <label for="background-page-key">Klíč stránky</label>
                <input id="background-page-key" type="text" name="page_key" required maxlength="100" value="<?php echo h($editRow ? $editRow['page_key'] : ''); ?>">
                <p class="admin-help">Např. home, news, artists.</p>
            </div>

            <div class="admin-field">
                <label for="background-image-file">Obrázek</label>
                <input id="background-image-file" type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                <p class="admin-help">JPG/PNG/WEBP, max. 5 MB. <?php if ($editRow && !empty($editRow['image'])): ?>Současný: <code><?php echo h($editRow['image']); ?></code><?php endif; ?></p>
            </div>

            <div class="admin-form__actions">
                <button class="admin-button admin-button--primary" type="submit"><?php echo $editRow ? 'Upravit pozadí' : 'Uložit pozadí'; ?></button>
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
                            <td data-label="Název"><?php echo h($row['page_key']); ?></td>
                            <td data-label="Datum/pořadí"><?php echo h($row['updated_at']); ?></td>
                            <td data-label="Akce"><a class="admin-button admin-button--secondary" href="backgrounds.php?edit=<?php echo (int) $row['id']; ?>">Upravit</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
