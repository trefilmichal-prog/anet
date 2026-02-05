<?php
require_once __DIR__ . '/auth.php';
require_admin_login();

$db = get_db();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $subtitle = isset($_POST['subtitle']) ? trim($_POST['subtitle']) : '';
    $venue = isset($_POST['venue']) ? trim($_POST['venue']) : '';
    $eventDate = isset($_POST['event_date']) ? trim($_POST['event_date']) : '';
    $eventTime = isset($_POST['event_time']) ? trim($_POST['event_time']) : '';
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';
    $sortOrder = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 0;

    if ($title !== '') {
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
}

$rows = $db->query('SELECT id, title, event_date, event_time, sort_order FROM program_items ORDER BY sort_order ASC, id DESC')->fetchAll();
?>
<!doctype html>
<html lang="cs">
<head><meta charset="utf-8"><title>Program</title></head>
<body>
<p><a href="dashboard.php">← Zpět na dashboard</a></p>
<h1>Program</h1>
<?php if ($message): ?><p style="color:green;"><?php echo h($message); ?></p><?php endif; ?>
<form method="post">
    <label>Název<br><input type="text" name="title" required></label><br>
    <label>Podtitul<br><input type="text" name="subtitle"></label><br>
    <label>Místo<br><input type="text" name="venue"></label><br>
    <label>Datum<br><input type="text" name="event_date" placeholder="YYYY-MM-DD"></label><br>
    <label>Čas<br><input type="text" name="event_time" placeholder="HH:MM"></label><br>
    <label>Obrázek (cesta/URL)<br><input type="text" name="image"></label><br>
    <label>Pořadí<br><input type="number" name="sort_order" value="0"></label><br>
    <button type="submit">Uložit položku</button>
</form>
<h2>Seznam</h2>
<ul>
<?php foreach ($rows as $row): ?>
    <li>#<?php echo (int) $row['id']; ?> | <?php echo h($row['title']); ?> | <?php echo h($row['event_date']); ?> <?php echo h($row['event_time']); ?> | pořadí <?php echo (int) $row['sort_order']; ?></li>
<?php endforeach; ?>
</ul>
</body>
</html>
