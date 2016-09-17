<?php

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


