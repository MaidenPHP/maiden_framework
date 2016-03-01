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
	      		'name' => array(
	      			'required' => true,
	      			'min' => 2,
	      			'max' => 50
	      			)

	      		/*
	        		'password_current'      => array(
	          		'required'  => true,
	          		'min'    => 6,
	          		'name'    => 'Old Password'
	        	),
	        'password_new'        => array(
	          'required'  => true,
	          'min'    => 6,
	          'name'    => 'New Password'
	        ),
	        'password_new_again'    => array(
	          'required'  => true,
	          'min'    => 6,
	          'name'    => 'Repeat New Password',
	          'matches'  => 'password_new'
	        )

	        */
	        ));
	  
	      if($validation->passed()){

	      	try{
	      		$user1->update(array(
	      			'name' => Input::get('name')
	      			));

				Session::flash('home', 'you have updated your information');
	      		Redirect::to('index.php');
	      	}
	      	catch(Exception $e){
	      		die($e->getMessage());
	      	}
	  	
	  	}
	  	else{
	  		foreach ($validation->errors() as $error) {
	  			echo $error . '<br>';
	  		}
	    }

  }
	}
?>

<html>

	<body>

		<form action='' method='post'>

			<div class='field'>
				<label for='name'> Username </label>
				<input type='text' name='name' id='name' value='' autcomplete='off'>
			</div>

			<input type='hidden' name='token' value='<?php echo Token::generate(); ?>'>
			<input type='submit' value='Update Information'>
		</form>

	</body>

</html>
