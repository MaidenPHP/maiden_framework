<?php

use Maiden\Auth\Auth;
use Mockery\Mock;

class Learn extends PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->auth = new Auth();
    }

    function test_it_works()
    {
        echo PHP_EOL;

        $mock = Mockery::mock('Maiden\Auth\Auth');
        $mock->shouldReceive('login')->once()->andReturn('hey');

        var_dump($mock->login([]));


        //var_dump($this->auth->isLoggedIn());

        $this->assertTrue(true);
    }

    function tearDown()
    {
        Mockery::close();
    }
}