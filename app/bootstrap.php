<?php

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = APP_PATH . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

$configFile = APP_PATH . '/config.php';
if (!file_exists($configFile)) {
    $configFile = APP_PATH . '/config.example.php';
}

$config = require $configFile;

if (!empty($config['session_name'])) {
    session_name($config['session_name']);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

return $config;
