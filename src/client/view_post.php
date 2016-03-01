<?php
	require_once 'core/init.php';

	$posts = DB::getInstance()->get_all('post_partner');

	print "<pre>";
	print_r($row = $posts->results());
	print "</pre>";

	$count = count($row);

	while ($count !== 0) {
		$count -= 1;
		echo $row[$count]->post_partner_message . '<br>';

	}

	
?>

