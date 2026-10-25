<?php
// Loaded first by every page: session, class autoloader, BASE url and view helpers.
session_start();
ob_start(); // lets pages redirect after output has started

spl_autoload_register(function (string $class) {
    $file = __DIR__ . '/../classes/' . $class . '.php';
    if (is_file($file)) require $file;
});

// BASE = url path of the project folder (works in htdocs sub-folder or php -S)
$docRoot = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])), '/');
$project = str_replace('\\', '/', dirname(__DIR__));
define('BASE', rtrim(substr($project, strlen($docRoot)), '/'));

require __DIR__ . '/functions.php';
