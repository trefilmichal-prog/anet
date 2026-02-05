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
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
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

        <form class="admin-form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $editRow ? (int) $editRow['id'] : 0; ?>">
            <label>Název<input type="text" name="title" required value="<?php echo h($editRow ? $editRow['title'] : ''); ?>"></label>
            <label>Podtitul<input type="text" name="subtitle" value="<?php echo h($editRow ? $editRow['subtitle'] : ''); ?>"></label>
            <label>Místo<input type="text" name="venue" value="<?php echo h($editRow ? $editRow['venue'] : ''); ?>"></label>
            <label>Datum<input type="text" name="event_date" placeholder="YYYY-MM-DD" value="<?php echo h($editRow ? $editRow['event_date'] : ''); ?>"></label>
            <label>Čas<input type="text" name="event_time" placeholder="HH:MM" value="<?php echo h($editRow ? $editRow['event_time'] : ''); ?>"></label>
            <?php if ($editRow && !empty($editRow['image'])): ?>
                <p class="admin-muted">Současný obrázek: <code><?php echo h($editRow['image']); ?></code></p>
            <?php endif; ?>
            <label>Obrázek (JPG/PNG/WEBP, max 5 MB)<input type="file" name="image_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"></label>
            <label>Pořadí<input type="number" name="sort_order" value="<?php echo $editRow ? (int) $editRow['sort_order'] : 0; ?>"></label>
            <button class="admin-button" type="submit"><?php echo $editRow ? 'Upravit položku' : 'Uložit položku'; ?></button>
        </form>
    </section>

    <section class="admin-card">
        <h2>Seznam</h2>
        <table class="admin-table">
            <thead><tr><th>ID</th><th>Název</th><th>Datum/čas</th><th>Obrázek</th><th>Pořadí</th><th>Akce</th></tr></thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td>#<?php echo (int) $row['id']; ?></td>
                    <td><?php echo h($row['title']); ?></td>
                    <td><?php echo h($row['event_date']); ?> <?php echo h($row['event_time']); ?></td>
                    <td><?php echo h($row['image']); ?></td>
                    <td><?php echo (int) $row['sort_order']; ?></td>
                    <td><a href="program.php?edit=<?php echo (int) $row['id']; ?>">Upravit</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
