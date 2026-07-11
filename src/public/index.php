<?php
declare(strict_types=1);
// Enforce Session Security and Expiration
if (session_status() === PHP_SESSION_NONE) {
    // Set secure PHP configuration options
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');

    $isSecure = (
        (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    );

    if ($isSecure) {
        ini_set('session.cookie_secure', '1');
    }

    session_set_cookie_params([
        'lifetime' => 86400, // Cookie expires in 1 day
        'path' => '/',
        'domain' => '',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    // Prevent PHP from sending default cache-control headers (avoiding automatic no-store)
    session_cache_limiter('');

    // Start session
    session_start();
}

// Enforce server-side session inactivity timeout (e.g., 30 minutes)
$inactivityTimeout = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactivityTimeout)) {
    // Clear and regenerate session
    session_unset();
    session_destroy();

    // Restart empty session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
$_SESSION['last_activity'] = time();

// Error handling: Disable display in production, log instead
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('error_log', dirname(__DIR__) . '/logs/php_errors.log');

// Custom Autoloader mapping PSR-4 namespace "App" to "app" directory
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = dirname(__DIR__) . '/app/';

    // Check if class uses prefix
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Load configs
$config = require_once dirname(__DIR__) . '/config/config.php';
define('BASE_URL', $config['base_url']);
define('APP_NAME', $config['app_name']);
define('APP_ROOT', dirname(__DIR__) . '/app');
define('PUBLIC_ROOT', __DIR__);

// Load Security and set headers
require_once dirname(__DIR__) . '/app/Core/Security.php';
\App\Core\Security::setSecurityHeaders();
\App\Core\Security::allowBFCache(); // Enable BFCache by default for all pages unless explicitly disabled

// Instantiate Router
use App\Core\Router;

$router = new Router();

// Define Routes
$router->get('/', [\App\Controllers\HomeController::class, 'index']);
$router->get('/dashboard', [\App\Controllers\HomeController::class, 'dashboardRedirect']);
$router->get('/login', [\App\Controllers\AuthController::class, 'index']);
$router->post('/api/login', [\App\Controllers\AuthController::class, 'apiLogin']);
$router->get('/logout', [\App\Controllers\AuthController::class, 'logout']);
$router->get('/settings', [\App\Controllers\SettingsController::class, 'index']);
$router->get('/leaderboard', [\App\Controllers\LeaderboardController::class, 'index']);
$router->get('/quiz', [\App\Controllers\QuizController::class, 'index']);
$router->get('/admin', [\App\Controllers\AdminController::class, 'index']);
$router->post('/admin/quiz/create', [\App\Controllers\AdminController::class, 'createQuiz']);
$router->post('/admin/quiz/delete/{id}', [\App\Controllers\AdminController::class, 'deleteQuiz']);
$router->post('/admin/member/create', [\App\Controllers\AdminController::class, 'createMember']);
$router->post('/admin/member/update/{id}', [\App\Controllers\AdminController::class, 'updateMember']);
$router->post('/admin/member/delete/{id}', [\App\Controllers\AdminController::class, 'deleteMember']);
$router->post('/admin/profile/update', [\App\Controllers\AdminController::class, 'updateProfile']);
$router->post('/admin/badge/create', [\App\Controllers\AdminController::class, 'createBadge']);
$router->post('/admin/badge/delete/{id}', [\App\Controllers\AdminController::class, 'deleteBadge']);
$router->post('/admin/badge/delete-bulk', [\App\Controllers\AdminController::class, 'deleteBadgesBulk']);
$router->get('/quiz/play/{id}', [\App\Controllers\QuizController::class, 'play']);
$router->post('/quiz/pause/{id}', [\App\Controllers\QuizController::class, 'pause']);
$router->post('/quiz/submit/{id}', [\App\Controllers\QuizController::class, 'submit']);
$router->get('/quiz/result/{id}', [\App\Controllers\QuizController::class, 'result']);
$router->get('/quiz/review/{id}', [\App\Controllers\QuizController::class, 'review']);
$router->post('/api/settings/profile', [\App\Controllers\SettingsController::class, 'updateProfile']);
$router->post('/api/settings/password', [\App\Controllers\SettingsController::class, 'updatePassword']);

// Dispatch requests
$url = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($url, $method);
