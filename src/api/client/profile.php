<?php
	require_once 'core/init.php';

	if (!$username = Input::get('user')) { //$_GET['get']
		Redirect::to('index.php');
	}
	else{
		$user = new User($username);
		if (!$user->exists()) {
			Redirect::to(404);
		}
		else{
			$data = $user->data();
		}

		?>
		<h4> <?php echo $data->username;?></h4>
		<p> full name: <?php echo $data->name;?></p>

		<?php
	}
?>

