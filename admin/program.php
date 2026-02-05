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
        $editStmt = $db->prepare('SELECT * FROM program_items WHERE id = :id');
        $editStmt->execute(array(':id' => $editId));
        $editRow = $editStmt->fetch();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $subtitle = isset($_POST['subtitle']) ? trim($_POST['subtitle']) : '';
    $venue = isset($_POST['venue']) ? trim($_POST['venue']) : '';
    $eventDate = isset($_POST['event_date']) ? trim($_POST['event_date']) : '';
    $eventTime = isset($_POST['event_time']) ? trim($_POST['event_time']) : '';
    $sortOrder = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 0;

    try {
        if ($title === '') {
            throw new RuntimeException('Název je povinný.');
        }

        $existingImage = '';
        if ($id > 0) {
            $existingStmt = $db->prepare('SELECT image FROM program_items WHERE id = :id');
            $existingStmt->execute(array(':id' => $id));
            $existingImage = (string) $existingStmt->fetchColumn();
        }

        $uploadedImage = handle_image_upload('image_file', 'program');
        $image = $uploadedImage !== null ? $uploadedImage : $existingImage;

        if ($id > 0) {
            $stmt = $db->prepare('UPDATE program_items SET title = :title, subtitle = :subtitle, venue = :venue, event_date = :event_date, event_time = :event_time, image = :image, sort_order = :sort_order WHERE id = :id');
            $stmt->execute(array(
                ':title' => $title,
                ':subtitle' => $subtitle,
                ':venue' => $venue,
                ':event_date' => $eventDate,
                ':event_time' => $eventTime,
                ':image' => $image,
                ':sort_order' => $sortOrder,
                ':id' => $id
            ));
            $message = 'Položka programu upravena.';
        } else {
            $stmt = $db->prepare('INSERT INTO program_items(title, subtitle, venue, event_date, event_time, image, sort_order) VALUES(:title, :subtitle, :venue, :event_date, :event_time, :image, :sort_order)');
            $stmt->execute(array(
                ':title' => $title,
                ':subtitle' => $subtitle,
                ':venue' => $venue,
                ':event_date' => $eventDate,
                ':event_time' => $eventTime,
                ':image' => $image,
                ':sort_order' => $sortOrder
            ));
            $message = 'Položka programu uložena.';
        }

        $editRow = null;
    } catch (Throwable $e) {
        $error = $e->getMessage() !== '' ? $e->getMessage() : 'Při ukládání došlo k chybě.';
    }
}

$rows = $db->query('SELECT id, title, image, event_date, event_time, sort_order FROM program_items ORDER BY sort_order ASC, id DESC')->fetchAll();
$adminPageTitle = 'Program';
require_once __DIR__ . '/partials/header.php';
?>
<section class="admin-shell">
    <section class="admin-card">
        <h1>Program</h1>
        <?php if ($message): ?><p class="admin-alert admin-alert--success"><?php echo h($message); ?></p><?php endif; ?>
        <?php if ($error): ?><p class="admin-alert admin-alert--error"><?php echo h($error); ?></p><?php endif; ?>

        <form class="admin-form admin-form--two-column" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $editRow ? (int) $editRow['id'] : 0; ?>">

            <div class="admin-field">
                <label for="program-title">Název</label>
                <input id="program-title" type="text" name="title" required maxlength="180" value="<?php echo h($editRow ? $editRow['title'] : ''); ?>">
                <p class="admin-help">Povinný název bodu programu.</p>
            </div>

            <div class="admin-field">
                <label for="program-subtitle">Podtitul</label>
                <input id="program-subtitle" type="text" name="subtitle" maxlength="180" value="<?php echo h($editRow ? $editRow['subtitle'] : ''); ?>">
                <p class="admin-help">Volitelný doplňující text.</p>
            </div>

            <div class="admin-field">
                <label for="program-venue">Místo</label>
                <input id="program-venue" type="text" name="venue" maxlength="150" value="<?php echo h($editRow ? $editRow['venue'] : ''); ?>">
                <p class="admin-help">Např. Kostel sv. Jakuba.</p>
            </div>

            <div class="admin-field">
                <label for="program-sort-order">Pořadí</label>
                <input id="program-sort-order" type="number" name="sort_order" value="<?php echo $editRow ? (int) $editRow['sort_order'] : 0; ?>">
                <p class="admin-help">Nižší číslo se zobrazí dříve.</p>
            </div>

            <div class="admin-field">
                <label for="program-date">Datum</label>
                <input id="program-date" type="date" name="event_date" value="<?php echo h($editRow ? $editRow['event_date'] : ''); ?>">
                <p class="admin-help">Kalendářní datum vystoupení.</p>
            </div>

            <div class="admin-field">
                <label for="program-time">Čas</label>
                <input id="program-time" type="time" name="event_time" value="<?php echo h($editRow ? $editRow['event_time'] : ''); ?>">
                <p class="admin-help">Čas začátku.</p>
            </div>

            <div class="admin-field admin-field--full">
                <label for="program-image-file">Obrázek</label>
                <input id="program-image-file" type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                <p class="admin-help">JPG/PNG/WEBP, max. 5 MB. <?php if ($editRow && !empty($editRow['image'])): ?>Současný: <code><?php echo h($editRow['image']); ?></code><?php endif; ?></p>
            </div>

            <div class="admin-form__actions">
                <button class="admin-button admin-button--primary" type="submit"><?php echo $editRow ? 'Upravit položku' : 'Uložit položku'; ?></button>
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
                            <td data-label="Datum/pořadí"><?php echo h(trim($row['event_date'] . ' ' . $row['event_time'])); ?> / <?php echo (int) $row['sort_order']; ?></td>
                            <td data-label="Akce"><a class="admin-button admin-button--secondary" href="program.php?edit=<?php echo (int) $row['id']; ?>">Upravit</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
