<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Modern HTTP Request Abstraction.
 * Encapsulates input parameters, headers, cookies, server parameters, and JSON payloads.
 */
class Request
{
    private array $get;
    private array $post;
    private array $server;
    private array $files;
    private array $cookies;
    private ?array $json = null;
    private string $rawBody;

    public function __construct(
        ?array $get = null,
        ?array $post = null,
        ?array $server = null,
        ?array $files = null,
        ?array $cookies = null,
        ?string $rawBody = null
    ) {
        $this->get = $get ?? $_GET;
        $this->post = $post ?? $_POST;
        $this->server = $server ?? $_SERVER;
        $this->files = $files ?? $_FILES;
        $this->cookies = $cookies ?? $_COOKIE;
        $this->rawBody = $rawBody ?? (string) file_get_contents('php://input');

        if ($this->isJson() && !empty($this->rawBody)) {
            $decoded = json_decode($this->rawBody, true);
            if (is_array($decoded)) {
                $this->json = $decoded;
            }
        }
    }

    /**
     * Create a Request instance from global superglobals.
     */
    public static function createFromGlobals(): self
    {
        return new self();
    }

    /**
     * Get the HTTP request method in uppercase.
     */
    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Check if the HTTP method matches.
     */
    public function isMethod(string $method): bool
    {
        return $this->getMethod() === strtoupper($method);
    }

    /**
     * Get the sanitized request URI path without query string.
     */
    public function getPath(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        return '/' . trim($path, '/');
    }

    /**
     * Get full request URL.
     */
    public function getFullUrl(): string
    {
        $protocol = $this->isSecure() ? 'https' : 'http';
        $host = $this->server['HTTP_HOST'] ?? 'localhost';
        $uri = $this->server['REQUEST_URI'] ?? '/';
        return "{$protocol}://{$host}{$uri}";
    }

    /**
     * Check if connection is HTTPS / Secure.
     */
    public function isSecure(): bool
    {
        return (
            (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') ||
            (isset($this->server['HTTP_X_FORWARDED_PROTO']) && $this->server['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (isset($this->server['SERVER_PORT']) && (int)$this->server['SERVER_PORT'] === 443)
        );
    }

    /**
     * Check if request expects or contains JSON payload.
     */
    public function isJson(): bool
    {
        $contentType = $this->server['CONTENT_TYPE'] ?? $this->server['HTTP_CONTENT_TYPE'] ?? '';
        return str_contains(strtolower($contentType), 'application/json');
    }

    /**
     * Check if request was made via AJAX / XMLHttpRequest.
     */
    public function isAjax(): bool
    {
        return (isset($this->server['HTTP_X_REQUESTED_WITH']) &&
            strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    /**
     * Retrieve input item from JSON, POST, or GET in priority order.
     */
    public function input(string $key, mixed $default = null): mixed
    {
        if ($this->json !== null && array_key_exists($key, $this->json)) {
            return $this->json[$key];
        }

        if (array_key_exists($key, $this->post)) {
            return $this->post[$key];
        }

        if (array_key_exists($key, $this->get)) {
            return $this->get[$key];
        }

        return $default;
    }

    /**
     * Get all merged inputs (JSON + POST + GET).
     */
    public function all(): array
    {
        $data = $this->get;
        $data = array_merge($data, $this->post);
        if ($this->json !== null) {
            $data = array_merge($data, $this->json);
        }
        return $data;
    }

    /**
     * Get query string parameter from $_GET.
     */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->get[$key] ?? $default;
    }

    /**
     * Get body parameter from $_POST.
     */
    public function post(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Get uploaded file by key from $_FILES.
     */
    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Check if file exists and has no upload errors.
     */
    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) &&
            is_array($this->files[$key]) &&
            ($this->files[$key]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;
    }

    /**
     * Get request header value.
     */
    public function header(string $name, ?string $default = null): ?string
    {
        $normalized = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if (isset($this->server[$normalized])) {
            return $this->server[$normalized];
        }

        if ($name === 'CONTENT_TYPE' || $name === 'Content-Type') {
            return $this->server['CONTENT_TYPE'] ?? $this->server['HTTP_CONTENT_TYPE'] ?? $default;
        }

        return $default;
    }

    /**
     * Get Bearer token from Authorization header if present.
     */
    public function bearerToken(): ?string
    {
        $header = $this->header('Authorization') ?? $this->server['HTTP_AUTHORIZATION'] ?? '';
        if (preg_match('/Bearer\s(\S+)/i', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Get client real IP address safely.
     */
    public function getClientIp(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if (!empty($this->server[$header])) {
                $ips = explode(',', $this->server[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return '127.0.0.1';
    }

    /**
     * Get raw request body string.
     */
    public function getRawBody(): string
    {
        return $this->rawBody;
    }
}
