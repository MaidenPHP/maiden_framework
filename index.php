<?php
/* --------------------------------------------------------------------------------------
                                      MAIN INDEX PAGE
   Credits         : phpSGEx by Aldrigo Raffaele
   Last modified by: raffa50 19.03.2014
   Comments        : reset password
-------------------------------------------------------------------------------------- */

$secure= true;
$head=""; $body=""; $blol="";
if ( !file_exists("config.php") ) header ("Location: ./install/index.php");
session_start();
require "func.php";
db_connect();

//load (once) game config from database
global $config;
$config= mysql_fetch_array( mysql_query("SELECT * FROM conf LIMIT 1") );

include("adm/updater/updater.php");
updateDB($dbver);

global $pg;
if( isset($_GET['pg']) ) $pg=$_GET["pg"];

if( isset($_GET['lang']) ){ include ("./lang/".$_GET['lang'].".php");
	$glang=$_GET['lang'];
	$lkrl="?lang=".$_GET['lang'];
} else { 
	if( isset($_SESSION['lang']) ) include("./lang/".$_SESSION['lang'].".php");
	else include ("./lang/".LANG.".php"); $glang=LANG; 
}

if( isset($_REQUEST["act"]) ) {
	switch($_REQUEST["act"]) {
		case "reg_login":
			login($_POST['reg_nickname'], $_POST['reg_password']);
		break;	
		case "register":
			register();
		break;
		case "logout":
			if( isset($_SESSION['id']) ){
				session_destroy();
				session_start();
			}
			$pg="index";
		break;
	}
}

if( isset($_POST['recoverpass']) && isset($_POST['email']) ){
	$email= mysql_real_escape_string($_POST['email']);
	$qr= mysql_query("SELECT `id`, `username` FROM `users` WHERE `email` = '$email' LIMIT 1");
	if( mysql_num_rows($qr) ==0 ) break;
	
	$usrinf= mysql_fetch_array($qr);
	$hash= md5( mtimetn() ."|". $usrinf['id'] );
	mail( $email, $cfg['server_name']." -Reset your password", 
		"Your username is: ". $usrinf['username'] ."\n
		Visit this link to reset you password: ".$_SERVER['host']."?pg=register.php?resetpw=$hash&id=" .$usrinf['id'] 
	);
	
	mysql_query("REPLACE INTO `user_passrecover` (`usrid`, `hash`, `until`) VALUES ('".$usrinf['id']."', '$hash', '".(mtimetn() +3600*24)."');");
}

if( isset($_POST['resetpass']) && isset($_POST['newpass']) && isset($_POST['id']) && isset($_POST['hash']) ){
	$newpass= md5( $_POST['newpass'] );
	$id= (int)$_POST['id'];
	$hash= mysql_real_escape_string($_POST['hash']);
	
	$qr= mysql_query("SELECT `until` FROM `user_passrecover` WHERE `usrid` = 1 AND `hash` = 'asd';");
		
	if( mysql_num_rows($qr) < 1 ){
		$body.="<b>Invalid!</b>";
	} else {
		mysql_query("UPDATE `users` SET `password` = '$newpass' WHERE `id` =$id;");
		mysql_query("DELETE FROM `user_passrecover` WHERE `usrid` = $id;");
	}
}

