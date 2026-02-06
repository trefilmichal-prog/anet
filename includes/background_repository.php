<?php

require_once __DIR__ . '/db.php';

function get_allowed_background_page_keys()
{
    return array(
        'home' => 'Úvod',
        'home_content' => 'Úvod – spodní pozadí',
        'news' => 'Aktuality',
        'program' => 'Program',
        'artists' => 'Umělci',
        'festival' => 'O festivalu'
    );
}

function get_background_image($pageKey, $fallbackImage)
{
    $pageKey = trim((string) $pageKey);
    $fallbackImage = trim((string) $fallbackImage);

    if ($fallbackImage === '') {
        $fallbackImage = 'kostel.jpg';
    }

    if ($pageKey === '') {
        return $fallbackImage;
    }

    try {
        $db = get_db();
        $stmt = $db->prepare('SELECT image FROM backgrounds WHERE page_key = :page_key ORDER BY updated_at DESC, id DESC LIMIT 1');
        $stmt->execute(array(':page_key' => $pageKey));
        $image = (string) $stmt->fetchColumn();

        if ($image !== '') {
            return $image;
        }
    } catch (Exception $e) {
        error_log('Background lookup failed for page_key "' . $pageKey . '": ' . $e->getMessage());
    }

    return $fallbackImage;
}
