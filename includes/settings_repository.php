<?php

require_once __DIR__ . '/db.php';

function get_setting($key, $default = '')
{
    $key = trim((string) $key);

    if ($key === '') {
        return (string) $default;
    }

    try {
        $db = get_db();
        $stmt = $db->prepare('SELECT value FROM settings WHERE key = :key LIMIT 1');
        $stmt->execute(array(':key' => $key));
        $value = $stmt->fetchColumn();

        if ($value !== false) {
            return (string) $value;
        }
    } catch (Exception $e) {
        error_log('Settings lookup failed for key "' . $key . '": ' . $e->getMessage());
    }

    return (string) $default;
}

function set_setting($key, $value)
{
    $key = trim((string) $key);

    if ($key === '') {
        return false;
    }

    $db = get_db();
    $stmt = $db->prepare('UPDATE settings SET value = :value WHERE key = :key');
    $stmt->execute(array(
        ':key' => $key,
        ':value' => (string) $value
    ));

    if ($stmt->rowCount() > 0) {
        return true;
    }

    $insert = $db->prepare('INSERT INTO settings(key, value) VALUES(:key, :value)');
    $insert->execute(array(
        ':key' => $key,
        ':value' => (string) $value
    ));

    return true;
}
