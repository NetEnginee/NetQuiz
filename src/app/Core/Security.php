<?php

declare(strict_types=1);

namespace App\Core;

class Security
{
    /**
     * Generate a CSRF token and store it in the session.
     */
    public static function generateCsrfToken(bool $force = false): string
    {
        if ($force || empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Render a hidden CSRF input field for forms.
     */
    public static function csrfField(): string
    {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . self::escape($token) . '">';
    }

    /**
     * Validate CSRF token from POST or JSON body.
     */
    public static function validateCsrfToken(?string $token = null): bool
    {
        if ($token === null) {
            // Check request headers (common in AJAX requests) or POST body
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (empty($token)) {
                // If it is a JSON request, we can extract from raw input
                $input = json_decode(file_get_contents('php://input'), true);
                $token = $input['csrf_token'] ?? '';
            }
        }

        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Check if email belongs to Administrator.
     */
    public static function isAdminEmail(?string $email): bool
    {
        if (empty($email)) {
            return false;
        }
        $cleanEmail = strtolower(trim($email));
        return in_array($cleanEmail, [
            'super@netquiz.academy',
            'admin@routerosquiz.academy',
            'admin@quiz.local'
        ], true);
    }

    /**
     * Get the current user's role.
     */
    public static function getCurrentRole(): Role
    {
        if (!isset($_SESSION['user'])) {
            return Role::GUEST;
        }
        $email = isset($_SESSION['user']['email']) ? trim($_SESSION['user']['email']) : '';
        return self::isAdminEmail($email) ? Role::ADMIN : Role::USER;
    }

    /**
     * Escape output for HTML context (anti-XSS).
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Escape output for use inside a JavaScript context.
     */
    public static function escapeJs(string $value): string
    {
        return json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
    }

    /**
     * Set secure HTTP response headers.
     */
    public static function setSecurityHeaders(): void
    {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://unpkg.com https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self' https://unpkg.com;");
    }

    /**
     * Configure headers to allow browser BFCache (Back/Forward Cache) for non-sensitive pages.
     */
    public static function allowBFCache(): void
    {
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: no-cache');
    }

    /**
     * Configure headers to completely block caching (e.g. for dynamic authenticated pages, logout, or forms).
     */
    public static function preventBFCache(): void
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    /**
     * Validate email format.
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate password meets minimum requirements.
     */
    public static function isValidPassword(string $password): array
    {
        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = 'Password minimal 8 karakter.';
        }
        if (strlen($password) > 128) {
            $errors[] = 'Password maksimal 128 karakter.';
        }
        return $errors;
    }

    /**
     * Sanitize and validate string input.
     */
    public static function sanitizeString(string $input, int $maxLength = 255): string
    {
        $input = trim($input);
        if (strlen($input) > $maxLength) {
            $input = substr($input, 0, $maxLength);
        }
        return $input;
    }

    /**
     * Get secret key for cryptography.
     */
    private static function getSecretKey(): string
    {
        $secret = getenv('APP_KEY') ?: 'RouterOS-Quiz-Academy-Secret-Key-1298471';
        return hash('sha256', $secret, true);
    }

    /**
     * Encrypt a value securely for URL usage using Authenticated Encryption (AES-256-GCM).
     */
    public static function encryptUrlId(string|int $value): string
    {
        $plaintext = (string) $value;
        $cipher = 'aes-256-gcm';
        $key = self::getSecretKey();
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = random_bytes($ivlen);
        $tag = '';

        $ciphertext = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag, '', 16);

        // Format: [1 byte version (1)] + [12 bytes IV] + [16 bytes Tag] + [Ciphertext]
        $data = "\x01" . $iv . $tag . $ciphertext;
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    /**
     * Decrypt a URL-safe encrypted value back to its original value.
     * Supports both modern AES-256-GCM and legacy AES-256-CBC payloads.
     */
    public static function decryptUrlId(?string $encrypted): ?string
    {
        if (empty($encrypted)) {
            return null;
        }

        // Decode base64 URL-safe
        $data = base64_decode(str_replace(['-', '_'], ['+', '/'], $encrypted), true);
        if ($data === false || strlen($data) < 16) {
            return null;
        }

        $key = self::getSecretKey();

        // Check for version 1 (AES-256-GCM)
        if ($data[0] === "\x01" && strlen($data) >= 29) {
            $ivlen = 12; // Standard GCM IV length
            $taglen = 16;
            $iv = substr($data, 1, $ivlen);
            $tag = substr($data, 1 + $ivlen, $taglen);
            $ciphertext = substr($data, 1 + $ivlen + $taglen);

            $decrypted = openssl_decrypt($ciphertext, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
            if ($decrypted !== false) {
                return $decrypted;
            }
        }

        // Fallback for legacy AES-256-CBC payloads
        $legacyKey = hash('sha256', 'RouterOS-Quiz-Academy-Secret-Key-1298471');
        $cipher = 'AES-256-CBC';
        $ivlen = openssl_cipher_iv_length($cipher);
        if (strlen($data) >= $ivlen) {
            $iv = substr($data, 0, $ivlen);
            $ciphertext = substr($data, $ivlen);
            $decrypted = openssl_decrypt($ciphertext, $cipher, $legacyKey, OPENSSL_RAW_DATA, $iv);
            if ($decrypted !== false) {
                return $decrypted;
            }
        }

        return null;
    }

    /**
     * Get client real IP address safely.
     */
    public static function getClientIp(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '127.0.0.1';
    }

    /**
     * Check if the account or IP+account is rate-limited (brute force protection).
     * Returns the remaining lock duration in seconds if limited, null otherwise.
     */
    public static function checkRateLimit(string $email, int $maxAttempts = 5, int $decaySeconds = 300): ?int
    {
        $ip = self::getClientIp();
        $email = trim(strtolower($email));
        if (empty($email)) {
            return null;
        }

        try {
            $db = Database::getInstance()->getConnection();

            // Clean up old attempts
            $cleanStmt = $db->prepare("DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL :decay SECOND)");
            $cleanStmt->execute(['decay' => $decaySeconds]);

            // Count attempts for this specific account or IP+account pair in the decay window
            $stmt = $db->prepare("SELECT COUNT(*) FROM login_attempts WHERE (email = :email OR (ip_address = :ip AND email = :email_pair)) AND attempted_at >= DATE_SUB(NOW(), INTERVAL :decay SECOND)");
            $stmt->execute([
                'email' => $email,
                'ip' => $ip,
                'email_pair' => $email,
                'decay' => $decaySeconds
            ]);
            $attempts = (int) $stmt->fetchColumn();

            if ($attempts >= $maxAttempts) {
                // Find how many seconds until the block lifts
                $timeStmt = $db->prepare("SELECT MIN(attempted_at) FROM login_attempts WHERE email = :email AND attempted_at >= DATE_SUB(NOW(), INTERVAL :decay SECOND)");
                $timeStmt->execute([
                    'email' => $email,
                    'decay' => $decaySeconds
                ]);
                $firstAttempt = $timeStmt->fetchColumn();
                if ($firstAttempt) {
                    $timeLeft = (strtotime($firstAttempt) + $decaySeconds) - time();
                    return $timeLeft > 0 ? $timeLeft : 1;
                }
                return $decaySeconds;
            }
        } catch (\Throwable $e) {
            error_log("Rate limit check failed: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Record a failed login attempt.
     */
    public static function recordLoginAttempt(string $email): void
    {
        $ip = self::getClientIp();
        $email = trim(strtolower($email));
        if (empty($email)) {
            return;
        }

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO login_attempts (ip_address, email) VALUES (:ip, :email)");
            $stmt->execute([
                'ip' => $ip,
                'email' => $email
            ]);
        } catch (\Throwable $e) {
            error_log("Failed to record login attempt: " . $e->getMessage());
        }
    }

    /**
     * Clear login attempts upon successful login for this account.
     */
    public static function clearLoginAttempts(string $email): void
    {
        $email = trim(strtolower($email));
        if (empty($email)) {
            return;
        }

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM login_attempts WHERE email = :email");
            $stmt->execute([
                'email' => $email
            ]);
        } catch (\Throwable $e) {
            error_log("Failed to clear login attempts: " . $e->getMessage());
        }
    }
}
