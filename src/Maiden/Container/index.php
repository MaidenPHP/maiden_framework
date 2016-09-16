<?php

require __DIR__ . '/../../../vendor/autoload.php';

use Maiden\Container\IOC as DI;

DI::get();

helper('123');


class ContaineringTest
{
    function __construct()
    {
    }
}

/**
 * SPL
 *
 * 6 interfaces
 *
 * countable interface
 */