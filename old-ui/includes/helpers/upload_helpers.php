<?php
/**
 * Upload Helpers
 */

function allowed_upload_extensions() {
    return ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'doc', 'docx'];
}

function max_upload_size_bytes() {
    return 25 * 1024 * 1024;
}

function safe_file_extension($filename) {
    return strtolower(pathinfo((string) $filename, PATHINFO_EXTENSION));
}

function is_allowed_upload($file) {
    if (empty($file) || !isset($file['name'], $file['size'], $file['error'])) {
        return [false, 'Invalid upload.'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [false, 'Upload failed. Please try again.'];
    }

    if ((int) $file['size'] > max_upload_size_bytes()) {
        return [false, 'File is too large. Maximum allowed size is 25MB.'];
    }

    $extension = safe_file_extension($file['name']);

    if (!in_array($extension, allowed_upload_extensions(), true)) {
        return [false, 'File type not allowed.'];
    }

    return [true, 'OK'];
}

function generate_safe_filename($originalName, $prefix = 'document') {
    $extension = safe_file_extension($originalName);
    $random = bin2hex(random_bytes(12));

    return preg_replace('/[^a-zA-Z0-9_-]/', '_', $prefix) . '_' . date('Ymd_His') . '_' . $random . '.' . $extension;
}

function upload_base_dir() {
    $dir = dirname(__DIR__, 2) . '/storage/uploads';

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    return $dir;
}

function move_secure_upload($file, $subfolder = 'documents') {
    [$allowed, $message] = is_allowed_upload($file);

    if (!$allowed) {
        return [false, $message, null];
    }

    $dir = upload_base_dir() . '/' . trim($subfolder, '/');

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }

    $safeName = generate_safe_filename($file['name'], $subfolder);
    $target = $dir . '/' . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $target)) {
        return [false, 'Unable to save uploaded file.', null];
    }

    return [true, 'Uploaded successfully.', [
        'stored_name' => $safeName,
        'original_name' => $file['name'],
        'path' => $target,
        'size' => (int) $file['size'],
        'extension' => safe_file_extension($file['name']),
    ]];
}