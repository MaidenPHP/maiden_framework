<?php
if( isset($secure) ){
	$cinf= mysql_fetch_array( mysql_query("SELECT * FROM conf LIMIT 1") );
	
	$head.= "<script src='../templates/js/nicEdit2.js'></script>
		<script>bkLib.onDomLoaded(nicEditors.allTextAreas);</script>";
	$body.="<form method='post' action='?pg=xconfig'>
	News: <br>
	<textarea name='freeRTE_content' id='textarea' cols='45' rows='5'>".$cinf['news1']."</textarea><br><br>
	Server name: <input name='servernam' type='text' value='".$config['server_name']."'> <br>
	Sub desc: <input name='subdesc' type='text' value='".$config['server_desc_sub']."'> <br>
	Main desc: <input name='maindesc' type='text' value='".$config['server_desc_main']."'> <br>
	Template: <select name='templ'>";
		
	$dir= '../templates';
    $handle= opendir($dir); // Lettura...
    while( $files= readdir($handle) ) { // Escludo gli elementi '.' e '..' e stampo il nome del file...
    	if( $files != '.' && $files != '..' && $files != 'js' && $files != 'index.php' ){
			$body.= "<option ";
			if( $files == $config['template'] ) $body.="selected";
			$body.=">".$files."</option>";
		}
    }
		
	$body.="</select><br>
	Show resource even if you don't have: <input type='checkbox' name='FLAG_SZERORES' ";
	if( $config['FLAG_SZERORES'] ) $body.="checked='checked'";
	$body.= "> <br>
	Show units, buildings and researches even if you cannot build: <input type='checkbox' name='FLAG_SUNAVALB' ";
	if( $config['FLAG_SUNAVALB'] ) $body.="checked='checked'";
	$body.="> <br>
	Show resource icon: <input type='checkbox' name='FLAG_RESICONS' ";
	if( $config['FLAG_RESICONS'] ) $body.="checked='checked'";
	$body.="> <br>
	Show resource label: <input type='checkbox' name='FLAG_RESLABEL' ";
	if( $config['FLAG_RESLABEL'] ) $body.="checked='checked'";
	$body.="> <br>
	<br>
	
	<h2>MAP SIZE</h2>
	Map X: <input type='number' min='1' name='mapx' value='".$config['Map_max_x']."' min='1'><br>
	Map Y: <input type='number' min='1' name='mapy' value='".$config['Map_max_y']."' min='1'><br>";
	
	if( MAP_SYS!=2 )$body.="Map Z: <input type='number' min='1' name='mapz' value='".$config['Map_max_z']."'><br>";
	
	$body.="<br>
	
	<h2>RATES</h2>
	Magazine base capacity: <input type='number' name='MG_max_cap' value='".$config['MG_max_cap']."'> <br>
	Barrack time dividier per level: <input type='text' name='baru_tmdl' value='".$config['baru_tmdl']."'> <br>
	Build fast moltiplier: <input type='text' name='buildfast_molt' value='".$config['buildfast_molt']."'> <br>
	Research fast moltiplier: <input type='text' name='researchfast_molt' value='".$config['researchfast_molt']."'> <br>
	<br>
	
	<h2>Population engine</h2>
	Population resource:<select name='popres'>
		<option value='0'>OFF</option>";
	
		$qrr= mysql_query("SELECT * FROM resdata");
		while( $row= mysql_fetch_array($qrr) ){
			$body.="<option value='".$row['id']."'>".$row['name']."</option>";	
		}
	
	$body.="</select>
	<br>Population add per level: <input name='popaddpl' type='number' value='".$config['popaddpl']."' min='0'>
	
	<br><input type='submit' name='editconfig' value='save'></form>";	
}
?>