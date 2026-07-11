<?php

namespace App\Core;

class ImageHelper
{
    /**
     * Converts and compresses any uploaded image to WebP format.
     *
     * @param array $file The $_FILES['image'] payload.
     * @param string $targetDir Target upload folder path.
     * @param int $quality Compression quality (0-100).
     * @return string|false Return relative file path on success, false on failure.
     */
    public static function uploadAndConvertToWebP(array $file, string $targetDir, int $quality = 80): string|false
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Generate unique filename
        $fileName = uniqid('img_', true) . '.webp';
        $destination = rtrim($targetDir, '/') . '/' . $fileName;

        // Ensure directory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $imageType = $file['type'];
        $tempPath = $file['tmp_name'];

        // Create image resource based on original type
        switch ($imageType) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($tempPath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($tempPath);
                // Preserve transparency details for WebP
                if ($image) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($tempPath);
                break;
            default:
                return false; // Unsupported format
        }

        if (!$image) {
            return false;
        }

        // Save WebP image
        $success = imagewebp($image, $destination, $quality);
        imagedestroy($image);

        return $success ? $destination : false;
    }
}
