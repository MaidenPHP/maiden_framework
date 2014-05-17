<?php
/* --------------------------------------------------------------------------------------
                                      BATTLE
   Credits         : phpSGEx by Aldrigo Raffaele
   Last modified by: rafa50 18.11.2013
   Comments        : fix colonization
-------------------------------------------------------------------------------------- */

if( isset($_GET['p']) ){
	$atkcity= (int)$_GET['p'];
	
	$qrc= mysql_query("SELECT * FROM city WHERE id =".$atkcity);
	if( mysql_num_rows($qrc) >0 ){
		$acinfo= mysql_fetch_array($qrc);
		
		if( $acinfo['owner'] == $me->user_id ){ header("Location: index.php"); end; }
		
		$body="<h3>You are ataking ".$acinfo['name']." city</h3> <table border='1' cellpadding='5'>
		<form method='post' action='index.php'> <input type='hidden' name='atkcity' value='$atkcity'>";
		$qrunt= mysql_query("SELECT * FROM `units` WHERE `owner_id` =".$me->user_id." AND `where` =".$me->city_id);
		while( $row= mysql_fetch_array($qrunt) ){
			$cuinf= mysql_fetch_array( mysql_query("SELECT * FROM t_unt WHERE id =".$row['id_unt']) );
			$body.= "<tr><td>".$cuinf['name']." (".$row['uqnt'].")</td> <td><input name='tuid".$row['id_unt']."' type='number' min='0' value='0' max='".$row['uqnt']."'></td></tr>";
		}
		$body.="<tr><td colspan='2'><input type='submit' value='Atack!' ></td></tr></form></table>";
	} else header("Location: index.php");
}

if( isset($_GET['colnize']) ){ //colonization page
	$qrcolunt= mysql_query("SELECT * FROM `t_unt` WHERE `type` = 'column' LIMIT 1;");
	if( mysql_num_rows($qrcolunt) > 0 ){
		$aidcolunt= mysql_fetch_array($qrcolunt);
		$idcolunt= $aidcolunt['id'];
		
		$numcu= 0;
		$qrycu= mysql_query("SELECT * FROM `units` WHERE `id_unt` =$idcolunt AND `owner_id` =".$me->user_id." AND `where` =".$me->city_id." AND `action` =0 LIMIT 1;");	
		if( mysql_num_rows($qrycu) >0 ){
			$anumcu= mysql_fetch_array($qrycu);
			$numcu= $anumcu['uqnt']; 
		}
		
		$qr; $x; $y; $z;
		if( MAP_SYS ==1 ){
			$x = (int)$_GET['gal'];
			$y = (int)$_GET['sys'];
			$z = (int)$_GET['colnize'];
			$qr = mysql_query("SELECT * FROM `city` WHERE `galaxy` =$x AND `system` =$y AND `pos` =$z LIMIT 1");
		} else {
			$x = (int)$_GET['x'];
			$y = (int)$_GET['y'];
			$z = 0;
			$qr = mysql_query("SELECT * FROM map WHERE x = $x AND y = $y");
		}
		
		if( mysql_num_rows($qr) > 0 ) header("Location: index.php?pg=main&err=Field is not empity!"); //field is not empity!
		else {
			$body.="
			<form name='fc' id='fc' method='post' action='?pg=main' >
			<input type='hidden' name='x' value='$x'>
			<input type='hidden' name='y' value='$y'>
			<input type='hidden' name='z' value='$z'>
			Colonize:<br> ".$aidcolunt['name']." (colonizator): $numcu<br>";
			
			if( $numcu >0 ) $body.="<input action='?pg=main' type='submit' name='colonize' value='Colonize!' >";
			else $body.="<span class='Stile3'>You must have at least one ".$aidcolunt['name']."</span>";
			
			$body.="</form>";
		}
	}
}
?>