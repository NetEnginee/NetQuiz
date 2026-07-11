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
     * Get the current user's role.
     */
    public static function getCurrentRole(): Role
    {
        if (!isset($_SESSION['user'])) {
            return Role::GUEST;
        }
        $email = isset($_SESSION['user']['email']) ? trim($_SESSION['user']['email']) : '';
        return (strcasecmp($email, 'admin@routerosquiz.academy') === 0) ? Role::ADMIN : Role::USER;
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
        header('Pragma: cache');
    }

    /**
     * Configure headers to completely block caching (e.g. for logout, admin pages, or sensitive forms).
     */
    public static function preventBFCache(): void
    {
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
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
     * Encrypt a value (like an integer ID) securely for URL usage.
     */
    public static function encryptUrlId($value): string
    {
        $plaintext = (string) $value;
        $cipher = 'AES-256-CBC';
        $key = hash('sha256', 'RouterOS-Quiz-Academy-Secret-Key-1298471');
        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = random_bytes($ivlen);
        $ciphertext = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        
        $data = $iv . $ciphertext;
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    /**
     * Decrypt a URL-safe encrypted value back to its original value.
     * Returns null if decryption fails or tampering is detected.
     */
    public static function decryptUrlId(?string $encrypted): ?string
    {
        if (empty($encrypted)) {
            return null;
        }
        
        // Decode base64 URL-safe
        $data = base64_decode(str_replace(['-', '_'], ['+', '/'], $encrypted));
        if ($data === false) {
            return null;
        }

        $cipher = 'AES-256-CBC';
        $key = hash('sha256', 'RouterOS-Quiz-Academy-Secret-Key-1298471');
        $ivlen = openssl_cipher_iv_length($cipher);
        if (strlen($data) < $ivlen) {
            return null;
        }

        $iv = substr($data, 0, $ivlen);
        $ciphertext = substr($data, $ivlen);
        
        $decrypted = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        return $decrypted !== false ? $decrypted : null;
    }
}
