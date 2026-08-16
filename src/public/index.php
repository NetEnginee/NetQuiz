<?php
declare(strict_types=1);

// 1. Session Configuration & Hardening
if (session_status() === PHP_SESSION_NONE) {
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
        'lifetime' => 86400, // 24 hours
        'path' => '/',
        'domain' => '',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    session_cache_limiter('');
    session_start();
}

// 2. Session Inactivity Timeout (30 minutes)
$inactivityTimeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactivityTimeout)) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

// 3. PSR-4 Autoloader Implementation
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// 4. Global Constants
$config = require_once dirname(__DIR__) . '/config/config.php';
define('BASE_URL', $config['base_url']);
define('APP_NAME', $config['app_name']);
define('APP_ROOT', dirname(__DIR__) . '/app');
define('PUBLIC_ROOT', __DIR__);

// 5. Centralized Error & Exception Handler
use App\Core\ErrorHandler;
ErrorHandler::register(dirname(__DIR__) . '/logs/php_errors.log', false);

// 6. Security Headers
use App\Core\Security;
Security::setSecurityHeaders();
Security::allowBFCache();

// 7. Dependency Injection Container Setup & Service Bindings
use App\Core\Container;
use App\Core\Database;
use App\Core\Request;
use App\Core\Router;
use App\Repositories\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\QuizRepositoryInterface;
use App\Repositories\QuizRepository;
use App\Repositories\QuestionRepositoryInterface;
use App\Repositories\QuestionRepository;
use App\Repositories\AttemptRepositoryInterface;
use App\Repositories\AttemptRepository;
use App\Repositories\MaterialRepositoryInterface;
use App\Repositories\MaterialRepository;
use App\Repositories\BadgeRepositoryInterface;
use App\Repositories\BadgeRepository;

$container = Container::getInstance();

// Bind Core Singletons
$container->singleton(Database::class, fn() => new Database($config));
$container->singleton(Request::class, fn() => Request::createFromGlobals());

// Bind Repositories (Interface to Implementation)
$container->singleton(UserRepositoryInterface::class, UserRepository::class);
$container->singleton(QuizRepositoryInterface::class, QuizRepository::class);
$container->singleton(QuestionRepositoryInterface::class, QuestionRepository::class);
$container->singleton(AttemptRepositoryInterface::class, AttemptRepository::class);
$container->singleton(MaterialRepositoryInterface::class, MaterialRepository::class);
$container->singleton(BadgeRepositoryInterface::class, BadgeRepository::class);

// 8. Router Definition & Route Registry
$router = new Router($container);

// Public & Student Routes
$router->get('/', [\App\Controllers\HomeController::class, 'index']);
$router->get('/dashboard', [\App\Controllers\HomeController::class, 'dashboardRedirect']);
$router->get('/login', [\App\Controllers\AuthController::class, 'index']);
$router->post('/api/login', [\App\Controllers\AuthController::class, 'apiLogin']);
$router->get('/logout', [\App\Controllers\AuthController::class, 'logout']);
$router->get('/leaderboard', [\App\Controllers\LeaderboardController::class, 'index']);
$router->get('/quiz', [\App\Controllers\QuizController::class, 'index']);
$router->get('/quiz/play/{id}', [\App\Controllers\QuizController::class, 'play']);
$router->post('/quiz/pause/{id}', [\App\Controllers\QuizController::class, 'pause']);
$router->post('/quiz/submit/{id}', [\App\Controllers\QuizController::class, 'submit']);
$router->get('/quiz/result/{id}', [\App\Controllers\QuizController::class, 'result']);
$router->get('/quiz/review/{id}', [\App\Controllers\QuizController::class, 'review']);
$router->get('/learn', [\App\Controllers\LearnController::class, 'index']);
$router->get('/learn/{id}', [\App\Controllers\LearnController::class, 'viewMaterial']);

// Admin Console Routes
$router->get('/admin', [\App\Controllers\AdminController::class, 'index']);
$router->post('/admin/quizzes', [\App\Controllers\AdminController::class, 'createQuiz']);
$router->post('/admin/quiz/create', [\App\Controllers\AdminController::class, 'createQuiz']);
$router->post('/admin/quizzes/delete/{id}', [\App\Controllers\AdminController::class, 'deleteQuiz']);
$router->post('/admin/quiz/delete/{id}', [\App\Controllers\AdminController::class, 'deleteQuiz']);

$router->post('/admin/users/create', [\App\Controllers\AdminController::class, 'createMember']);
$router->post('/admin/member/create', [\App\Controllers\AdminController::class, 'createMember']);
$router->post('/admin/users/update/{id}', [\App\Controllers\AdminController::class, 'updateMember']);
$router->post('/admin/member/update/{id}', [\App\Controllers\AdminController::class, 'updateMember']);
$router->post('/admin/users/delete/{id}', [\App\Controllers\AdminController::class, 'deleteMember']);
$router->post('/admin/member/delete/{id}', [\App\Controllers\AdminController::class, 'deleteMember']);
$router->post('/admin/users/suspend/{id}', [\App\Controllers\AdminController::class, 'suspendMember']);
$router->post('/admin/member/suspend/{id}', [\App\Controllers\AdminController::class, 'suspendMember']);

$router->post('/admin/badges/create', [\App\Controllers\AdminController::class, 'createBadge']);
$router->post('/admin/badge/create', [\App\Controllers\AdminController::class, 'createBadge']);
$router->post('/admin/badges/delete/{id}', [\App\Controllers\AdminController::class, 'deleteBadge']);
$router->post('/admin/badge/delete/{id}', [\App\Controllers\AdminController::class, 'deleteBadge']);
$router->post('/admin/badge/delete-bulk', [\App\Controllers\AdminController::class, 'deleteBadgesBulk']);

$router->post('/admin/materials/create', [\App\Controllers\AdminController::class, 'createMaterial']);
$router->post('/admin/material/create', [\App\Controllers\AdminController::class, 'createMaterial']);
$router->post('/admin/materials/update/{id}', [\App\Controllers\AdminController::class, 'updateMaterial']);
$router->post('/admin/material/update/{id}', [\App\Controllers\AdminController::class, 'updateMaterial']);
$router->get('/admin/material/get/{id}', [\App\Controllers\AdminController::class, 'getMaterialJson']);
$router->post('/admin/materials/delete/{id}', [\App\Controllers\AdminController::class, 'deleteMaterial']);
$router->post('/admin/material/delete/{id}', [\App\Controllers\AdminController::class, 'deleteMaterial']);

// 9. Dispatch Request via Router
$router->dispatch();
