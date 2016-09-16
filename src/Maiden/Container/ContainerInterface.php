<?php

interface ContainerInterface {

    function get($input = '');

    public function set(array $input);

    function name();

}