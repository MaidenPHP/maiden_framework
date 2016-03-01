<?php

	class Post{

		private $_db;

		public function __construct(){
			$this->_db = DB::getInstance();
		}

		public function post_create($table, $fields=array()){
			$this->_db->insert($table, $fields);
		}

		public function post_view_all($table){
			$this->_db->get_all($table);
		}

		public function post_update(){

		}

		public function post_delete(){

		}


	}