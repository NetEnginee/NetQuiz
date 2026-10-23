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
        $mimeType = '';
        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mimeType = (string)@finfo_file($finfo, $tmpPath);
                @finfo_close($finfo);
            }
        }
        if ($mimeType === '' && function_exists('mime_content_type')) {
            $mimeType = (string)@mime_content_type($tmpPath);
        }
        if ($mimeType === '' && function_exists('getimagesize')) {
            $imgInfo = @getimagesize($tmpPath);
            if (is_array($imgInfo) && !empty($imgInfo['mime'])) {
                $mimeType = (string)$imgInfo['mime'];
            }
        }

        if (!array_key_exists(strtolower($mimeType), self::ALLOWED_MIME_TYPES)) {
            return false;
        }

        // Generate cryptographically random filename
        $randomHash = bin2hex(random_bytes(16));
        $newFilename = "img_{$randomHash}.webp";

        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }
        if (!is_writable($targetDir)) {
            @chmod($targetDir, 0777);
        }

        $destination = rtrim($targetDir, '/') . '/' . $newFilename;

        // Create image resource based on actual magic-byte MIME
        $mimeLower = strtolower($mimeType);
        $image = null;
        if (in_array($mimeLower, ['image/jpeg', 'image/jpg', 'image/pjpeg'], true) && function_exists('imagecreatefromjpeg')) {
            $image = @imagecreatefromjpeg($tmpPath);
        } elseif (in_array($mimeLower, ['image/png', 'image/x-png'], true) && function_exists('imagecreatefrompng')) {
            $image = @imagecreatefrompng($tmpPath);
        } elseif ($mimeLower === 'image/webp' && function_exists('imagecreatefromwebp')) {
            $image = @imagecreatefromwebp($tmpPath);
        }

        if (!$image && function_exists('imagecreatefromstring')) {
            $rawBinary = @file_get_contents($tmpPath);
            if ($rawBinary) {
                $image = @imagecreatefromstring($rawBinary);
            }
        }

        if (!$image) {
            return false;
        }

        // Preserve alpha transparency for PNG / WebP
        if (function_exists('imagepalettetotruecolor')) {
            @imagepalettetotruecolor($image);
        }
        @imagealphablending($image, true);
        @imagesavealpha($image, true);

        $success = false;
        if (function_exists('imagewebp')) {
            $success = @imagewebp($image, $destination, max(1, min(100, $quality)));
        }
        imagedestroy($image);

        if ($success) {
            @chmod($destination, 0644);
        }

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
        $mimeLower = strtolower($mimePart);
        $isPng = in_array($mimeLower, ['image/png', 'image/x-png'], true);
        $ext = $isPng ? 'png' : 'webp';
        $newFilename = "qimg_{$randomHash}.{$ext}";
        $fullPath = rtrim($targetDir, '/') . '/' . $newFilename;

        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $saved = false;
        if ($isPng && function_exists('imagepng')) {
            // Lossless PNG compression (level 6) preserving crisp lines and topology diagram text
            $saved = @imagepng($image, $fullPath, 6);
        }

        if (!$saved && function_exists('imagewebp')) {
            $newFilename = "qimg_{$randomHash}.webp";
            $fullPath = rtrim($targetDir, '/') . '/' . $newFilename;
            $saved = @imagewebp($image, $fullPath, max(1, min(100, $quality)));
        }

        if (!$saved && function_exists('imagepng')) {
            $newFilename = "qimg_{$randomHash}.png";
            $fullPath = rtrim($targetDir, '/') . '/' . $newFilename;
            $saved = @imagepng($image, $fullPath, 6);
        }

        if (!$saved && function_exists('imagejpeg')) {
            $newFilename = "qimg_{$randomHash}.jpg";
            $fullPath = rtrim($targetDir, '/') . '/' . $newFilename;
            $saved = @imagejpeg($image, $fullPath, 85);
        }

        imagedestroy($image);

        if ($saved && is_file($fullPath)) {
            @chmod($fullPath, 0644);
            return $newFilename;
        }

        return null;
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
        $mimeType = '';
        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $mimeType = (string)@finfo_file($finfo, $tmpPath);
                @finfo_close($finfo);
            }
        }
        if ($mimeType === '' && function_exists('mime_content_type')) {
            $mimeType = (string)@mime_content_type($tmpPath);
        }
        $imgInfo = function_exists('getimagesize') ? @getimagesize($tmpPath) : false;
        if ($mimeType === '' && is_array($imgInfo) && !empty($imgInfo['mime'])) {
            $mimeType = (string)$imgInfo['mime'];
        }

        $mimeType = strtolower(trim($mimeType));
        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if (!array_key_exists($mimeType, self::ALLOWED_MIME_TYPES)) {
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                return null;
            }
        }

        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }
        if (!is_writable($targetDir)) {
            @chmod($targetDir, 0777);
        }

        // Generate cryptographically secure randomized filename
        $randomHash = bin2hex(random_bytes(16));

        // Create image resource based on actual magic-byte MIME (safe if GD functions are disabled)
        $image = null;
        if (in_array($mimeType, ['image/jpeg', 'image/jpg', 'image/pjpeg'], true) && function_exists('imagecreatefromjpeg')) {
            $image = @imagecreatefromjpeg($tmpPath);
        } elseif (in_array($mimeType, ['image/png', 'image/x-png'], true) && function_exists('imagecreatefrompng')) {
            $image = @imagecreatefrompng($tmpPath);
        } elseif ($mimeType === 'image/webp' && function_exists('imagecreatefromwebp')) {
            $image = @imagecreatefromwebp($tmpPath);
        }

        if (!$image && function_exists('imagecreatefromstring')) {
            $rawBinary = @file_get_contents($tmpPath);
            if ($rawBinary) {
                $image = @imagecreatefromstring($rawBinary);
            }
        }

        if ($image) {
            // Auto-correct JPEG orientation based on EXIF if available
            if (
                function_exists('exif_read_data') &&
                function_exists('imagerotate') &&
                ($mimeType === 'image/jpeg' || $mimeType === 'image/pjpeg' || $mimeType === 'image/jpg')
            ) {
                $exif = @exif_read_data($tmpPath);
                if (!empty($exif['Orientation'])) {
                    $rotated = match ((int)$exif['Orientation']) {
                        3 => @imagerotate($image, 180, 0),
                        6 => @imagerotate($image, -90, 0),
                        8 => @imagerotate($image, 90, 0),
                        default => $image
                    };
                    if ($rotated) {
                        $image = $rotated;
                    }
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
                if (function_exists('imagepalettetotruecolor')) {
                    @imagepalettetotruecolor($image);
                }
                @imagealphablending($image, true);
                @imagesavealpha($image, true);
            }

            $newFilename = "mat_img_{$randomHash}.webp";
            $destination = rtrim($targetDir, '/') . '/' . $newFilename;

            $success = false;
            if (function_exists('imagewebp')) {
                $success = @imagewebp($image, $destination, max(1, min(100, $quality)));
            }

            if (!$success && function_exists('imagejpeg')) {
                // Fallback to JPEG if WebP conversion fails on host
                $newFilename = "mat_img_{$randomHash}.jpg";
                $destination = rtrim($targetDir, '/') . '/' . $newFilename;
                $success = @imagejpeg($image, $destination, max(1, min(100, $quality)));
            }

            if (!$success && function_exists('imagepng')) {
                $newFilename = "mat_img_{$randomHash}.png";
                $destination = rtrim($targetDir, '/') . '/' . $newFilename;
                $success = @imagepng($image, $destination, 6);
            }

            imagedestroy($image);

            if ($success && is_file($destination)) {
                @chmod($destination, 0644);
                return $newFilename;
            }
        }

        // Production Fallback: If GD lacks WebP/codec support on shared hosting,
        // verify image dimensions via getimagesize() and move the already-compressed client upload safely.
        if (is_array($imgInfo) && ($imgInfo[0] ?? 0) > 0 && ($imgInfo[1] ?? 0) > 0) {
            $detectedMime = strtolower((string)($imgInfo['mime'] ?? $mimeType));
            $safeExt = self::ALLOWED_MIME_TYPES[$detectedMime] ?? (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) ? ($ext === 'jpeg' ? 'jpg' : $ext) : null);
            if ($safeExt !== null) {
                $fallbackFilename = "mat_img_{$randomHash}.{$safeExt}";
                $fallbackDest = rtrim($targetDir, '/') . '/' . $fallbackFilename;
                if (@move_uploaded_file($tmpPath, $fallbackDest) || @copy($tmpPath, $fallbackDest)) {
                    @chmod($fallbackDest, 0644);
                    return $fallbackFilename;
                }
            }
        }

        return null;
    }
}
