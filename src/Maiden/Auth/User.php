<?php
	
	class User{

		private $_db;
		private $_data;
		private $_sessionName;
		private $_isLoggedIn;
		private $_cookieName;

		public function __construct($user = null){
			$this->_db = DB::getInstance();

			$this->_sessionName = Config::get('session/session_name');
			$this->_cookieName = Config::get('remember/cookie_name');

			if (!$user) {
				if (Session::exists($this->_sessionName)) {
					$user = Session::get($this->_sessionName);
					if ($this->find($user)) {
						$this->_isLoggedIn = true;
					}
					else{
						// process lgout
					}
				}
			}
			else{
				$this->find($user);
			}
		}

		public function create($fields = array()){
			if (!$this->_db->insert('users', $fields)) {
				throw new Exception("problem creating account", 1);
				
			}
		}

		public function update($fields = array(), $id = null){

			if (!$id && $this->isLoggedIn()) {
				$id = $this->data()->id;
			}

			($this->_db->update('users', $id, $fields));

		}

		public function hasPermission($key){
			$group = $this->_db->get('groups', array('id', '=', $this->data()->group));


			if ($group->count()) {
				$permissions = json_encode($group->first()->permissions);
				if ($permissions[$key] == true) {
					return true;
				}

			}
			return false;
		}

		public function find($user = null){
			//find a user by id or username
			if ($user) {
				$field = is_numeric($user) ? 'id' : 'username';
				$data = $this->_db->get('users', array($field, '=', $user));

				if ($data->count()) {
					$this->_data = $data->first();
					return true;
				}
			}

			return false;
		}

		public function login($username = null, $password = null, $remember = false){

			$user = $this->find($username);
			//if the username clicked remmeber me
			if (!$username && !$password && $this->exists()) {
				Session::put($this->_sessionName, $this->data()->id);
			}
			else {
				if ($user) {
					if ($this->data()->password === Hash::make($password, $this->data()->salt)) {
						Session::put($this->_sessionName, $this->data()->id);

						//logged in with a session;

						if ($remember) {

							// generate a hash and check if the hash doesnt exist in the daatbase
							$hash = Hash::unique();
							// checks if the database has it the hash
							$hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));
							
							//will insert if there there does not exists a hash for the user
							if (!$hashCheck->count()) {
								$this->_db->insert('users_session', array(
									'user_id' => $this->data()->id,
									'hash' => $hash
									));
							}
							else{
								$hash = $hashCheck->first()->hash;
							}

							// storing a cookie
							Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
						}

						return true;
					}
				}
			}
			return false;

		}

		public function logout(){
			$this->_db->delete('users_session', array('user_id', '=', $this->data()->id));
			Session::delete($this->_sessionName);
			Cookie::delete($this->_cookieName);

		}
		public function data(){
			return $this->_data;
		}

		public function isLoggedIn(){
			return $this->_isLoggedIn;
		}

		public function exists(){
			return (!empty($this->_data)) ? true: false;
		}


	}