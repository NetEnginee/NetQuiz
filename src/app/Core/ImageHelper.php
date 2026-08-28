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
        'image/jpg'  => 'jpg',
        'image/pjpeg' => 'jpg',
        'image/png'  => 'png',
        'image/x-png' => 'png',
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
        $mimeType = $finfo ? finfo_file($finfo, $tmpPath) : (function_exists('mime_content_type') ? mime_content_type($tmpPath) : '');
        if ($finfo) {
            finfo_close($finfo);
        }

        if (!array_key_exists(strtolower((string)$mimeType), self::ALLOWED_MIME_TYPES)) {
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
        $image = match (strtolower((string)$mimeType)) {
            'image/jpeg', 'image/jpg', 'image/pjpeg' => @imagecreatefromjpeg($tmpPath),
            'image/png', 'image/x-png' => @imagecreatefrompng($tmpPath),
            'image/webp' => @imagecreatefromwebp($tmpPath),
            default => null
        };

        if (!$image) {
            $rawBinary = @file_get_contents($tmpPath);
            if ($rawBinary) {
                $image = @imagecreatefromstring($rawBinary);
            }
        }

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
        if (!array_key_exists(strtolower($mimePart), self::ALLOWED_MIME_TYPES)) {
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
        $mimeType = $finfo ? finfo_file($finfo, $tmpPath) : (function_exists('mime_content_type') ? mime_content_type($tmpPath) : '');
        if ($finfo) {
            finfo_close($finfo);
        }

        $mimeType = strtolower((string)$mimeType);
        if (!array_key_exists($mimeType, self::ALLOWED_MIME_TYPES)) {
            $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                return null;
            }
        }

        // Create image resource based on actual magic-byte MIME
        $image = match ($mimeType) {
            'image/jpeg', 'image/jpg', 'image/pjpeg' => @imagecreatefromjpeg($tmpPath),
            'image/png', 'image/x-png' => @imagecreatefrompng($tmpPath),
            'image/webp' => @imagecreatefromwebp($tmpPath),
            default => null
        };

        if (!$image) {
            $rawBinary = @file_get_contents($tmpPath);
            if ($rawBinary) {
                $image = @imagecreatefromstring($rawBinary);
            }
        }

        if (!$image) {
            return null;
        }

        // Auto-correct JPEG orientation based on EXIF if available
        if (function_exists('exif_read_data') && ($mimeType === 'image/jpeg' || $mimeType === 'image/pjpeg' || $mimeType === 'image/jpg')) {
            $exif = @exif_read_data($tmpPath);
            if (!empty($exif['Orientation'])) {
                $image = match ((int)$exif['Orientation']) {
                    3 => imagerotate($image, 180, 0),
                    6 => imagerotate($image, -90, 0),
                    8 => imagerotate($image, 90, 0),
                    default => $image
                };
            }
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
            @mkdir($targetDir, 0777, true);
        }

        // Generate cryptographically secure randomized filename
        $randomHash = bin2hex(random_bytes(16));
        $newFilename = "mat_img_{$randomHash}.webp";
        $destination = rtrim($targetDir, '/') . '/' . $newFilename;

        $success = false;
        if (function_exists('imagewebp')) {
            $success = @imagewebp($image, $destination, max(1, min(100, $quality)));
        }

        if (!$success) {
            // Fallback to JPEG if WebP conversion fails on host
            $newFilename = "mat_img_{$randomHash}.jpg";
            $destination = rtrim($targetDir, '/') . '/' . $newFilename;
            $success = @imagejpeg($image, $destination, max(1, min(100, $quality)));
        }

        imagedestroy($image);

        return $success ? $newFilename : null;
    }
}
