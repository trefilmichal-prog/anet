<?php

require_once __DIR__ . '/db.php';

function get_default_menu_items()
{
    return array(
        array(
            'label' => 'Úvod',
            'url' => 'index.php',
            'item_type' => 'link',
            'sort_order' => 10
        ),
        array(
            'label' => 'Aktuality',
            'url' => 'Aktuality.php',
            'item_type' => 'link',
            'sort_order' => 20
        ),
        array(
            'label' => 'Program',
            'url' => 'Program.php',
            'item_type' => 'link',
            'sort_order' => 30
        ),
        array(
            'label' => 'Umělci',
            'url' => 'Umelci.php',
            'item_type' => 'link',
            'sort_order' => 40
        ),
        array(
            'label' => 'O festivalu',
            'url' => 'Ofestivalu.php',
            'item_type' => 'link',
            'sort_order' => 50
        ),
        array(
            'label' => 'Facebook',
            'url' => 'https://www.facebook.com/hcimfcz/',
            'item_type' => 'icon',
            'sort_order' => 60
        ),
        array(
            'label' => 'E-mail',
            'url' => 'mailto:reditel@ipd-ccsh.cz',
            'item_type' => 'icon',
            'sort_order' => 70
        )
    );
}

function normalize_menu_item_type($value)
{
    $value = strtolower(trim((string) $value));
    return $value === 'icon' ? 'icon' : 'link';
}

function get_menu_items()
{
    try {
        $db = get_db();
        $rows = $db->query('SELECT id, label, url, item_type, sort_order FROM menu_items ORDER BY sort_order ASC, id ASC')
            ->fetchAll();
    } catch (Exception $e) {
        error_log('Menu load failed: ' . $e->getMessage());
        $rows = array();
    }

    if (!$rows) {
        return get_default_menu_items();
    }

    $items = array();
    foreach ($rows as $row) {
        $items[] = array(
            'label' => isset($row['label']) ? (string) $row['label'] : '',
            'url' => isset($row['url']) ? (string) $row['url'] : '',
            'item_type' => normalize_menu_item_type(isset($row['item_type']) ? $row['item_type'] : ''),
            'sort_order' => isset($row['sort_order']) ? (int) $row['sort_order'] : 0
        );
    }

    return $items;
}

function get_menu_icon_class($url, $label)
{
    $url = strtolower(trim((string) $url));
    $label = strtolower(trim((string) $label));

    if ($url !== '' && strpos($url, 'facebook.com') !== false) {
        return 'icon--fb';
    }

    if ($label !== '' && (strpos($label, 'facebook') !== false || $label === 'fb')) {
        return 'icon--fb';
    }

    if ($url !== '' && strpos($url, 'mailto:') === 0) {
        return 'icon--mail';
    }

    if ($label !== '' && (strpos($label, 'mail') !== false || strpos($label, 'e-mail') !== false)) {
        return 'icon--mail';
    }

    return '';
}
