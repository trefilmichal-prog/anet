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
            $editStmt = $db->prepare('SELECT * FROM artists WHERE id = :id');
            $editStmt->execute(array(':id' => $editId));
            $editRow = $editStmt->fetch();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? trim($_POST['action']) : 'save';
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $role = isset($_POST['role']) ? trim($_POST['role']) : '';
        $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
        $sortOrder = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 0;

        try {
            if ($action === 'delete') {
                if ($id <= 0) {
                    throw new RuntimeException('Nepodařilo se určit položku ke smazání.');
                }

                $deleteStmt = $db->prepare('DELETE FROM artists WHERE id = :id');
                $deleteStmt->execute(array(':id' => $id));
                $message = 'Umělec byl odstraněn.';
                $editRow = null;
            } else {
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
            }
        } catch (Exception $e) {
            $error = resolve_admin_form_error($e);
        }
    }

    $rows = $db->query('SELECT id, name, role, image, sort_order FROM artists ORDER BY sort_order ASC, id DESC')->fetchAll();
} catch (RuntimeException $e) {
    error_log('Admin artists DB failed: ' . $e->getMessage());
    if ($error === '') {
        $error = $e->getMessage();
    }
    $rows = array();
} catch (Exception $e) {
    error_log('Admin artists init failed: ' . $e->getMessage());
    if ($error === '') {
        $error = 'Nepodařilo se načíst data. Zkuste to prosím znovu.';
    }
    $rows = array();
}
$adminPageTitle = 'Umělci';
require_once __DIR__ . '/partials/header.php';
?>
<section class="admin-shell">
    <section class="admin-card">
        <h1>Umělci</h1>
        <?php if ($message): ?><p class="admin-alert admin-alert--success"><?php echo h($message); ?></p><?php endif; ?>
        <?php if ($error): ?><p class="admin-alert admin-alert--error"><?php echo h($error); ?></p><?php endif; ?>

        <form class="admin-form admin-form--two-column" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $editRow ? (int) $editRow['id'] : 0; ?>">

            <div class="admin-field">
                <label for="artist-name">Jméno</label>
                <input id="artist-name" type="text" name="name" required maxlength="150" value="<?php echo h($editRow ? $editRow['name'] : ''); ?>">
                <p class="admin-help">Povinné jméno účinkujícího.</p>
            </div>

            <div class="admin-field">
                <label for="artist-role">Role</label>
                <input id="artist-role" type="text" name="role" maxlength="150" value="<?php echo h($editRow ? $editRow['role'] : ''); ?>">
                <p class="admin-help">Např. housle, dirigent, sbor.</p>
            </div>

            <div class="admin-field admin-field--full">
                <label for="artist-bio">Bio</label>
                <textarea id="artist-bio" name="bio"><?php echo h($editRow ? $editRow['bio'] : ''); ?></textarea>
                <p class="admin-help">Volitelný medailonek umělce.</p>
            </div>

            <div class="admin-field">
                <label for="artist-sort-order">Pořadí</label>
                <input id="artist-sort-order" type="number" name="sort_order" value="<?php echo $editRow ? (int) $editRow['sort_order'] : 0; ?>">
                <p class="admin-help">Nižší číslo se zobrazí dříve.</p>
            </div>

            <div class="admin-field">
                <label for="artist-image-file">Obrázek</label>
                <input id="artist-image-file" type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                <p class="admin-help">JPG/PNG/WEBP, max. 5 MB. <?php if ($editRow && !empty($editRow['image'])): ?>Současný: <code><?php echo h($editRow['image']); ?></code><?php endif; ?></p>
            </div>

            <div class="admin-form__actions">
                <button class="admin-button admin-button--primary" type="submit"><?php echo $editRow ? 'Upravit umělce' : 'Uložit umělce'; ?></button>
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
                            <td data-label="Název"><?php echo h($row['name']); ?><?php if ($row['role']): ?> <span class="admin-muted">(<?php echo h($row['role']); ?>)</span><?php endif; ?></td>
                            <td data-label="Datum/pořadí">Pořadí: <?php echo (int) $row['sort_order']; ?></td>
                            <td data-label="Akce">
                                <div class="admin-actions">
                                    <a class="admin-button admin-button--secondary" href="artists.php?edit=<?php echo (int) $row['id']; ?>">Upravit</a>
                                    <form method="post" onsubmit="return confirm('Opravdu chcete tohoto umělce odstranit?');">
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
