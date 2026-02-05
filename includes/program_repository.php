<?php

require_once __DIR__ . '/db.php';

function get_program_items($limit = null)
{
    $db = get_db();

    $sql = "SELECT id, title, subtitle, venue, event_date, event_time, image, sort_order
            FROM program_items
            ORDER BY
                CASE
                    WHEN event_date IS NULL OR TRIM(event_date) = '' THEN 1
                    ELSE 0
                END ASC,
                datetime(event_date || ' ' || CASE
                    WHEN event_time IS NULL OR TRIM(event_time) = '' THEN '23:59'
                    ELSE event_time
                END) ASC,
                sort_order ASC,
                id DESC";

    if ($limit !== null) {
        $sql .= ' LIMIT :limit';
    }

    $stmt = $db->prepare($sql);

    if ($limit !== null) {
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    }

    $stmt->execute();

    return $stmt->fetchAll();
}