if( isset($_SESSION['id']) ) {
	if( !isset($pg) ) $pg="main";
	$me= new sge($_SESSION['id']);

	//Query from other pages
	if( isset($_GET['bid']) ) $me->build_addquee( $_GET['bid'] );
	
	if( isset($_POST['itunt']) ) $me->unit_train_addquee( $_POST['itunt'], $_POST['qunt'] );
	
	if( isset($_GET['research']) ) $me->research_addquee( $_GET['research'] );
	
	if( isset($_POST['act']) && $_POST['act']=="messagesend" ) $me->sendmsg( $_POST['ito'], $_POST['mtit'], $_POST['freeRTE_content'], 1, null);
	
	if( isset($_GET['delmsg']) ) mysql_query("DELETE FROM user_message WHERE id =".(int)$_GET['delmsg']);
	
	if( isset($_POST['createally']) ) {
		$qaid= mysql_fetch_array( mysql_query("SELECT `id` FROM ally ORDER BY `id` ASC LIMIT 1") );
		$aid= $qaid['id'] +1;
		$nam= mysql_real_escape_string($_POST['name']);
		mysql_query("INSERT INTO `ally` (`id` ,`name` ,`desc` ,`owner` ,`points` ,`acess`) VALUES ($aid , '$nam', '', '".$me->user_id."', '0', '0');");
		mysql_query("UPDATE `users` SET `ally_id` = '$aid' WHERE `id` =".$me->user_id);
		$me->user_info['ally_id'] =$aid;
	}
	
	if( isset($_POST['allyinvite']) ) $me->sendmsg( (int)$_POST['usr'], "Ally invite", "You are invited!", 3, $me->user_info['ally_id'] );
	
	if( isset($_GET['aia']) ){ //enjoy ally invite
		//invite must exist!
		if( mysql_num_rows( mysql_query("SELECT id FROM user_message WHERE id =".(int)$_GET['invid']) ) >0 ){
			$aid= (int)$_GET['aia'];
			mysql_query("UPDATE `users` SET `ally_id` = '$aid' WHERE `id` =".$me->user_id);
			$me->user_info['ally_id'] =$aid;
			mysql_query("DELETE FROM user_message WHERE id =".(int)$_GET['invid']);
		}
	}
	
	if( isset($_GET['allyleave']) ) { //send a message to the admin that says that the player left
		$ainf= mysql_fetch_array( mysql_query("SELECT owner FROM ally WHERE id =".$me->user_info['ally_id']) );
		$me->sendmsg( $ainf['owner'], "Player left ally", "Player ".$me->user_id." left the ally", 2 );
		mysql_query("UPDATE `users` SET `ally_id` = '0' WHERE `id` =".$me->user_id);
	}
	
	if( isset($_POST['saveally']) ) {
		$desc= html_entity_decode( htmlentities($_POST['allydesc']) );
		$nam= mysql_real_escape_string( $_POST['name'] );
		mysql_query("UPDATE `ally` SET `desc` = '$desc', `name` = '$nam' WHERE `id` =".(int)$_POST['saveally']);
	}
	
	if( isset($_POST['makeoffer']) ){
		$resreq= (int)$_POST['resreq'];
		$resreqqnt= (int)$_POST['resreqqnt'];
		$resoff= (int)$_POST['resoff'];
		$resoffqnt= (int)min( (int)$_POST['resoffqnt'], $me->city_res[$resoff] );
		$me->city_res[$resoff] -= $resoffqnt;
		mysql_query("INSERT INTO `market` (`id`, `owner`, `resoff`, `resoqnt`, `resreq`, `resrqnt`) VALUES (NULL, '".$me->user_id."', '$resoff', '$resoffqnt', '$resreq', '$resreqqnt');");
		$me->updatedb_city_resources();
	}
	
	if( isset($_GET['aceptoff']) ){
		$offid=(int)$_GET['aceptoff'];
		$offinf= mysql_fetch_array( mysql_query("SELECT * FROM market WHERE id=".$offid) );	
		if( $me->city_res[ $offinf['resreq'] ] >= $offinf['resrqnt'] ){ //you must have the requided resoure!
			$me->city_res[ $offinf['resreq'] ] -= $offinf['resrqnt'];
			$me->city_res[ $offinf['resoff'] ] += $offinf['resoqnt'];
			
			$op= new sge( $offinf['owner'] ); //update the offer owner resource!
			$op->city_res[ $offinf['resreq'] ] += $offinf['resrqnt'];
			$op->updatedb_city_resources();
			
			mysql_query("DELETE FROM market WHERE id=".$offid);
			$me->updatedb_city_resources();
		}
	}
	
	if( isset($_POST['atkcity']) ){
		$atkct= (int)$_POST['atkcity'];
		$qrtunt= mysql_query("SELECT * FROM t_unt");
		while( $row= mysql_fetch_array( $qrtunt ) ){
			if( isset( $_POST[ 'tuid'.$row['id'] ] ) ){
				$movunt= (int)$_POST[ 'tuid'.$row['id'] ];
				$qryourunt= mysql_query("SELECT * FROM `units` WHERE `owner_id` =".$me->user_id." AND `where` =".$me->city_id." AND id_unt =".$row['id']);
				if( mysql_num_rows($qryourunt) >0 ){
					$ayu= mysql_fetch_array($qryourunt);
					$myunt= $ayu['uqnt'] -$movunt;
					mysql_query("INSERT INTO `units` (`id`, `id_unt`, `uqnt`, `owner_id`, `from`, `to`, `where`, `time`, `action`) VALUES (NULL, '".$row['id']."', '$movunt', '".$me->user_id."', '".$me->city_id."', '$atkct', NULL, '".(mtimetn()+10)."', '1');");
					mysql_query("UPDATE `units` SET `uqnt` = '$myunt' WHERE `id` =".$ayu['id']);
				}
			}
		}
	}
	
	if( isset($_POST['editset']) ){
		$lng= mysql_real_escape_string($_POST['lang']);
		mysql_query("UPDATE `users` SET `lang` = '$lng' WHERE `id` =".$me->user_id);
		$_SESSION['lang'] = $lng;
		header("Location: index.php?pg=settings");
	}
	
	if( isset($_POST['colonize']) ){
		$gal= (int)$_POST['gal'];
		$sys= (int)$_POST['sys'];
		$pos= (int)$_POST['pos'];
		if( mysql_num_rows( mysql_query("SELECT * FROM `city` WHERE `galaxy` =$gal AND `system` =$sys AND `pos` =$pos LIMIT 1;") ) ==0 ){
			mysql_query("INSERT INTO `city` (`id`, `owner`, `name`, `pop`, `last_update`, `galaxy`, `system`, `pos`, `img`) VALUES (NULL, '".$me->user_id."', 'Your new city', '100', '".mtimetn()."', '$gal', '$sys', '$pos', 'null.gif');");
		}
	}
	
	if( isset($_POST['deloff']) ){
		$offdata= mysql_fetch_array( mysql_query("SELECT * FROM market WHERE id = ".(int)$_POST['deloff']) );
		$me->city_res[ $offdata['resoff'] ] += $offdata['resoqnt'];
		
		mysql_query("DELETE FROM market WHERE id = ".(int)$_POST['deloff']);
	}
	
	if( isset($_POST['colonize']) && isset($_POST['x']) && isset($_POST['y']) && isset($_POST['z']) ){
		$x = (int)$_POST['x'];
		$y = (int)$_POST['y'];
		$z = (int)$_POST['z'];
			
		$qr;
		if( MAP_SYS ==1 ) $qr = mysql_query("SELECT * FROM `city` WHERE `galaxy` =$x AND `system` =$y AND `pos` =$z LIMIT 1");
		else $qr = mysql_query("SELECT * FROM map WHERE x = $x AND y = $y");	
		if( mysql_num_rows( $qr ) >0 ) break;
		
		if( MAP_SYS ==1 ) mysql_query("INSERT INTO `city` (`id`, `owner`, `name`, `last_update`, `galaxy`, `system`, `pos`, `img`) VALUES (NULL, '".$me->user_id."', 'new city', '".mtimetn()."', '$x', '$y', '$z', 'null.gif');");
		else{ 
			$sqd=mysql_query("SELECT id FROM ".TB_PREFIX."city ORDER BY `id` DESC");
			if( mysql_num_rows($sqd) >0 ){
				$alic= mysql_fetch_array($sqd);
				$cidr= $alic['id']+1;
			} else $cidr= 1;
		
			mysql_query("INSERT INTO `city` (`id`, `owner`, `name`, `last_update`) VALUES ($cidr, '".$me->user_id."', 'new city', '".mtimetn()."');");
			mysql_query("INSET INTO `map` VALUES $x, $y, 'v1', $cidr");
		}
	}
	
	//---
	//private messages
	if( !isset($_GET['mtp']) ) $usermessage= mysql_query("SELECT * FROM `".TB_PREFIX."user_message` WHERE `to` =".$me->user_id); 
	else $usermessage= mysql_query("SELECT * FROM `".TB_PREFIX."user_message` WHERE `to` =".$me->user_id. " AND mtype =".(int)$_GET['mtp']);
	
	$msgunreaded= mysql_query("SELECT * FROM `".TB_PREFIX."user_message` WHERE `to` =".$me->user_id." AND `read` =0");
	$nummsg= mysql_num_rows($msgunreaded);
}

