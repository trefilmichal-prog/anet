<?php
require_once __DIR__ . '/auth.php';
require_once dirname(__DIR__) . '/includes/menu_repository.php';
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
            $editStmt = $db->prepare('SELECT * FROM menu_items WHERE id = :id');
            $editStmt->execute(array(':id' => $editId));
            $editRow = $editStmt->fetch();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = isset($_POST['action']) ? trim($_POST['action']) : 'save';
        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        $label = isset($_POST['label']) ? trim($_POST['label']) : '';
        $url = isset($_POST['url']) ? trim($_POST['url']) : '';
        $itemType = normalize_menu_item_type(isset($_POST['item_type']) ? $_POST['item_type'] : '');
        $sortOrder = isset($_POST['sort_order']) ? (int) $_POST['sort_order'] : 0;

        try {
            if ($action === 'delete') {
                if ($id <= 0) {
                    throw new RuntimeException('Nepodařilo se určit položku ke smazání.');
                }

                $deleteStmt = $db->prepare('DELETE FROM menu_items WHERE id = :id');
                $deleteStmt->execute(array(':id' => $id));
                $message = 'Položka menu byla odstraněna.';
                $editRow = null;
            } else {
                if ($label === '') {
                    throw new RuntimeException('Text položky je povinný.');
                }
                if ($url === '') {
                    throw new RuntimeException('URL je povinná.');
                }

                if ($id > 0) {
                    $stmt = $db->prepare('UPDATE menu_items SET label = :label, url = :url, item_type = :item_type, sort_order = :sort_order WHERE id = :id');
                    $stmt->execute(array(
                        ':label' => $label,
                        ':url' => $url,
                        ':item_type' => $itemType,
                        ':sort_order' => $sortOrder,
                        ':id' => $id
                    ));
                    $message = 'Položka menu byla upravena.';
                } else {
                    $stmt = $db->prepare('INSERT INTO menu_items(label, url, item_type, sort_order) VALUES(:label, :url, :item_type, :sort_order)');
                    $stmt->execute(array(
                        ':label' => $label,
                        ':url' => $url,
                        ':item_type' => $itemType,
                        ':sort_order' => $sortOrder
                    ));
                    $message = 'Položka menu byla uložena.';
                }

                $editRow = null;
            }
        } catch (Exception $e) {
            $error = resolve_admin_form_error($e);
        }
    }

    $rows = $db->query('SELECT id, label, url, item_type, sort_order FROM menu_items ORDER BY sort_order ASC, id ASC')->fetchAll();
} catch (RuntimeException $e) {
    error_log('Admin menu DB failed: ' . $e->getMessage());
    if ($error === '') {
        $error = $e->getMessage();
    }
    $rows = array();
} catch (Exception $e) {
    error_log('Admin menu init failed: ' . $e->getMessage());
    if ($error === '') {
        $error = 'Nepodařilo se načíst data. Zkuste to prosím znovu.';
    }
    $rows = array();
}

$adminPageTitle = 'Menu';
require_once __DIR__ . '/partials/header.php';
?>
<section class="admin-shell">
    <section class="admin-card">
        <h1>Menu webu</h1>
        <?php if ($message): ?><p class="admin-alert admin-alert--success"><?php echo h($message); ?></p><?php endif; ?>
        <?php if ($error): ?><p class="admin-alert admin-alert--error"><?php echo h($error); ?></p><?php endif; ?>
        <?php if (!$rows): ?>
            <p class="admin-help">Pokud neuložíte žádné položky, web použije výchozí menu.</p>
        <?php endif; ?>

        <form class="admin-form admin-form--two-column" method="post">
            <input type="hidden" name="id" value="<?php echo $editRow ? (int) $editRow['id'] : 0; ?>">

            <div class="admin-field admin-field--full">
                <label for="menu-label">Text</label>
                <input id="menu-label" type="text" name="label" required maxlength="120" value="<?php echo h($editRow ? $editRow['label'] : ''); ?>">
                <p class="admin-help">Text odkazu nebo popis ikonky (např. Facebook).</p>
            </div>

            <div class="admin-field admin-field--full">
                <label for="menu-url">URL</label>
                <input id="menu-url" type="text" name="url" required maxlength="255" value="<?php echo h($editRow ? $editRow['url'] : ''); ?>">
                <p class="admin-help">Např. index.php, https://example.com nebo mailto:info@example.com.</p>
            </div>

            <div class="admin-field">
                <label for="menu-type">Typ</label>
                <select id="menu-type" name="item_type">
                    <?php $currentType = $editRow ? normalize_menu_item_type($editRow['item_type']) : 'link'; ?>
                    <option value="link" <?php echo $currentType === 'link' ? 'selected' : ''; ?>>Odkaz</option>
                    <option value="icon" <?php echo $currentType === 'icon' ? 'selected' : ''; ?>>Ikonka</option>
                </select>
                <p class="admin-help">Ikonka zobrazí pouze symbol (např. Facebook, e-mail).</p>
            </div>

            <div class="admin-field">
                <label for="menu-sort-order">Pořadí</label>
                <input id="menu-sort-order" type="number" name="sort_order" value="<?php echo $editRow ? (int) $editRow['sort_order'] : 0; ?>">
                <p class="admin-help">Nižší číslo se zobrazí dříve.</p>
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
                    <thead><tr><th>ID</th><th>Název</th><th>Typ</th><th>URL</th><th>Pořadí</th><th>Akce</th></tr></thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td data-label="ID">#<?php echo (int) $row['id']; ?></td>
                            <td data-label="Název"><?php echo h($row['label']); ?></td>
                            <td data-label="Typ"><?php echo h(normalize_menu_item_type($row['item_type']) === 'icon' ? 'Ikonka' : 'Odkaz'); ?></td>
                            <td data-label="URL"><?php echo h($row['url']); ?></td>
                            <td data-label="Pořadí"><?php echo (int) $row['sort_order']; ?></td>
                            <td data-label="Akce">
                                <div class="admin-actions">
                                    <a class="admin-button admin-button--secondary" href="menu.php?edit=<?php echo (int) $row['id']; ?>">Upravit</a>
                                    <form method="post" onsubmit="return confirm('Opravdu chcete tuto položku odstranit?');">
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
