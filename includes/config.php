<?php
// ============================================================
// StrideOn - Configuração da Base de Dados
// Desenvolvido por: Eng. Software Malvin Manguele
// ============================================================

// ── Carrega o .env se existir ────────────────────────────────
// Função simples para ler .env sem precisar de Composer
function loadEnv(string $path): void {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue; // ignorar comentários
        if (!str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);
        if (!empty($key) && !array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Carrega .env a partir da raiz do projeto
loadEnv(__DIR__ . '/../.env');

// ── Definições — lidas do ambiente, nunca hardcoded ──────────
define('DB_HOST',    $_ENV['DB_HOST']    ?? 'localhost');
define('DB_USER',    $_ENV['DB_USER']    ?? 'root');
define('DB_PASS',    $_ENV['DB_PASS']    ?? '');
define('DB_NAME',    $_ENV['DB_NAME']    ?? 'strideon');
define('DB_CHARSET', 'utf8mb4');

define('APP_ENV',          $_ENV['APP_ENV']          ?? 'development');
define('SITE_NAME',        'StrideOn');
define('SITE_URL',         $_ENV['SITE_URL']          ?? 'http://localhost/strideon');
define('WHATSAPP_NUMBER',  $_ENV['WHATSAPP_NUMBER']   ?? '');
define('PHONE_CONTACT',    $_ENV['PHONE_CONTACT']     ?? '');
define('CURRENCY',         $_ENV['CURRENCY']          ?? 'MT');

// ── Controlo de erros por ambiente ──────────────────────────
if (APP_ENV === 'production') {
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    // Cria a pasta logs/ se não existir
    $logDir = __DIR__ . '/../logs';
    if (!is_dir($logDir)) mkdir($logDir, 0750, true);
    ini_set('error_log', $logDir . '/php_errors.log');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// ── Ligação à base de dados (singleton) ─────────────────────
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Em produção não expõe detalhes; em desenvolvimento mostra
            if (APP_ENV === 'production') {
                error_log('DB connection failed: ' . $e->getMessage());
                http_response_code(500);
                die(json_encode(['error' => 'Serviço temporariamente indisponível.']));
            }
            die(json_encode(['error' => 'Erro de conexão: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}
