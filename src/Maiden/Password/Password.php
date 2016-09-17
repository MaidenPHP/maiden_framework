<?php

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
     *
     * password_hash()
     */
    function create($password) : void
    {
        password_hash($password, PASSWORD_BCRYPT, [
            'cost' => 11,
            'salt' => 'any string here'
        ]);
    }

    // password_needs_rehash()
    function update($password)
    {

    }

    // password_verify()
    function check()
    {

    }
}

