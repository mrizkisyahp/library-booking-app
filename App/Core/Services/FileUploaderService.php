<?php

namespace App\Core\Services;

class FileUploaderService
{
    /**
     * Handles generic file upload with validation.
     *
     * @param array  $file            The $_FILES[...] array
     * @param string $destinationDir  Absolute directory path
     * @param array  $allowedTypes    Allowed MIME types
     * @param int    $maxSizeMB       Maximum size in MB
     * @param string|null $prefix     Optional filename prefix
     *
     * @return array
     *   success: bool
     *   filename: string|null
     *   path: string|null
     *   error: string|null
     */
    public static function upload(
        array $file,
        string $destinationDir,
        array $allowedTypes,
        int $maxSizeMB = 2,
        ?string $prefix = null
    ) {
        // File exists?
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'error' => 'Invalid or missing file.'];
        }

        // Validate MIME type
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'File type not allowed.'];
        }

        // Validate file size
        if ($file['size'] > $maxSizeMB * 1024 * 1024) {
            return ['success' => false, 'error' => "File must be under {$maxSizeMB}MB."];
        }

        // Ensure directory exists
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        // Build safe filename
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $prefix = $prefix ? $prefix . '_' : '';
        $filename = $prefix . uniqid() . '_' . time() . '.' . $ext;

        $path = rtrim($destinationDir, '/') . '/' . $filename;

        // Move file
        if (!move_uploaded_file($file['tmp_name'], $path)) {
            return ['success' => false, 'error' => 'Failed to save uploaded file.'];
        }

        return [
            'success' => true,
            'filename' => $filename,
            'path' => $path,
            'error' => null
        ];
    }
}
