<?php

require_once __DIR__ . '/festival_content.php';

function get_db()
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $dataDir = dirname(__DIR__) . '/data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0777, true);
    }

    if (!is_writable($dataDir)) {
        throw new RuntimeException('Adresář data/ není zapisovatelný. Zkontrolujte prosím práva k souborům pro PHP proces (webserver uživatel).');
    }

    $dbFile = $dataDir . '/anet.sqlite';

    if (file_exists($dbFile) && !is_writable($dbFile)) {
        throw new RuntimeException('Soubor databáze data/anet.sqlite není zapisovatelný. Zkontrolujte prosím práva nebo vlastníka souboru.');
    }

    if (!extension_loaded('pdo') || !class_exists('PDO')) {
        throw new RuntimeException('Na serveru není dostupné PDO rozšíření. Kontaktujte správce hostingu.');
    }

    if (!extension_loaded('pdo_sqlite') || !in_array('sqlite', PDO::getAvailableDrivers(), true)) {
        throw new RuntimeException('Na serveru není dostupné PDO SQLite (extension pdo_sqlite / sqlite driver). Kontaktujte správce hostingu.');
    }

    try {
        $pdo = new PDO('sqlite:' . $dbFile);
    } catch (Exception $e) {
        throw new RuntimeException('Nepodařilo se navázat SQLite připojení přes PDO. Zkontrolujte podporu PDO SQLite na hostingu.', 0, $e);
    }

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    try {
        initialize_schema($pdo);
    } catch (Exception $e) {
        throw new RuntimeException('Nepodařilo se inicializovat databázi. Pravděpodobně chybí práva k zápisu, nebo je databázový soubor poškozený. Kontaktujte správce.', 0, $e);
    }

    return $pdo;
}

