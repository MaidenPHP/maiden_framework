<?php

function dd($data)
{
    die(var_dump($data));
}

function de($data)
{
    die(var_export($data));
}

function prettyView($data)
{
    echo '<pre>', print_r($data), '</pre>';
}

function responseJson($data)
{
    header('application/json');
    echo json_encode($data);
}