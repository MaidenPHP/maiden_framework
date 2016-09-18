<?php

/**
 * scalar and return type declaration:
 * int
 * float
 * bool
 * string
 * array
 *
 * you will get TypeError if conditions are not met properly
 */
declare(strict_types=1);

// composer autoload
require __DIR__ . '/../../vendor/autoload.php';

// sessions + cookies
session_start();

// config
$config = [
    'db_driver' => 'mysql',
    'db_host' => 'localhost',
    'db_name' => 'maiden_db',
    'db_username' => 'root',
    'db_password' => '',
];

// db
include __DIR__ . '/../Maiden/DB/db_connect.php';


