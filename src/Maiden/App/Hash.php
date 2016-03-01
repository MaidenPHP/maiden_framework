<?php

	class Hash{
		public static function make($string, $salt=''){
			return hash('sha256', $string . $salt);
			//,make a hash
			// salt: adds a randomly generated string
		}

		public static function salt($length){
			//generate a salt

			return mcrypt_create_iv($length);

		}

		public static function unique(){
			return self::make(uniqid());

		}
	}

