<?php
/**
 * helper functions
 * @return void
 */


function autoload(): void
{
    spl_autoload_register(function ($class) {
        try {
            if (requireClass($class, APP_PATH) || requireClass($class, ROOT_PATH)) {
                return true;
            }
            error_log("Class $class not found");
            return false;
        } catch (\Throwable $th) {
            error_log('Error in load class ' . $th->getMessage());
            return false;
        }
    });
}

function requireClass(string $class, string $basePath): bool
{
    $file = $basePath . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    $file = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $file);
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    return false;
}

function envLoader(string $envDirectory): void
{
    $envFilePath = $envDirectory . '/.env';
    if (!file_exists($envFilePath)) {
        dieError(500, '.env file not found. Create a environment file(.env) similar to .env.example file.');
    }
    $env = file_get_contents($envFilePath);
    $lines = explode(PHP_EOL, $env);

    foreach ($lines as $line) {
        preg_match("/([^#]+)\=(.*)/", $line, $matches);
        if (isset($matches[2])) {
            putenv(trim($line));
        }
    }
}

function debug(string $status): void
{
    $status = $status == 'true' ? '1' : '0';
    error_reporting(E_ERROR | E_PARSE);
    ini_set('display_errors', $status);
}

function dd(...$args): void
{
    highlight_string("<?php\n" . var_export($args, true) . ";\n?>");
    exit();
}

function dieError(int $code, string $message, int $status = 500, string $logMessage = ''): void
{
    http_response_code($status);
    if (!empty($logMessage)) {
        error_log($logMessage);
    }
    dd($code, $message);
}