<?php

class Router
{
    // parse_url
    // curl request with GET/POST/PUT/DELETE

    function __construct()
    {
        func_get_args();
        // initialize curl
    }
}


/*
 * $router = new Router;
 *
 * $router->setBaseUrl();
 * $router->action('GET', 'link/to/url');
 *
 */