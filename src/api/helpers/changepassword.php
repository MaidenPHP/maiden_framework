<?php
	require_once 'core/init.php';

	$user1 = new User();

	if (!$user1->isLoggedIn()) {
		Redirect::to('index.php');
	}
	
	if (Input::exists()) {
		if (Token::check(Input::get('token'))) {
			$validate = new Validate();
			$validation = $validate->check($_POST, array(
				'password_current' => array(
					'required' => true,
					'min' => 6,
					),
				'password_new' => array(
					'required' => true,
					'min' => 6
					),
				'password_new_again' => array(
					'required' => true,
					'min' => 6,
					'matches' => 'password_new'
					)
				));

			if ($validation->passed()){
				if (Hash::make(Input::get('password_current'), $user1->data()->salt) != $user1->data()->password) {
					echo "current password is wrong";
				}else{
					echo "changed pasword success";

					$salt = Hash::salt(32);
					$user1->update(array(
						'password' => Hash::make(Input::get('password_new'), $salt),
						'salt' => $salt
						));

					Session::flash('home', 'your password has been changed');
					//Redirect::to('index.php');

				}
			}
			else{
				echo "failed to change password, here are the errors: <br>";
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
				<label for='password_current'> Password </label>
				<input type='text' name='password_current' id='password_current' value='' autcomplete='off'>
			</div>

			<div class='field'>
				<label for='password_new'> Password New </label>
				<input type='text' name='password_new' id='password_new' value='' autcomplete='off'>
			</div>	

			<div class='field'>
				<label for='password_new_again'> Password Again </label>
				<input type='text' name='password_new_again' id='password_new_again' value='' autcomplete='off'>
			</div>	
			
			<input type='hidden' name='token' value='<?php echo Token::generate(); ?>'>
			<input type='submit' value='change password'>
		</form>


	</body>


</html>