if( file_exists("templates/".$config['template']."/tmpdc.php") ) include("templates/".$config['template']."/tmpdc.php"); //include template definition for style used by corepages
else include("templates/sgex/tmpdc.php");

if( $pg == "dbcheck" ){
	include("adm/dbcheck.php");
	include("templates/".$config['template']."/body.php");
} else {
	if( !isset($me) and $pg!="register" and $pg!="credits" ) $pg= "index";
	if( isset($me) and isset($pg) and $pg!="index" ){ //logged in
		if( isbanned($_SESSION['id']) ){
			session_destroy();
			session_start();
			header("Location: index.php?msg=You where banned!");
			exit;	
		}

		switch ($pg) {
			case 'ally':
			case 'barracks':
			case 'battle':
			case 'buildings':
			case 'chat':
			case 'highscores':
			case 'map':
			case 'market':
			case 'message':
			case 'profile':
			case 'research':
			case 'settings':
			case 'credits':
				include 'core-pages/'.$pg.'.php';
				break;
			case 'map2':
				include("templates/".$config['template']."/map2.php");
				break;
			default:
				if( file_exists("templates/".$config['template']."/main.php") ) include("templates/".$config['template']."/main.php");
				else $body="PAGE DOESN'T EXIST!";
		}

		/*$pgbd=""; //plugin TODO
		include("plugins/tut.php");
		$body= $pgbd . $body;*/

		include("templates/".$config['template']."/body.php");
	} else {
		$tuq= mysql_query("SELECT username FROM ".TB_PREFIX."users ORDER BY `id` DESC");
		$tusr= mysql_num_rows($tuq);
		$lastreg= mysql_fetch_array($tuq);

		if( $pg != "index" ){
			if( $pg != "credits" ) include("templates/".$config['template']."/$pg.php");
			else include("core-pages/credits.php");
		}
		
		if( isset($_GET['msg']) ) $blol = "onload=\"javascript:alert('".$_GET['msg']."');\"";
		if( !isset($me) ) include("templates/".$config['template']."/index.php");
	}
}
?>