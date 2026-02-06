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

function get_admin_menu_defaults()
{
    return array(
        'admin_menu_bg_enabled' => '1',
        'admin_menu_bg_color' => '#160c23',
        'admin_menu_bg_opacity' => '0.95'
    );
}

function get_site_font_family_default()
{
    return 'serif';
}

function normalize_site_font_family($value)
{
    $value = trim((string) $value);

    if ($value === '') {
        return '';
    }

    if (strlen($value) > 120) {
        return '';
    }

    if (!preg_match('/^[-a-zA-Z0-9 ,"\\\']+$/', $value)) {
        return '';
    }

    return $value;
}

function normalize_admin_menu_color($value, &$rgb = null)
{
    $value = trim((string) $value);
    $rgb = null;

    if ($value === '') {
        return '';
    }

    if (preg_match('/^#([0-9a-f]{3})$/i', $value, $matches)) {
        $hex = strtolower($matches[1]);
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    } elseif (preg_match('/^#([0-9a-f]{6})$/i', $value, $matches)) {
        $hex = strtolower($matches[1]);
    } elseif (preg_match('/^rgb\\s*\\(\\s*(\\d{1,3})\\s*,\\s*(\\d{1,3})\\s*,\\s*(\\d{1,3})\\s*\\)$/i', $value, $matches)) {
        $r = (int) $matches[1];
        $g = (int) $matches[2];
        $b = (int) $matches[3];

        if ($r > 255 || $g > 255 || $b > 255) {
            return '';
        }

        $hex = sprintf('%02x%02x%02x', $r, $g, $b);
    } else {
        return '';
    }

    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $rgb = array('r' => $r, 'g' => $g, 'b' => $b);

    return '#' . $hex;
}

function normalize_admin_menu_opacity($value)
{
    $raw = trim((string) $value);

    if ($raw === '') {
        return '';
    }

    $raw = str_replace('%', '', $raw);

    if (!is_numeric($raw)) {
        return '';
    }

    $number = (float) $raw;

    if ($number > 1) {
        if ($number > 100 || $number < 0) {
            return '';
        }
        $number = $number / 100;
    }

    if ($number < 0 || $number > 1) {
        return '';
    }

    $normalized = number_format(round($number, 2), 2, '.', '');
    $normalized = rtrim(rtrim($normalized, '0'), '.');

    return $normalized;
}

function get_admin_menu_rgba(&$menu_enabled = null)
{
    $defaults = get_admin_menu_defaults();
    $menuEnabledValue = get_setting('admin_menu_bg_enabled', $defaults['admin_menu_bg_enabled']);
    $menu_enabled = $menuEnabledValue === '1';

    if (!$menu_enabled) {
        return '';
    }

    $menuColorValue = get_setting('admin_menu_bg_color', $defaults['admin_menu_bg_color']);
    $menuOpacityValue = get_setting('admin_menu_bg_opacity', $defaults['admin_menu_bg_opacity']);

    $menuRgb = null;
    $menuColorNormalized = normalize_admin_menu_color($menuColorValue, $menuRgb);
    if ($menuColorNormalized === '') {
        $menuColorNormalized = normalize_admin_menu_color($defaults['admin_menu_bg_color'], $menuRgb);
    }

    $menuOpacityNormalized = normalize_admin_menu_opacity($menuOpacityValue);
    if ($menuOpacityNormalized === '') {
        $menuOpacityNormalized = normalize_admin_menu_opacity($defaults['admin_menu_bg_opacity']);
    }

    if ($menuRgb === null || $menuOpacityNormalized === '') {
        return '';
    }

    return 'rgba(' . $menuRgb['r'] . ', ' . $menuRgb['g'] . ', ' . $menuRgb['b'] . ', ' . $menuOpacityNormalized . ')';
}
