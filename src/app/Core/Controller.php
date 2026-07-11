<?php
declare(strict_types=1);

namespace App\Core;

class Controller
{
    /**
     * Renders a view file and injects variables into it.
     *
     * @param string $view Name of the view file (e.g. 'home/index')
     * @param array $data Associative array of data to be extracted to variables
     */
    protected function view(string $view, array $data = []): void
    {
        // Make data available to view without extract()
        foreach ($data as $__key => $__value) {
            $$__key = $__value;
        }
        unset($__key, $__value);
        require_once APP_ROOT . '/Views/' . $view . '.php';
    }

    /**
     * Sends a JSON response and terminates execution.
     *
     * @param array $data Data to be encoded to JSON
     * @param int $statusCode HTTP response status code (default 200)
     */
    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
