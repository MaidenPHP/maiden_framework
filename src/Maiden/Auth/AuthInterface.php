<?php

namespace Maiden\Auth;

interface AuthInterface
{
    function login(array $data);

    function logout();

    function register(array $data);

    function forgotPassword();

    function getAuthUser();
}