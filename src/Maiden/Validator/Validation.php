<?php

	class Valdiate {
		private $_passed = false;
		private $_errors = array();
		private $_db = null;

		public function __construct(){
			$this->db = DB::getInstance();
		}

		public function check($source, $items = array()){
			foreach ($items as $item => $rules) {
				foreach ($rules as $rule => $rule_value) {
					echo {$item} {$rule} 'must be' . {$rule_value} . '<br>' =;
					# code...
				}

			}
		}
	}

?>