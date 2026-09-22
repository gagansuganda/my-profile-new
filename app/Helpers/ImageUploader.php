<?php

namespace App\Helpers;

use App\Helpers\Uuid;
use Exception;

class ImageUploader
{
    public static function upload(array $file, string $targetDir = '../uploads/'): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Validate size (max 2MB)
        if ($file['size'] > 2 * 1024 * 1024) {
            throw new Exception("Ukuran berkas gambar maksimal 2MB.");
        }

        // Validate type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $file['tmp_name']);
        finfo_close($fileInfo);

        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception("Tipe berkas tidak didukung. Gunakan format JPG, PNG, atau WEBP.");
        }

        // Ensure directories exist
        $absoluteDir = rtrim($targetDir, '/') . '/';
        if (!is_dir($absoluteDir)) {
            mkdir($absoluteDir, 0755, true);
        }

        // Secure name
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (empty($extension)) {
            $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $extension = $extMap[$mimeType] ?? 'png';
        }

        $fileName = Uuid::v4() . '.' . strtolower($extension);
        $destination = $absoluteDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return 'uploads/' . $fileName;
        }

        return null;
    }
}
