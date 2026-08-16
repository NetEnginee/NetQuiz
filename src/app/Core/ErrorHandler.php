<?php
declare(strict_types=1);

namespace App\Core;

use Throwable;
use ErrorException;

/**
 * Centralized Global Error and Exception Handler.
 * Intercepts PHP notices, warnings, fatal errors, and unhandled exceptions.
 */
class ErrorHandler
{
    private static string $logFile = '';
    private static bool $displayErrors = false;

    /**
     * Register global error, exception, and shutdown handlers.
     */
    public static function register(string $logFilePath, bool $displayErrors = false): void
    {
        self::$logFile = $logFilePath;
        self::$displayErrors = $displayErrors;

        // Ensure log directory exists
        $dir = dirname($logFilePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Set error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', $displayErrors ? '1' : '0');
        ini_set('log_errors', '1');
        ini_set('error_log', $logFilePath);

        // Register handlers
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Convert PHP errors into ErrorException.
     */
    public static function handleError(int $level, string $message, string $file = '', int $line = 0): bool
    {
        if (!(error_reporting() & $level)) {
            return false;
        }

        throw new ErrorException($message, 0, $level, $file, $line);
    }

    /**
     * Handle uncaught exceptions gracefully.
     */
    public static function handleException(Throwable $exception): void
    {
        self::logException($exception);

        if (PHP_SAPI === 'cli') {
            echo "\n[ERROR] " . $exception->getMessage() . "\n";
            echo "In " . $exception->getFile() . ":" . $exception->getLine() . "\n";
            exit(1);
        }

        $statusCode = ($exception->getCode() >= 400 && $exception->getCode() < 600) ? (int)$exception->getCode() : 500;

        if (!headers_sent()) {
            http_response_code($statusCode);
        }

        $isJson = (
            (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) ||
            (isset($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json')) ||
            (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        );

        if ($isJson) {
            header('Content-Type: application/json; charset=utf-8');
            $payload = [
                'status' => 'error',
                'message' => self::$displayErrors ? $exception->getMessage() : 'Terjadi kesalahan sistem internal. Silakan hubungi administrator.',
            ];
            if (self::$displayErrors) {
                $payload['file'] = $exception->getFile();
                $payload['line'] = $exception->getLine();
                $payload['trace'] = $exception->getTraceAsString();
            }
            echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            exit;
        }

        // Render HTML error view
        $title = '500 - Kesalahan Server Internal';
        $viewFile = APP_ROOT . '/Views/errors/500.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "<!DOCTYPE html><html lang='id'><head><title>500 - Internal Error</title></head><body style='font-family:sans-serif;text-align:center;padding:50px;'><h1>500 - Kesalahan Server Internal</h1><p>Terjadi gangguan pada sistem. Silakan coba kembali beberapa saat lagi.</p></body></html>";
        }
        exit;
    }

    /**
     * Handle fatal PHP errors upon shutdown.
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE], true)) {
            self::handleException(new ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            ));
        }
    }

    /**
     * Log exception with sanitized context.
     */
    public static function logException(Throwable $e): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $type = get_class($e);
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $trace = $e->getTraceAsString();

        // Sanitize sensitive strings (passwords, tokens)
        $message = preg_replace('/(password|db_pass|token|csrf_token)=[^&\s]+/i', '$1=[REDACTED]', $message);

        $logEntry = "[{$timestamp}] {$type}: {$message} in {$file}:{$line}\nStack trace:\n{$trace}\n\n";

        if (!empty(self::$logFile)) {
            error_log($logEntry, 3, self::$logFile);
        } else {
            error_log($logEntry);
        }
    }
}
