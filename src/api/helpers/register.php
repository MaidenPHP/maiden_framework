<?php
	require_once 'core/init.php';

	if (Input::exists()) {
		if (Token::check(Input::get('token'))) {
			$validate = new Validate();
			$validation = $validate->check($_POST, array(
				'username' => array(
					'required' => true,
					'min' => 2,
					'max' => 20,
					'unique' => 'users'
					),
				'password' => array(
					'required' => true,
					'min' => 6
					),
				'password_again' => array(
					'required' => true,
					'matches' => 'password'
					),
				'name' => array(
					'required' => true,
					'min' => 6,
					'max' => 50
					),
				));

			if ($validation->passed()){
				//Session::flash('success', 'you have reigstered');
				//header("Location: index.php");

				$user = new User();

				$salt = Hash::salt(32);

				try {
					$user->create(array(
						'username' => Input::get('username'),
						'password' => Hash::make(Input::get('password'), $salt),
						'salt' => $salt,
						'name' => Input::get('name'),
						'joined' => date('Y-m-d H:i:s'),
						'group' => 1
						));
					
						Session::flash('home', 'you have been registered');
						Redirect::to('index.php');
				} catch (Exception $e) {
					die($e->getMessage());
				}
			}
			else{
				echo "failed to register, here are the errors: <br>";
				foreach ($validation->errors() as $error) {
					echo $error . "<br>";

				}

			}

		}
	}


?>

<html>

	<body>

		<form action='' method='post'>

			<div class='field'>
				<label for='username'> Username </label>
				<input type='text' name='username' id='username' value='' autcomplete='off'>
			</div>

			<div class='field'>
				<label for='password'> Password </label>
				<input type='text' name='password' id='password' value='' autcomplete='off'>
			</div>

			<div class='field'>
				<label for='password_again'> Password Again </label>
				<input type='text' name='password_again' id='password_again' value='' autcomplete='off'>
			</div>	

			<div class='field'>
				<label for='name'> Name </label>
				<input type='text' name='name' id='name' value='' autcomplete='off'>
			</div>	
			
			<input type='hidden' name='token' value='<?php echo Token::generate(); ?>'>
			<input type='submit' value='Register'>
		</form>


	</body>


</html>