<?php

// setup __autoload for every singe class
spl_autoload_register(function($class) {
    require_once 'classes/' . $class . '.php';
});