<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Modern HTTP Response Abstraction.
 * Supports status codes, custom headers, JSON output, redirects, and clean view rendering.
 */
class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $content = '';

    public function __construct(string $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Set the HTTP status code.
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Get the HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Set a response header.
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Set multiple response headers.
     */
    public function setHeaders(array $headers): self
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }
        return $this;
    }

    /**
     * Set the response body content.
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get the response body content.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Send HTTP status code, headers, and body to the client.
     */
    public function send(): void
    {
        if (isset($this->headers['Content-Type']) && str_contains($this->headers['Content-Type'], 'application/json')) {
            if (ob_get_length()) {
                @ob_clean();
            }
        }

        if (!headers_sent()) {
            http_response_code($this->statusCode);

            foreach ($this->headers as $name => $value) {
                header("{$name}: {$value}");
            }
        }

        echo $this->content;
    }

    /**
     * Factory: Create a JSON response.
     */
    public static function json(array $data, int $statusCode = 200, array $headers = []): self
    {
        $headers['Content-Type'] = 'application/json; charset=utf-8';
        $jsonContent = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return new self($jsonContent !== false ? $jsonContent : '{}', $statusCode, $headers);
    }

    /**
     * Factory: Create a Redirect response.
     */
    public static function redirect(string $url, int $statusCode = 302): self
    {
        $response = new self('', $statusCode);
        $response->setHeader('Location', $url);
        return $response;
    }

    /**
     * Factory: Render a view template into a Response.
     */
    public static function view(string $view, array $data = [], int $statusCode = 200, array $headers = []): self
    {
        ob_start();
        foreach ($data as $__key => $__value) {
            $$__key = $__value;
        }
        unset($__key, $__value);

        $viewPath = APP_ROOT . '/Views/' . ltrim($view, '/') . '.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            throw new \RuntimeException("View template [{$view}] not found at [{$viewPath}].");
        }

        $content = ob_get_clean();
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'text/html; charset=utf-8';
        }

        return new self($content !== false ? $content : '', $statusCode, $headers);
    }
}
