<?php

namespace Maiden;

class Response
{
    function __construct()
    {

    }

    public function responseJson($data) {
        header('Content-Type: application/json;charset=utf-8');
        echo json_encode($data);
    }
}