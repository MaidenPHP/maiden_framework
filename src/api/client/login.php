<?php
	include 'core/init.php';

	if (Input::exists()) {
		if (Token::check(Input::get('token'))) {
			$validate = new Validate();
			$validation = $validate->check($_POST, array(
				'username' => array('required' => true),
				'password' => array('required' => true)
				));

			if ($validation->passed()) {
				try{
					$user = new User();

					$remember = (Input::get('remember') === 'on') ? true : false;
					$login = $user->login(Input::get('username'), Input::get('password'), $remember);

					if ($login) {
						Redirect::to('index.php');
					}
					else{
						echo "not successful login";
					}
				}
				catch (Exception $e) {
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

	<head>
		<title> My Website </title>
	</head>

	<body>
		<form action='' method='post'>
			<div>
				<label for='username'> Username </label>
				<input type='text' name='username' id='username' autocomplete='off'>
			</div>
			<div>
				<label for='password'> Password </label>
				<input type='text' name='password' id='username' autocomplete='off'>
			</div>

			<div class='field'>
				<label for='remember'>
					<input type='checkbox' name='remember' id='remember'> Remember
				</label>
			</div>

			<input type='hidden' name='token' value='<?php echo Token::generate();?>'>
			<input type='submit' value='Login'>
		</form>
	</body>

</html>
