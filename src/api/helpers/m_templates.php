<?php

	class Template{
		
		private $data;

		function __construct(){

		}

		//load function
		public function load($url){
			include $url;
		}

		function setData($name, $value){
			$this->data[$name] = ($value);
		}

		function getData($name){
			if (isset($this->data[$name])) {
				return $this->data[$name];
			}
			else{
				return '';
			}
		}

	}