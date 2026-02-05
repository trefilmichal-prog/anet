<?php

function get_db()
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dataDir = dirname(__DIR__) . '/data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0777, true);
    }

    $dbFile = $dataDir . '/anet.sqlite';

    if (!extension_loaded('pdo_sqlite') || !in_array('sqlite', PDO::getAvailableDrivers(), true)) {
        throw new RuntimeException('Na serveru není dostupné PDO SQLite (extension pdo_sqlite / sqlite driver). Kontaktujte správce hostingu.');
    }

    try {
        $pdo = new PDO('sqlite:' . $dbFile);
    } catch (PDOException $e) {
        throw new RuntimeException('Nepodařilo se navázat SQLite připojení přes PDO. Zkontrolujte podporu PDO SQLite na hostingu.', 0, $e);
    }

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    initialize_schema($pdo);

    return $pdo;
}

function initialize_schema(PDO $pdo)
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

    // Backward-compatible migrations for older SQLite files.
    ensure_table_column($pdo, 'news', 'sort_order', 'INTEGER NOT NULL DEFAULT 0');
    ensure_table_column($pdo, 'program_items', 'subtitle', 'TEXT');
    ensure_table_column($pdo, 'program_items', 'event_time', 'TEXT');
    ensure_table_column($pdo, 'program_items', 'sort_order', 'INTEGER NOT NULL DEFAULT 0');
    ensure_table_column($pdo, 'artists', 'bio', 'TEXT');
    ensure_table_column($pdo, 'artists', 'sort_order', 'INTEGER NOT NULL DEFAULT 0');
    ensure_table_column($pdo, 'backgrounds', 'updated_at', 'TEXT');

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
}

function ensure_table_column(PDO $pdo, $tableName, $columnName, $columnDefinition)
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

function get_table_columns(PDO $pdo, $tableName)
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
