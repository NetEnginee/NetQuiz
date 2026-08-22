<?php
declare(strict_types=1);

/**
 * NetQuiz E2E Test Suite - Core Test Harness & Assertion Engine
 */

namespace NetQuiz\Tests\E2E;

use DOMDocument;
use DOMXPath;
use PDO;
use Exception;
use RuntimeException;

class E2EConfig
{
    public static function getBaseUrl(): string
    {
        if (getenv('NETQUIZ_BASE_URL')) {
            return rtrim(getenv('NETQUIZ_BASE_URL'), '/');
        }
        // Check if running inside container
        if (file_exists('/.dockerenv') || gethostbyname('ether1-nginx') !== 'ether1-nginx') {
            return 'http://ether1-nginx';
        }
        return 'http://localhost:8080';
    }

    public static function getHostHeader(): string
    {
        return 'localhost:8080';
    }

    public static function getDbPdo(): PDO
    {
        $dbHost = getenv('DB_HOST') ?: (file_exists('/.dockerenv') || gethostbyname('nvram-mysql') !== 'nvram-mysql' ? 'nvram-mysql' : '127.0.0.1');
        $dbName = getenv('DB_NAME') ?: 'db_mikrotik_quiz';
        $dbUser = getenv('DB_USER') ?: 'operator_winbox';
        $dbPass = getenv('DB_PASS') ?: 'password_winbox';
        $dbPort = getenv('DB_PORT') ?: '3306';

        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
        return new PDO($dsn, $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5
        ]);
    }
}

class E2EResponse
{
    public int $statusCode;
    public string $body;
    public array $headers;
    public ?string $redirectUrl = null;
    private ?DOMDocument $dom = null;
    private ?DOMXPath $xpath = null;

    public function __construct(int $statusCode, string $body, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;

        foreach ($headers as $k => $v) {
            if (strcasecmp($k, 'Location') === 0) {
                $this->redirectUrl = is_array($v) ? end($v) : $v;
            }
        }
    }

    public function json(): ?array
    {
        $data = json_decode($this->body, true);
        return is_array($data) ? $data : null;
    }

    public function dom(): DOMDocument
    {
        if ($this->dom === null) {
            $this->dom = new DOMDocument();
            // Suppress HTML5 parsing warnings for clean output
            libxml_use_internal_errors(true);
            $this->dom->loadHTML('<?xml encoding="UTF-8">' . $this->body, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            libxml_clear_errors();
        }
        return $this->dom;
    }

    public function xpath(): DOMXPath
    {
        if ($this->xpath === null) {
            $this->xpath = new DOMXPath($this->dom());
        }
        return $this->xpath;
    }
}

class E2EClient
{
    private string $cookieFile;
    private string $baseUrl;
    private string $hostHeader;
    private ?string $lastCsrfToken = null;

    public function __construct(?string $baseUrl = null, ?string $hostHeader = null)
    {
        $this->baseUrl = $baseUrl ?? E2EConfig::getBaseUrl();
        $this->hostHeader = $hostHeader ?? E2EConfig::getHostHeader();
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'netquiz_cookie_');
    }

    public function __destruct()
    {
        if (file_exists($this->cookieFile)) {
            @unlink($this->cookieFile);
        }
    }

