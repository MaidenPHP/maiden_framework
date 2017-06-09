<?php

/**
 * Class Password
 *
 * http://php.net/manual/en/ref.password.php
 *
 * http://php.net/manual/en/faq.passwords.php
 */
class Password 
{
    /**
     * http://php.net/manual/en/book.password.php
     *
     * Password constructor.
     */
	function __construct() 
	{

	}

    /**
     * @param $password
     * @return string
     */
    function create(string $password) : string
    {
        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => 11,
            'salt' => 'any string here'
        ]);
    }

    /**
     * @param string $password
     * @return bool
     */
    function update(string $password) : bool
    {
        return password_needs_rehash($password, 11);
    }

    /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    function check(string $password, string $hash) : bool
    {
        return password_verify($password, $hash);
    }

    /**
     * @param string $hash
     * @return array
     */
    function getInfo(string $hash) : array
    {
        return password_get_info($hash);
    }
}