function initialize_schema($pdo)
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT NOT NULL
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS news (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        body TEXT,
        image TEXT,
        published_at TEXT,
        sort_order INTEGER NOT NULL DEFAULT 0
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS program_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        subtitle TEXT,
        venue TEXT,
        event_date TEXT,
        event_time TEXT,
        image TEXT,
        sort_order INTEGER NOT NULL DEFAULT 0
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS artists (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        role TEXT,
        bio TEXT,
        image TEXT,
        sort_order INTEGER NOT NULL DEFAULT 0
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS backgrounds (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        page_key TEXT NOT NULL,
        image TEXT NOT NULL,
        updated_at TEXT
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS partners (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        url TEXT,
        image TEXT,
        sort_order INTEGER NOT NULL DEFAULT 0
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS menu_items (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        label TEXT NOT NULL,
        url TEXT NOT NULL,
        item_type TEXT NOT NULL,
        sort_order INTEGER NOT NULL DEFAULT 0
    )');

    // Backward-compatible migrations for older SQLite files.
    ensure_table_column($pdo, 'news', 'sort_order', 'INTEGER NOT NULL DEFAULT 0');
    ensure_table_column($pdo, 'program_items', 'subtitle', 'TEXT');
    ensure_table_column($pdo, 'program_items', 'event_time', 'TEXT');
    ensure_table_column($pdo, 'program_items', 'sort_order', 'INTEGER NOT NULL DEFAULT 0');
    ensure_table_column($pdo, 'artists', 'bio', 'TEXT');
    ensure_table_column($pdo, 'artists', 'sort_order', 'INTEGER NOT NULL DEFAULT 0');
    ensure_table_column($pdo, 'backgrounds', 'updated_at', 'TEXT');
    ensure_table_column($pdo, 'partners', 'name', 'TEXT NOT NULL');
    ensure_table_column($pdo, 'partners', 'url', 'TEXT');
    ensure_table_column($pdo, 'partners', 'image', 'TEXT');
    ensure_table_column($pdo, 'partners', 'sort_order', 'INTEGER NOT NULL DEFAULT 0');
    ensure_table_column($pdo, 'menu_items', 'label', 'TEXT NOT NULL');
    ensure_table_column($pdo, 'menu_items', 'url', 'TEXT NOT NULL');
    ensure_table_column($pdo, 'menu_items', 'item_type', 'TEXT NOT NULL');
    ensure_table_column($pdo, 'menu_items', 'sort_order', 'INTEGER NOT NULL DEFAULT 0');

    $stmt = $pdo->prepare('SELECT value FROM settings WHERE key = :key');
    $stmt->execute(array(':key' => 'admin_pin_hash'));
    $pinHash = $stmt->fetchColumn();

    if (!$pinHash) {
        $defaultHash = password_hash('1234', PASSWORD_DEFAULT);
        $insert = $pdo->prepare('INSERT INTO settings(key, value) VALUES(:key, :value)');
        $insert->execute(array(
            ':key' => 'admin_pin_hash',
            ':value' => $defaultHash
        ));
    }

    $stmt = $pdo->prepare('SELECT value FROM settings WHERE key = :key');
    $stmt->execute(array(':key' => 'festival_page_text'));
    $festivalPageText = $stmt->fetchColumn();

    if ($festivalPageText === false) {
        $insert = $pdo->prepare('INSERT INTO settings(key, value) VALUES(:key, :value)');
        $insert->execute(array(
            ':key' => 'festival_page_text',
            ':value' => get_default_festival_page_text()
        ));
    }

    $stmt = $pdo->prepare('SELECT value FROM settings WHERE key = :key');
    $stmt->execute(array(':key' => 'home_artists_text'));
    $homeArtistsText = $stmt->fetchColumn();

    if ($homeArtistsText === false) {
        $insert = $pdo->prepare('INSERT INTO settings(key, value) VALUES(:key, :value)');
        $insert->execute(array(
            ':key' => 'home_artists_text',
            ':value' => get_default_home_artists_text()
        ));
    }

    $stmt = $pdo->prepare('SELECT value FROM settings WHERE key = :key');
    $stmt->execute(array(':key' => 'program_placeholder_text'));
    $programPlaceholderText = $stmt->fetchColumn();

    if ($programPlaceholderText === false) {
        $insert = $pdo->prepare('INSERT INTO settings(key, value) VALUES(:key, :value)');
        $insert->execute(array(
            ':key' => 'program_placeholder_text',
            ':value' => 'Program bude brzy doplněn.'
        ));
    }

    $stmt = $pdo->prepare('SELECT value FROM settings WHERE key = :key');
    $stmt->execute(array(':key' => 'artists_placeholder_text'));
    $artistsPlaceholderText = $stmt->fetchColumn();

    if ($artistsPlaceholderText === false) {
        $insert = $pdo->prepare('INSERT INTO settings(key, value) VALUES(:key, :value)');
        $insert->execute(array(
            ':key' => 'artists_placeholder_text',
            ':value' => 'Umělci budou brzy doplněni.'
        ));
    }

    $stmt = $pdo->prepare('SELECT value FROM settings WHERE key = :key');
    $stmt->execute(array(':key' => 'site_font_family'));
    $siteFontFamily = $stmt->fetchColumn();

    if ($siteFontFamily === false) {
        $insert = $pdo->prepare('INSERT INTO settings(key, value) VALUES(:key, :value)');
        $insert->execute(array(
            ':key' => 'site_font_family',
            ':value' => 'serif'
        ));
    }
}

function ensure_table_column($pdo, $tableName, $columnName, $columnDefinition)
{
    $existingColumns = get_table_columns($pdo, $tableName);

    if (isset($existingColumns[$columnName])) {
        return;
    }

    $sql = sprintf(
        'ALTER TABLE %s ADD COLUMN %s %s',
        $tableName,
        $columnName,
        $columnDefinition
    );

    $pdo->exec($sql);
}

function get_table_columns($pdo, $tableName)
{
    $columns = array();
    $statement = $pdo->query("PRAGMA table_info('" . str_replace("'", "''", $tableName) . "')");
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        if (isset($row['name'])) {
            $columns[(string) $row['name']] = true;
        }
    }

    return $columns;
}
