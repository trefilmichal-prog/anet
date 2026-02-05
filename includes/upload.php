<?php

const UPLOAD_ALLOWED_MIME_TYPES = array(
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp'
);
const UPLOAD_ALLOWED_EXTENSIONS = array('jpg', 'jpeg', 'png', 'webp');
const UPLOAD_MAX_FILE_SIZE = 5 * 1024 * 1024;
const FORM_INTERNAL_ERROR_MESSAGE = 'Došlo k interní chybě. Zkuste to prosím znovu později.';

function resolve_admin_form_error(Exception $exception)
{
    if ($exception instanceof RuntimeException) {
        $message = trim($exception->getMessage());
        if ($message !== '') {
            return $message;
        }
    }

    error_log('Admin/upload form failure: ' . $exception->getMessage());
    return FORM_INTERNAL_ERROR_MESSAGE;
}

function ensure_upload_directory($type)
{
    $allowed = array('backgrounds', 'news', 'program', 'artists');
    if (!in_array($type, $allowed, true)) {
        throw new RuntimeException('Neplatný typ upload adresáře.');
    }

    $baseDir = dirname(__DIR__) . '/uploads';
    if (!is_dir($baseDir)) {
        mkdir($baseDir, 0755, true);
    }

    $targetDir = $baseDir . '/' . $type;
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    return $targetDir;
}

function generate_upload_token($lengthBytes = 16)
{
    $lengthBytes = (int) $lengthBytes;
    if ($lengthBytes < 1) {
        $lengthBytes = 16;
    }

    $randomData = '';

    if (function_exists('random_bytes')) {
        try {
            $randomData = random_bytes($lengthBytes);
        } catch (Exception $exception) {
            $randomData = '';
        }
    }

    if ($randomData === '' && function_exists('openssl_random_pseudo_bytes')) {
        $opensslData = openssl_random_pseudo_bytes($lengthBytes);
        if (is_string($opensslData) && $opensslData !== '') {
            $randomData = $opensslData;
        }
    }

    if ($randomData !== '') {
        return bin2hex($randomData);
    }

    $requiredLength = $lengthBytes * 2;
    $token = '';

    while (strlen($token) < $requiredLength) {
        $token .= sha1(uniqid(mt_rand(), true) . microtime(true));
    }

    return substr($token, 0, $requiredLength);
}

function handle_image_upload($inputName, $type)
{
    if (!isset($_FILES[$inputName]) || !is_array($_FILES[$inputName])) {
        return null;
    }

    $file = $_FILES[$inputName];

    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Soubor se nepodařilo nahrát.');
    }

    $size = isset($file['size']) ? (int) $file['size'] : 0;
    if ($size <= 0 || $size > UPLOAD_MAX_FILE_SIZE) {
        throw new RuntimeException('Soubor je příliš velký. Maximum je 5 MB.');
    }

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        throw new RuntimeException('Neplatný upload souboru.');
    }

    $originalName = isset($file['name']) ? (string) $file['name'] : '';
    $pathInfo = pathinfo($originalName);
    $extension = isset($pathInfo['extension']) ? strtolower((string) $pathInfo['extension']) : '';
    if (!in_array($extension, UPLOAD_ALLOWED_EXTENSIONS, true)) {
        throw new RuntimeException('Nepovolená přípona souboru.');
    }

    $mimeType = '';
    $fileinfoAvailable = class_exists('finfo') && defined('FILEINFO_MIME_TYPE');

    if ($fileinfoAvailable) {
        try {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = (string) $finfo->file($file['tmp_name']);
        } catch (Exception $throwable) {
            throw new RuntimeException('Nepodařilo se ověřit typ obrázku přes fileinfo. Zkontrolujte konfiguraci PHP extension fileinfo.', 0, $throwable);
        }
    } else {
        if (!function_exists('getimagesize')) {
            throw new RuntimeException('Na serveru není dostupné fileinfo ani getimagesize(). Zapněte extension fileinfo v PHP konfiguraci serveru.');
        }

        $imageInfo = @getimagesize($file['tmp_name']);
        if (!is_array($imageInfo) || !isset($imageInfo['mime'])) {
            throw new RuntimeException('Nepodařilo se ověřit typ obrázku.');
        }

        $mimeType = (string) $imageInfo['mime'];
    }

    $allowedMimeTypes = UPLOAD_ALLOWED_MIME_TYPES;
    if (!array_key_exists($mimeType, $allowedMimeTypes)) {
        throw new RuntimeException('Nepovolený typ souboru.');
    }

    $targetExtension = $allowedMimeTypes[$mimeType];
    $extensionByMime = array(
        'jpg' => array('jpg', 'jpeg'),
        'png' => array('png'),
        'webp' => array('webp')
    );

    if (!in_array($extension, $extensionByMime[$targetExtension], true)) {
        throw new RuntimeException('Přípona neodpovídá MIME typu souboru.');
    }

    $targetDir = ensure_upload_directory($type);
    $safeName = generate_upload_token(16) . '.' . $targetExtension;
    $targetPath = $targetDir . '/' . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new RuntimeException('Soubor se nepodařilo uložit.');
    }

    return 'uploads/' . $type . '/' . $safeName;
}