    public function resetSession(): void
    {
        if (file_exists($this->cookieFile)) {
            @unlink($this->cookieFile);
        }
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'netquiz_cookie_');
        $this->lastCsrfToken = null;
    }

    public function request(string $method, string $path, array $data = [], array $headers = [], bool $followRedirects = false): E2EResponse
    {
        $url = str_starts_with($path, 'http') ? $path : $this->baseUrl . '/' . ltrim($path, '/');
        $ch = curl_init($url);

        $defaultHeaders = [
            'Host: ' . $this->hostHeader,
            'User-Agent: NetQuiz-E2E-Tester/1.0',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,application/json,*/*;q=0.8'
        ];

        $allHeaders = array_merge($defaultHeaders, $headers);

        $responseHeaders = [];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $allHeaders);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followRedirects);
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $headerLine) use (&$responseHeaders) {
            $len = strlen($headerLine);
            $parts = explode(':', $headerLine, 2);
            if (count($parts) === 2) {
                $responseHeaders[trim($parts[0])] = trim($parts[1]);
            }
            return $len;
        });

        $methodUpper = strtoupper($method);
        if ($methodUpper === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
        } elseif ($methodUpper !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $methodUpper);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
        }

        $body = curl_exec($ch);
        if ($body === false) {
            $err = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException("cURL error requesting {$url}: {$err}");
        }

        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Extract CSRF token if present in HTML body
        if (preg_match('/window\.CSRF_TOKEN\s*=\s*"([a-f0-9]+)"/i', $body, $m)) {
            $this->lastCsrfToken = $m[1];
        } elseif (preg_match('/name="csrf_token"\s+value="([a-f0-9]+)"/i', $body, $m)) {
            $this->lastCsrfToken = $m[1];
        }

        return new E2EResponse($statusCode, $body, $responseHeaders);
    }

    public function get(string $path, array $headers = [], bool $followRedirects = false): E2EResponse
    {
        return $this->request('GET', $path, [], $headers, $followRedirects);
    }

    public function post(string $path, array $data = [], array $headers = [], bool $followRedirects = false): E2EResponse
    {
        return $this->request('POST', $path, $data, $headers, $followRedirects);
    }

    public function fetchCsrfToken(): string
    {
        if ($this->lastCsrfToken !== null) {
            return $this->lastCsrfToken;
        }
        $resp = $this->get('/login');
        if ($this->lastCsrfToken) {
            return $this->lastCsrfToken;
        }
        if (preg_match('/window\.CSRF_TOKEN\s*=\s*"([a-f0-9]+)"/i', $resp->body, $m)) {
            $this->lastCsrfToken = $m[1];
            return $this->lastCsrfToken;
        }
        return '';
    }

    public function getLastCsrfToken(): string
    {
        return $this->lastCsrfToken ?? $this->fetchCsrfToken();
    }

    public function login(string $email, string $password): bool
    {
        $this->resetSession();
        $csrf = $this->fetchCsrfToken();

        $resp = $this->post('/api/login', [
            'csrf_token' => $csrf,
            'email' => $email,
            'password' => $password
        ]);

        $json = $resp->json();
        return ($resp->statusCode === 200 && is_array($json) && ($json['status'] ?? '') === 'success');
    }
}

class TestReporter
{
    private string $currentSuite = '';
    private int $totalAssertions = 0;
    private int $passedAssertions = 0;
    private int $failedAssertions = 0;
    private array $failures = [];
    private float $suiteStartTime = 0;
    private float $tierStartTime = 0;
    private array $tierResults = [];

    public function __construct()
    {
        $this->suiteStartTime = microtime(true);
    }

    public function startTier(string $tierName, string $description): void
    {
        $this->currentSuite = $tierName;
        $this->tierStartTime = microtime(true);
        echo "\n\033[1;34m======================================================================\033[0m\n";
        echo "\033[1;36m>> RUNNING {$tierName}: {$description}\033[0m\n";
        echo "\033[1;34m======================================================================\033[0m\n";
    }

    public function endTier(): array
    {
        $duration = microtime(true) - $this->tierStartTime;
        $tierSummary = [
            'tier' => $this->currentSuite,
            'duration' => round($duration, 3),
            'assertions' => $this->totalAssertions,
            'passed' => $this->passedAssertions,
            'failed' => $this->failedAssertions
        ];
        $this->tierResults[$this->currentSuite] = $tierSummary;
        
        $color = ($this->failedAssertions === 0) ? "\033[1;32m" : "\033[1;31m";
        echo "{$color}✓ Completed {$this->currentSuite} in " . round($duration, 3) . "s\033[0m\n";
        return $tierSummary;
    }

    public function assert(bool $condition, string $testDescription, string $details = ''): void
    {
        $this->totalAssertions++;
        if ($condition) {
            $this->passedAssertions++;
            echo "  \033[32m✔\033[0m {$testDescription}\n";
        } else {
            $this->failedAssertions++;
            $failureInfo = [
                'tier' => $this->currentSuite,
                'test' => $testDescription,
                'details' => $details
            ];
            $this->failures[] = $failureInfo;
            echo "  \033[1;31m✘ [FAILED]\033[0m {$testDescription}\n";
            if ($details) {
                echo "    \033[33m└─ Reason: {$details}\033[0m\n";
            }
        }
    }

    public function assertEquals($expected, $actual, string $testDescription): void
    {
        $cond = ($expected === $actual);
        $details = $cond ? '' : "Expected " . var_export($expected, true) . ", got " . var_export($actual, true);
        $this->assert($cond, $testDescription, $details);
    }

    public function assertContains(string $needle, string $haystack, string $testDescription): void
    {
        $cond = str_contains($haystack, $needle);
        $details = $cond ? '' : "Substring '{$needle}' not found in haystack (len " . strlen($haystack) . ")";
        $this->assert($cond, $testDescription, $details);
    }

    public function assertMatchesRegex(string $pattern, string $subject, string $testDescription): void
    {
        $cond = (bool)preg_match($pattern, $subject);
        $details = $cond ? '' : "Subject did not match regex pattern '{$pattern}'";
        $this->assert($cond, $testDescription, $details);
    }

    public function assertGreaterThanOrEqual($expectedMin, $actual, string $testDescription): void
    {
        $cond = ($actual >= $expectedMin);
        $details = $cond ? '' : "Expected >= {$expectedMin}, got {$actual}";
        $this->assert($cond, $testDescription, $details);
    }

    public function getSummary(): array
    {
        $totalDuration = microtime(true) - $this->suiteStartTime;
        return [
            'total_assertions' => $this->totalAssertions,
            'passed' => $this->passedAssertions,
            'failed' => $this->failedAssertions,
            'duration' => round($totalDuration, 3),
            'failures' => $this->failures,
            'tiers' => $this->tierResults
        ];
    }

    public function printFinalReport(): bool
    {
        $summary = $this->getSummary();
        echo "\n\033[1;35m======================================================================\033[0m\n";
        echo "\033[1;37m                      FINAL E2E TEST SUMMARY                          \033[0m\n";
        echo "\033[1;35m======================================================================\033[0m\n";
        echo " Total Assertions: \033[1m{$summary['total_assertions']}\033[0m\n";
        echo " Passed          : \033[1;32m{$summary['passed']}\033[0m\n";
        echo " Failed          : \033[1;31m{$summary['failed']}\033[0m\n";
        echo " Total Time      : \033[1m{$summary['duration']}s\033[0m\n";
        echo "\033[1;35m----------------------------------------------------------------------\033[0m\n";

        if ($summary['failed'] > 0) {
            echo "\033[1;31mFAILURES ENCOUNTERED (" . count($summary['failures']) . "):\033[0m\n";
            foreach ($summary['failures'] as $i => $f) {
                $num = $i + 1;
                echo "  {$num}. [{$f['tier']}] {$f['test']}\n";
                if ($f['details']) {
                    echo "     Details: {$f['details']}\n";
                }
            }
            echo "\033[1;31m\n>>> OVERALL RESULT: FAILED <<<\033[0m\n\n";
            return false;
        } else {
            echo "\033[1;32m\n>>> ALL TESTS PASSED SUCCESSFULLY! (100% PASS RATE) <<<\033[0m\n\n";
            return true;
        }
    }
}
