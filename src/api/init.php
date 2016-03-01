<?php

	session_start();

	$GLOBALS['config'] = array(
		'mysql' => array(
			'host' => 'pingpongpartnerscom.ipagemysql.com',
			'username' => 'yichen',
			'password' => 'yichen',
			'db' => 'oop_login'
			),

		'remember' => array(
			'cookie_name' => 'hash',
			'cookie_expiry' => 604800
			),

		'session' => array(
			'session_name' => 'user',
			'token_name' => 'token'
			)

	);

	spl_autoload_register(

		function($class){
			require_once 'classes/' . $class . '.php';
		}
	);

	/*
	function __autoload($class){
		require_once 'classes/' . $class . '.php';
	}
	*/
	require_once 'functions/sanitize.php';

	if (Cookie::exists(Config::get('remember/cookie_name')) && !Session::exists(Config::get('session/session_name'))) {
		//echo "user asked to be remember";	
		//loop up the cookie in the database and see if the token exists in the database
		// do a hash check

		$hash = Cookie::get(Config::get('remember/cookie_name'));	
		$hashCheck = DB::getInstance()->get('users_session', array('hash', '=', $hash));

		if ($hashCheck->count()) { //doesnt work
			//echo "hash matches logg user in bro. <br>";

			$user = new User($hashCheck->first()->user_id);
			$user->login();
		}
	}

?>