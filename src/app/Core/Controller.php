<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Base Controller.
 * Provides helper methods for rendering views, returning JSON responses, and redirects.
 */
abstract class Controller
{
    /**
     * Renders a view file and injects variables into it.
     *
     * @param string $view Name of the view file (e.g. 'home/index')
     * @param array<string, mixed> $data Associative array of data to be extracted to variables
     */
    protected function view(string $view, array $data = []): Response
    {
        return Response::view($view, $data);
    }

    /**
     * Returns a JSON response.
     *
     * @param array<string, mixed> $data Data to be encoded to JSON
     * @param int $statusCode HTTP response status code (default 200)
     */
    protected function jsonResponse(array $data, int $statusCode = 200): Response
    {
        return Response::json($data, $statusCode);
    }

    /**
     * Returns a redirect response.
     */
    protected function redirect(string $url, int $statusCode = 302): Response
    {
        return Response::redirect($url, $statusCode);
    }
}
