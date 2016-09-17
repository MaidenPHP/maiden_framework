<?php

namespace Maiden\Router;

class Router
{
    // parse_url
    // curl request with GET/POST/PUT/DELETE
    function __construct(Foo $foo, callable $f, \stdClass $stdClass)
    {
        func_get_args();
        // initialize curls
    }

    public function getRoutes(int $numberOfRoutes, float $hey) : array
    {
        return [];
    }

    public function printRoutes()
    {
        echo 'print all the routes';

        return;
    }
}

/*
 * Type hints:
 * Scalars
 * - int
 * - float
 * - bool
 * - string
 *
 * others
 * - array
 * - object (std())
 * - callable
 */
/*
 * $router = new Router;
 *
 * $router->setBaseUrl();
 * $router->action('GET', 'link/to/url');
 *
 */