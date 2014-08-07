<?php

/* Uncomment to enable modules (http://github.com/jasny/phay/README.md#modules) */
//define('MODULE', basename($_SERVER['DOCUMENT_ROOT']));

chdir(__DIR__);

set_include_path(get_include_path() . PATH_SEPARATOR
    . 'lib' . PATH_SEPARATOR
    . 'model' . PATH_SEPARATOR
    . 'controllers' . (defined('MODULE') ? '/' . MODULE : '') . PATH_SEPARATOR
    . 'forms' . (defined('MODULE') ? '/' . MODULE : '')
);

$loader = require_once("vendor/autoload.php");
$loader->setUseIncludePath(true);

// Use Twig to render views (by default)
View::using('twig');

// Set locale (based on config)
App::setLocale();

// Enable error handling
App::handleErrors();

// Enable generator automatically generates Record, Table and Form classes
DB::enableModelGenerator();
//Form::enableGenerator();

// Use sessions
session_start();
