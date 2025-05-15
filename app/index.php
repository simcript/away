<?php

define("AWAY_STARTED", microtime(true));
define("APP_PATH", trim(__DIR__, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
define("ROOT_PATH", APP_PATH . '..' . DIRECTORY_SEPARATOR);
require_once 'functions.php';

try {

    envLoader(ROOT_PATH);
    debug(getenv('APP.DEBUG'));
    autoload();

    (new src\Boot())->start();
} catch (\Throwable $th) {
    if (getenv('APP.DEBUG') == 'true') {
        $error = $th->getMessage() . $th->getTraceAsString();
        dieError(404, $error, 500, $error);
    }
}
exit();
