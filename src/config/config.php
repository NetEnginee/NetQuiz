<?php
// Base URL configuration (automatic detection supporting reverse proxies like Cloudflare)
$protocol = 'http';

if (
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
) {
    $protocol = 'https';
}

$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
$base_url = $protocol . '://' . $host;

$hostOnly = explode(':', $host)[0];

$isLocal = (
    $hostOnly === 'localhost' ||
    $hostOnly === '127.0.0.1' ||
    $hostOnly === 'nvram-mysql' ||
    str_starts_with($hostOnly, '192.168.') ||
    str_starts_with($hostOnly, '10.') ||
    str_starts_with($hostOnly, '172.') ||
    str_ends_with($hostOnly, '.local') ||
    str_ends_with($hostOnly, '.test') ||
    str_ends_with($hostOnly, '.lan') ||
    getenv('APP_ENV') === 'local' ||
    (isset($_SERVER['SERVER_PORT']) && in_array((int)$_SERVER['SERVER_PORT'], [8080, 8000, 3000], true))
);

// Optional .env loader if .env file exists in project root or app root
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile) && is_readable($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv("{$name}={$value}");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

if ($isLocal) {
    return [
        'app_name' => getenv('APP_NAME') ?: 'NetQuiz (Dev)',
        'base_url' => getenv('APP_URL') ?: $base_url,
        'app_key' => getenv('APP_KEY') ?: 'NetQuiz-Dev-Secure-Secret-Key-89234710',
        // Database Config for Docker Local Dev
        'db_host' => getenv('DB_HOST') ?: 'nvram-mysql',
        'db_name' => getenv('DB_NAME') ?: 'db_mikrotik_quiz',
        'db_user' => getenv('DB_USER') ?: 'operator_winbox',
        'db_pass' => getenv('DB_PASS') ?: 'password_winbox',
    ];
} else {
    return [
        'app_name' => getenv('APP_NAME') ?: 'NetQuiz Academy',
        'base_url' => getenv('APP_URL') ?: $base_url,
        'app_key' => getenv('APP_KEY') ?: 'NetQuiz-Prod-Secure-Secret-Key-72314981',
        // Database Config for InfinityFree Production Shared Hosting
        'db_host' => getenv('DB_HOST') ?: 'sql301.infinityfree.com',
        'db_name' => getenv('DB_NAME') ?: 'if0_42727530_netquiz',
        'db_user' => getenv('DB_USER') ?: 'if0_42727530',
        'db_pass' => getenv('DB_PASS') ?: '1UnionMzCADHseR',
    ];
}
