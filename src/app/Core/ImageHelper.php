<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Secure Image Processing & Conversion Helper.
 * Enforces magic-byte MIME validation, randomized hash filenames, and WebP compression.
 */
class ImageHelper
{
    private const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB limit
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    /**
     * Validate and convert uploaded image to WebP format securely.
     *
     * @param array $file $_FILES element
     * @param string $targetDir Destination folder path
     * @param int $quality WebP compression quality (1-100)
     * @return string|false Relative file path on success, false on failure
     */

    public static function uploadAndConvertToWebP(array $file, string $targetDir, int $quality = 80): string|false
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        if (($file['size'] ?? 0) > self::MAX_FILE_SIZE) {
            return false;
        }

        $tmpPath = $file['tmp_name'];

        // Strict Server-Side MIME-Type Detection via Magic Bytes
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$finfo) {
            return false;
        }
        $mimeType = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);

        if (!array_key_exists($mimeType, self::ALLOWED_MIME_TYPES)) {
            return false;
        }

        // Generate cryptographically random filename
        $randomHash = bin2hex(random_bytes(16));
        $newFilename = "img_{$randomHash}.webp";

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $destination = rtrim($targetDir, '/') . '/' . $newFilename;

        // Create image resource based on actual magic-byte MIME
        $image = match ($mimeType) {
            'image/jpeg' => @imagecreatefromjpeg($tmpPath),
            'image/png' => @imagecreatefrompng($tmpPath),
            'image/webp' => @imagecreatefromwebp($tmpPath),
            default => null
        };

        if (!$image) {
            return false;
        }

        // Preserve alpha transparency for PNG / WebP
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $success = imagewebp($image, $destination, max(1, min(100, $quality)));
        imagedestroy($image);

        return $success ? $destination : false;
    }

    /**
     * Process base64 data URI image and save as WebP securely.
     */
    public static function saveBase64ToWebP(string $dataUri, string $targetDir, int $quality = 80): ?string
    {
        if (!str_starts_with($dataUri, 'data:image/')) {
            return null;
        }

        $dataUri = str_replace(' ', '+', $dataUri);
        $parts = explode(';', $dataUri, 2);
        if (count($parts) !== 2) {
            return null;
        }

        $mimePart = str_replace('data:', '', $parts[0]);
        if (!array_key_exists($mimePart, self::ALLOWED_MIME_TYPES)) {
            return null;
        }

        $dataPart = explode(',', $parts[1], 2);
        if (count($dataPart) !== 2) {
            return null;
        }

        $binaryData = base64_decode($dataPart[1], true);
        if ($binaryData === false || strlen($binaryData) > self::MAX_FILE_SIZE) {
            return null;
        }

        $image = @imagecreatefromstring($binaryData);
        if (!$image) {
            return null;
        }

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $randomHash = bin2hex(random_bytes(16));
        $newFilename = "qimg_{$randomHash}.webp";
        $fullPath = rtrim($targetDir, '/') . '/' . $newFilename;

        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $saved = imagewebp($image, $fullPath, max(1, min(100, $quality)));
        imagedestroy($image);

        return $saved ? $newFilename : null;
    }

    /**
     * Process, resize, and convert material article image to WebP securely.
     * Prevents database bloat by storing files on filesystem and capping max width.
     *
     * @param array $file $_FILES element
     * @param string $targetDir Destination folder path
     * @param int $maxWidth Max width constraint for optimization (default 1600px)
     * @param int $quality WebP compression quality (1-100)
     * @return string|null Generated filename on success, null on failure
     */
    public static function uploadMaterialImage(array $file, string $targetDir, int $maxWidth = 1600, int $quality = 80): ?string
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return null;
        }

        if (($file['size'] ?? 0) > self::MAX_FILE_SIZE) {
            return null;
        }

        $tmpPath = $file['tmp_name'];

        // Strict Server-Side MIME-Type Detection via Magic Bytes
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$finfo) {
            return null;
        }
        $mimeType = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);

        if (!array_key_exists($mimeType, self::ALLOWED_MIME_TYPES)) {
            return null;
        }

        // Create image resource based on actual magic-byte MIME
        $image = match ($mimeType) {
            'image/jpeg' => @imagecreatefromjpeg($tmpPath),
            'image/png' => @imagecreatefrompng($tmpPath),
            'image/webp' => @imagecreatefromwebp($tmpPath),
            default => null
        };

        if (!$image) {
            return null;
        }

        $origWidth = imagesx($image);
        $origHeight = imagesy($image);

        // Cap dimensions if image exceeds maxWidth (maintaining aspect ratio)
        if ($origWidth > $maxWidth && $origWidth > 0 && $origHeight > 0) {
            $newWidth = $maxWidth;
            $newHeight = (int)round(($origHeight / $origWidth) * $maxWidth);

            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            if ($resizedImage) {
                // Enable alpha blending and transparency preservation
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
                $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
                imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);

                imagecopyresampled(
                    $resizedImage,
                    $image,
                    0,
                    0,
                    0,
                    0,
                    $newWidth,
                    $newHeight,
                    $origWidth,
                    $origHeight
                );

                imagedestroy($image);
                $image = $resizedImage;
            }
        } else {
            // Preserve alpha transparency for PNG / WebP
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        // Generate cryptographically secure randomized filename
        $randomHash = bin2hex(random_bytes(16));
        $newFilename = "mat_img_{$randomHash}.webp";
        $destination = rtrim($targetDir, '/') . '/' . $newFilename;

        $success = imagewebp($image, $destination, max(1, min(100, $quality)));
        imagedestroy($image);

        return $success ? $newFilename : null;
    }
}
