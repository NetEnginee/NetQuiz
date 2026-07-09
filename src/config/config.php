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

$isLocal = ($host === 'localhost:8080' || $host === '127.0.0.1:8080');

if ($isLocal) {
    return [
        'app_name' => 'RouterOS Quiz (Dev)',
        'base_url' => $base_url,
        // Database Config for Docker Local Dev
        'db_host' => getenv('DB_HOST') ?: 'nvram-mysql',
        'db_name' => getenv('DB_NAME') ?: 'db_mikrotik_quiz',
        'db_user' => getenv('DB_USER') ?: 'operator_winbox',
        'db_pass' => getenv('DB_PASS') ?: 'password_winbox',
    ];
} else {
    return [
        'app_name' => 'RouterOS Quiz Academy',
        'base_url' => $base_url,
        // Database Config for InfinityFree Production Shared Hosting
        'db_host' => 'sql213.infinityfree.com',
        'db_name' => 'if0_42364306_db_mikrotik_quiz',
        'db_user' => 'if0_42364306',
        'db_pass' => 'routerosquiz123',
    ];
}