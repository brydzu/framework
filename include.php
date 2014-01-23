<?php

session_start();
if (preg_match('~^application/json\b~', $_SERVER['HTTP_ACCEPT'])) ini_set('html_errors', false);

define('BASE_PATH', __DIR__);
define('DOMAIN', preg_replace('/^' . basename($_SERVER['DOCUMENT_ROOT']) . '\./', '', $_SERVER['HTTP_HOST']));

set_include_path(get_include_path() . PATH_SEPARATOR
    . BASE_PATH . '/lib' . PATH_SEPARATOR
    . BASE_PATH . '/model' . PATH_SEPARATOR
    . BASE_PATH . '/controllers' . PATH_SEPARATOR
    . BASE_PATH . '/forms'
);

$loader = require_once(BASE_PATH . "/vendor/autoload.php");
$loader->setUseIncludePath(true);

// The model generator automatically generates Record and Table classes
DB::enableModelGenerator();
