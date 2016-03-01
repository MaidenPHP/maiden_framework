<?php

	class Config{

		public static function get($path=null){
			/*
			 * gets the $path and uses the globals from config from
			 * init.php.
			 * returns the global variable within an array or return false
			 * ex: 	echo Config::get('mysql/host') --> "localhost"
			*/

			if ($path) {

				$config = $GLOBALS['config'];
				$path = explode('/', $path);
				// gets rid of the '/' and makes it into an array
				// ex: $path = mysql/host -> print_r ($path) --> array([0] -> mysql, [1] -> host

				foreach ($path as $bit) { 

					// loops through $path array now
					// sets $config = $GLOBALS['config']['mysql'] 
					if (isset($config[$bit])) { //loops through until it gets to the end, $GLOBALS['config']['mysql']['host'] 
						$config = $config[$bit];
					}
				}

				return $config; // returns the 'host' -> localhost
			}

			return false;
		}
	}

?>