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
				'level' => array(
					'required' => true
					),
				'message' => array(
					'required' => true,
					'max' => 500
					),
				'location' => array(
					'required' => true
					)
				));

			if ($validation->passed()){

				echo "validation successful";

				$post_level = Input::get('level');
				$post_message = Input::get('message');
				$post_location = Input::get('location');

				echo $post_level . " " . $post_message . " " . $post_location;
			
				$post = new Post();

/*
				$post->post_create('post_partner', array(
					'post_partner_author_id' => 0,
					'post_partner_level' => $post_level,
					'post_partner_message' => $post_message,
					'post_partner_location' => $post_location,
					'post_partner_date' => 'the date'
					));
*/



/*	
					$user->create(array(
						'username' => Input::get('username'),
						'password' => Hash::make(Input::get('password'), $salt),
						'salt' => $salt,
						'name' => Input::get('name'),
						'joined' => date('Y-m-d H:i:s'),
						'group' => 1
						));

				$salt = Hash::salt(32);
				$user1->update(array(
					'password' => Hash::make(Input::get('password_new'), $salt),
					'salt' => $salt
					));
*/
				Session::flash('home', 'your password has been changed');
				//Redirect::to('index.php');

			}
			else{
				echo "failed to process information, here are the errors: <br>";
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
				<label for='level'> Level </label>
				<select name="level">
					<option value="1"> 1 </option>
					<option value="2"> 2 </option>
					<option value="3"> 3 </option>
					<option value="4"> 4 </option>
					<option value="5"> 5 </option>
				</select>
			</div>

			<div class='field'>
				<label for='message'> Message </label>
				<br>
				<textarea type="text" name="message" rows="10" cols="30"></textarea>
			</div>	

			<div class='field'>
				<label for='location'> Location </label>
				<select name="location">
					<option value="city1"> city 1</option>
					<option value="city2"> city 2</option>
					<option value="city3"> city 3</option>
				</select>
<!--
				<input type='text' name='password_new_again' id='password_new_again' value='' autcomplete='off'> -->
			</div>	
			<input type='hidden' name='token' value='<?php echo Token::generate(); ?>'>
			<input type='submit' value='Add post'>
		</form>

	</body>


</html>