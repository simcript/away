<?php

loadEnv();
debug(getenv('DEBUG'));
autoload();

(new \App\Boot())->start();
exit();


// general functions

function autoload(): void
{
    spl_autoload_register(function ($class) {
        try {
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            $file = __DIR__ . '/src/' . $file;
            $file = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $file);
            if (file_exists($file)) {
                require_once $file;
                return true;
            }
            error_log("File class not found " . $file);
            return false;
        } catch (\Throwable $th) {
            error_log('Error in load class ' . $th->getMessage());
            return false;
        }
    });
}

function loadEnv(): void
{
    $envPath = __DIR__ . '/.env';
    if (!file_exists($envPath)) {
        die('.env file not found. Create a environment file(.env) similar to .env.example file.');
    }
    $env = file_get_contents($envPath);
    $lines = explode(PHP_EOL, $env);

    foreach ($lines as $line) {
        preg_match("/([^#]+)\=(.*)/", $line, $matches);
        if (isset($matches[2])) {
            putenv(trim($line));
        }
    }
}

function debug($status = true): void
{
    $status = $status ? '1' : '0';
    error_reporting(E_ERROR | E_PARSE);
    ini_set('display_errors', $status);
}

function dd(...$args): void
{
    highlight_string("<?php\n" . var_export($args, true) . ";\n?>");
    exit;
}

function dieError(int $code, string $message, int $status = 500, string $logMessage = ''): void
{
    http_response_code($status);
    if (!empty($logMessage)) {
        error_log($logMessage);
    }
    dd($code, $message);
}