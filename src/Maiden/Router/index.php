<?php

namespace Maiden\Router;

require __DIR__ . '/../../../vendor/autoload.php';

//use Maiden\Router;

$router = new Router(new Foo(), function() {

}, new \stdClass());

$router->printRoutes();