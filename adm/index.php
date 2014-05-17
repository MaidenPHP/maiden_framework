<?php
if ( !file_exists("../config.php") ) header ("Location: ../index.php");
session_start();
require("../config.php");
require("../func.php");
db_connect();

//load (once) game config from database
$config= mysql_fetch_array( mysql_query("SELECT * FROM conf LIMIT 1") );

if( isset($_GET['lang']) ){ include ("../lang/".$_GET['lang'].".php");
	$glang=$_GET['lang'];
	$lkrl="?lang=".$_GET['lang'];
} else { include ("../lang/".LANG.".php"); $glang=LANG; }

if( isset($_SESSION['rank']) && $_SESSION['rank'] >1 ){
	if( isset($_GET['pg']) ) $pg= $_GET['pg'];
	else $pg= "main";
	
	$body="";
	$head="";
	$bol="";
	$secure= true;
	
	//query
	if( isset($_POST['clean_chat']) ) mysql_query("TRUNCATE chat");
	//races
	if( isset($_POST['addrace']) ){
		$rnam= mysql_real_escape_string($_POST['rname']);
		$rdesc= mysql_real_escape_string($_POST['rdesc']);
		$img= mysql_real_escape_string($_POST['img']);
		mysql_query("INSERT INTO `races` (`id`, `rname`, `rdesc`, `img`) VALUES (NULL, '$rnam', '$rdesc', '$img');");	
	}
	
	if( isset($_GET['delrac']) ) mysql_query("DELETE FROM `races` WHERE `id` =".(int)$_GET['delrac']);
	
	if( isset($_POST['eitrac']) ){
		$rnam= mysql_real_escape_string($_POST['rname']);
		$rdesc= mysql_real_escape_string($_POST['rdesc']);
		$img= mysql_real_escape_string($_POST['img']); 
		mysql_query("UPDATE `races` SET `rname` = '$rnam', `rdesc` = '$rdesc', `img` = '$img' WHERE `id` =".(int)$_POST['eitrac']);
	}
	//resources
	if( isset($_POST['addresource']) ){
		$name= mysql_real_escape_string($_POST['name']);
		$prodrate= (int)$_POST['prodrate'];
		$start= (int)$_POST['start'];
		$img= mysql_real_escape_string($_POST['ico']);
		mysql_query("INSERT INTO `resdata` (`id` ,`name` ,`prod_rate` ,`start` ,`ico`) VALUES (NULL ,  '$name',  '$prodrate',  '$start',  '$img');");	
	}
	
	if( isset($_GET['delresource']) ) mysql_query("DELETE FROM `resdata` WHERE `id` =".(int)$_GET['delresource']);
	
	if( isset($_POST['editresource']) ){
		$name= mysql_real_escape_string($_POST['name']);
		$prodrate= (int)$_POST['prodrate'];
		$start= (int)$_POST['start'];
		$img= mysql_real_escape_string($_POST['ico']);
		mysql_query("UPDATE `resdata` SET `name` = '$name', `prod_rate` = '$prodrate', `start` = '$start', `ico` = '$img' WHERE `id` =".(int)$_POST['editresource']);	
	}
	//units
	if( isset($_GET['delunt']) ){
		mysql_query("DELETE FROM `t_unt` WHERE `id` =".(int)$_GET['delunt']);
		mysql_query("DELETE FROM `t_unt_resourcecost` WHERE `unit` =".(int)$_GET['delunt']);
	}
	
	if( isset($_POST['editunt']) ){
		$id= (int)$_POST['editunt'];
		$name= mysql_real_escape_string($_POST['name']);
		$race= (int)$_POST['race'];
		$img= mysql_real_escape_string($_POST['img']);
		$health= (int)$_POST['health'];
		$atk= (int)$_POST['atk'];
		$dif= (int)$_POST['dif'];
		$vel= (int)$_POST['vel'];
		$time= (int)$_POST['time'];
		$desc= mysql_real_escape_string($_POST['desc']);
		mysql_query("UPDATE `t_unt` SET `name` = '$name', `race` = '$race', `img` = '$img', `health` = '$health', `atk` = '$atk', `dif` = '$dif', `vel` = '$vel', `etime` = '$time', `desc` = '$desc' WHERE `id` =$id;");	
		
		//edit resource cost
		$qr= mysql_query("SELECT * FROM resdata");
		while( $row= mysql_fetch_array($qr) ){
			$cost= (int)$_POST["res".$row['id']];
			if( $cost!=0 ) mysql_query("REPLACE INTO `t_unt_resourcecost` (`unit`, `resource`, `cost`) VALUES
($id, ".$row['id'].", $cost);");	
			else mysql_query("DELETE FROM `t_unt_resourcecost` WHERE `unit` = $id AND `resource` = ".$row['id']." LIMIT 1;");
		}
		
		//edit requisites (bud)
		for( $i=1; isset($_POST['reqbud'.$i]); $i++ ) {
			$rb= (int)$_POST['reqbud'.$i];
			$rl= (int)$_POST['levbud'.$i];
			if( $rl >0 ) mysql_query("REPLACE INTO `t_unt_reqbuild` (`unit`, `reqbuild`, `lev`) VALUES ($id, $rb, $rl);");
			else mysql_query("DELETE FROM `t_unt_reqbuild` WHERE `unit` =$id AND `reqbuild` =$rb LIMIT 1;");	
		}
		
		//edit requisites (reserch)
		for( $i=1; isset($_POST['reqresearch'.$i]); $i++ ) {
			$rb= (int)$_POST['reqresearch'.$i];
			$rl= (int)$_POST['levresearch'.$i];
			if( $rl >0 ) mysql_query("REPLACE INTO `t_unt_req_research` (`unit`, `reqresearch`, `lev`) VALUES ($id, $rb, $rl);");
			else mysql_query("DELETE FROM `t_unt_req_research` WHERE `unit` =$id AND `reqresearch` =$rb LIMIT 1;");
		}
	}
	
	if( isset($_POST['addunt']) ){
		$aid= mysql_fetch_array( mysql_query("SELECT * FROM `t_unt` ORDER BY `id` DESC LIMIT 1") );
		if( $aid=="" ) $id =0;
		else $id =(int)$aid['id']+1;
		
		$name= mysql_real_escape_string($_POST['name']);
		$race= (int)$_POST['race'];
		$img= mysql_real_escape_string($_POST['img']);
		$health= (int)$_POST['health'];
		$atk= (int)$_POST['atk'];
		$dif= (int)$_POST['dif'];
		$vel= (int)$_POST['vel'];
		$time= (int)$_POST['time'];
		$desc= mysql_real_escape_string($_POST['desc']);
		mysql_query("INSERT INTO `t_unt` (`id`, `name`, `race`, `img`, `health`, `atk`, `dif`, `vel`, `res_car_cap`, `etime`, `desc`, `type`) VALUES ($id, '$name', '$race', '$img', '$health', '$atk', '$dif', '$vel', '5', '$time', '$desc', NULL);");	
		
		//add resource cost
		$qr= mysql_query("SELECT * FROM resdata");
		while( $row= mysql_fetch_array($qr) ){
			$cost= (int)$_POST["res".$row['id']];
			if( $cost!=0 ) mysql_query("INSERT INTO `t_unt_resourcecost` (`unit`, `resource`, `cost`) VALUES ('$id', '".$row['id']."', '$cost');");	
		}
	}
	//buildings
	if( isset($_GET['delbuild']) ){
		mysql_query("DELETE FROM `t_builds` WHERE `id` =".(int)$_GET['delbuild']);
		mysql_query("DELETE FROM `t_build_resourcecost` WHERE `build` =".(int)$_GET['delbuild']);	
	}
	
	if( isset($_POST['editbuild']) ){
		$id= (int)$_POST['editbuild'];
		$name= mysql_real_escape_string($_POST['name']);
		$func= mysql_real_escape_string($_POST['budfunc']);
		$race= (int)$_POST['race'];
		$prod= (int)$_POST['prod'];
		$img= mysql_real_escape_string($_POST['img']);
		$time= (int)$_POST['time'];
		$timempl= (float)$_POST['timempl'];
		$maxlev= (int)$_POST['maxlev'];
		
		mysql_query("UPDATE `t_builds` SET `name` = '$name', `func` = '$func', `arac` = '$race', `produceres` = '$prod', `img` = '$img', `time` = '$time', `time_mpl` = '$timempl', `maxlev` = $maxlev WHERE `id` =".$id);	
		
		//edit resource cost
		$qr= mysql_query("SELECT * FROM resdata");
		while( $row= mysql_fetch_array($qr) ){
			$cost= (int)$_POST["res".$row['id']];
			$mpl= (float)$_POST['rmpl'.$row['id']];
			if( $cost!=0 ) mysql_query("REPLACE INTO `t_build_resourcecost` (`build`, `resource`, `cost`, `moltiplier`) VALUES ($id, ".$row['id'].", $cost, $mpl);");	
			else mysql_query("DELETE FROM `t_build_resourcecost` WHERE `build` = $id AND `resource` = ".$row['id']." LIMIT 1;");
		}	
		
		//edit requisites (bud)
		for( $i=1; isset($_POST['reqbud'.$i]); $i++ ) {
			$rb= (int)$_POST['reqbud'.$i];
			$rl= (int)$_POST['levbud'.$i];
			if( $rl!=0 ) mysql_query("REPLACE INTO `t_build_reqbuild` (`build`, `reqbuild`, `lev`) VALUES ($id, $rb, $rl);");
			else mysql_query("DELETE FROM `t_build_reqbuild` WHERE `build` =$id AND `reqbuild` =$rb");	
		}
		
		//edit requisites (reserch)
		for( $i=1; isset($_POST['reqresearch'.$i]); $i++ ) {
			$rb= (int)$_POST['reqresearch'.$i];
			$rl= (int)$_POST['levresearch'.$i];
			if( $rl >0 ) mysql_query("REPLACE INTO `t_build_req_research` (`build`, `reqresearch`, `lev`) VALUES ($id, $rb, $rl);");
			else mysql_query("DELETE FROM `t_build_req_research` WHERE `build` =$id AND `reqresearch` =$rb LIMIT 1;");
		}
	}
	
	if( isset($_POST['addbuild']) ) {
		$aid= mysql_fetch_array( mysql_query("SELECT * FROM `t_builds` ORDER BY `id` DESC LIMIT 1") );
		if( $aid=="" ) $id =0;
		else $id =(int)$aid['id']+1;
		
		$name= mysql_real_escape_string($_POST['name']);
		$prod= (int)$_POST['prod'];
		$img= mysql_real_escape_string($_POST['img']);
		$budfunc= mysql_real_escape_string($_POST['budfunc']);
		$time= (int)$_POST['time'];
		$timempl= (float)$_POST['timempl'];
		$maxlev= (int)$_POST['maxlev'];
		
		mysql_query("INSERT INTO `t_builds` (`id`, `arac`, `name`, `func`, `produceres`, `img`, `desc`, `time`, `time_mpl`, `gpoints`, `maxlev`) VALUES ($id, 0, '$name', '$budfunc', $prod, '$img', NULL, $time, $timempl, 15, $maxlev);");	
		
		//add resource cost
		$qr= mysql_query("SELECT * FROM resdata");
		while( $row= mysql_fetch_array($qr) ){
			$cost= (int)$_POST["res".$row['id']];
			$mpl= (float)$_POST['rmpl'.$row['id']];
			if( $cost!=0 ) mysql_query("INSERT INTO `t_build_resourcecost` (`build`, `resource`, `cost`, `moltiplier`) VALUES ('$id', '".$row['id']."', '$cost', '$mpl');");	
		}
	}
	
	if( isset($_POST['editconfig']) ){
		$news= mysql_real_escape_string($_POST['freeRTE_content']);
		$servernam= mysql_real_escape_string($_POST['servernam']);
		$subdesc= mysql_real_escape_string($_POST['subdesc']);
		$maindesc= mysql_real_escape_string($_POST['maindesc']);
		$templ= mysql_real_escape_string($_POST['templ']);
		
		$mapx= (int)$_POST['mapx']; if( $mapx <=0 ) $mapx=500;
		$mapy= (int)$_POST['mapy']; if( $mapy <=0 ) $mapy=500;
		$mapz= ( isset($_POST['mapz']) && (int)$_POST['mapz'] >0 ) ? (int)$_POST['mapz'] : 12;
		
		$FLAG_SZERORES= ( isset($_POST['FLAG_SZERORES']) ) ? 1 : 0;
		$FLAG_SUNAVALB= ( isset($_POST['FLAG_SUNAVALB']) ) ? 1 : 0;
		$FLAG_RESICONS= ( isset($_POST['FLAG_RESICONS']) ) ? 1 : 0;
		$FLAG_RESLABEL= ( isset($_POST['FLAG_RESLABEL']) ) ? 1 : 0;
		
		$MG_max_cap=(float)$_POST['MG_max_cap'];
		$baru_tmdl=(float)$_POST['baru_tmdl'];
		$buildfast_molt=(float)$_POST['buildfast_molt'];
		$researchfast_molt=(float)$_POST['researchfast_molt'];
		
		$popres= (int)$_POST['popres'];
		$popaddpl= (int)$_POST['popaddpl'];
		//save new config
		mysql_query("UPDATE `conf` SET `news1` = '$news', `server_name` = '$servernam', `server_desc_sub` = '$subdesc', `server_desc_main` = '$maindesc', `template` = '$templ', FLAG_SZERORES = $FLAG_SZERORES, FLAG_SUNAVALB = $FLAG_SUNAVALB, FLAG_RESICONS = $FLAG_RESICONS, FLAG_RESLABEL = $FLAG_RESLABEL, MG_max_cap = $MG_max_cap, baru_tmdl = $baru_tmdl, buildfast_molt = $buildfast_molt, researchfast_molt = $researchfast_molt, popres = $popres, popaddpl = $popaddpl, Map_max_x = $mapx, Map_max_y = $mapy, Map_max_z = $mapz WHERE 1 LIMIT 1;");
		//now I have to reload config
		$config= mysql_fetch_array( mysql_query("SELECT * FROM conf LIMIT 1") );
	}
	//research
	if( isset($_GET['delresearch']) ) mysql_query("DELETE FROM `t_research` WHERE id=".(int)$_GET['delresearch']);
	if( isset($_POST['addresearch']) ){
		$aid= mysql_fetch_array( mysql_query("SELECT * FROM `t_research` ORDER BY `id` DESC LIMIT 1") );
		if( $aid=="" ) $id =0;
		else $id =(int)$aid['id']+1;
		
		$name= mysql_real_escape_string($_POST['name']);
		$img= mysql_real_escape_string($_POST['img']);
		$race= (int)$_POST['race'];
		$time= (int)$_POST['time'];
		$timempl= (float)$_POST['timempl'];
		$maxlev= (int)$_POST['maxlev'];
		
		mysql_query("INSERT INTO `t_research` (`id`, `name`, `desc`, `arac`, `img`, `time`, `time_mpl`, `gpoints`, `maxlev`) VALUES ($id, '$name', NULL, $race, '$img', $time, $timempl, 0, $maxlev);");	
		
		//add resource cost
		$qr= mysql_query("SELECT * FROM resdata");
		while( $row= mysql_fetch_array($qr) ){
			$cost= (int)$_POST["res".$row['id']];
			$mpl= (float)$_POST['rmpl'.$row['id']];
			if( $cost!=0 ) mysql_query("INSERT INTO `t_research_resourcecost` (`research`, `resource`, `cost`, `moltiplier`) VALUES ($id, ".$row['id'].", $cost, $mpl);");	
			else mysql_query("DELETE FROM `t_research_resourcecost` WHERE `research` = $id AND `resource` = ".$row['id']." LIMIT 1;");
		}
	}
	if( isset($_POST['editresearch']) ){
		$id= (int)$_POST['editresearch'];
		$name= mysql_real_escape_string($_POST['name']);
		$race= (int)$_POST['race'];
		$img= mysql_real_escape_string($_POST['img']);
		$time= (int)$_POST['time'];
		$timempl= (float)$_POST['timempl'];
		$maxlev= (int)$_POST['maxlev'];
		
		mysql_query("UPDATE `t_research` SET `name` = '$name', `arac` = '$race', `img` = '$img', `time` = '$time', `time_mpl` = '$timempl', `maxlev` = $maxlev WHERE `id` =".$id);	
		
		//edit resource cost
		$qr= mysql_query("SELECT * FROM resdata");
		while( $row= mysql_fetch_array($qr) ){
			$cost= (int)$_POST["res".$row['id']];
			$mpl= (float)$_POST['rmpl'.$row['id']];
			if( $cost!=0 ) mysql_query("REPLACE INTO `t_research_resourcecost` (`research`, `resource`, `cost`, `moltiplier`) VALUES ($id, ".$row['id'].", $cost, $mpl);");	
			else mysql_query("DELETE FROM `t_research_resourcecost` WHERE `research` = $id AND `resource` = ".$row['id']." LIMIT 1;");
		}
		
		//edit requisites (bud)
		for( $i=1; isset($_POST['reqbud'.$i]); $i++ ) {
			$rb= (int)$_POST['reqbud'.$i];
			$rl= (int)$_POST['levbud'.$i];
			if( $rl!=0 ) mysql_query("REPLACE INTO `t_research_reqbuild` (`research`, `reqbuild`, `lev`) VALUES ($id, $rb, $rl);");
			else mysql_query("DELETE FROM `t_research_reqbuild` WHERE `research` =$id AND `reqbuild` =$rb");	
		}	
		
		//edit requisites (reserch)
		for( $i=1; isset($_POST['reqresearch'.$i]); $i++ ) {
			$rb= (int)$_POST['reqresearch'.$i];
			$rl= (int)$_POST['levresearch'.$i];
			if( $rl >0 ) mysql_query("REPLACE INTO `t_research_req_research` (`research`, `reqresearch`, `lev`) VALUES ($id, $rb, $rl);");
			else mysql_query("DELETE FROM `t_research_req_research` WHERE `research` =$id AND `reqresearch` =$rb LIMIT 1;");
		}	
	}
	
	//moderator
	if( isset($_POST['editct']) ){
		mysql_query("UPDATE `city` SET `name` = '".$_POST['name']."' WHERE `id` =".(int)$_POST['editct']);	
		
		$resnam= get_resources_name();
		for( $i=1; $i <= count($resnam); $i++ ) mysql_query("UPDATE `city_resources` SET `res_quantity` = '".(int)$_POST['res'.$i]."' WHERE `city_id` =".(int)$_POST['editct']." AND `res_id` =".$i);
	}
	
	if( isset($_GET['ban']) ){
		mysql_query("INSERT INTO `banlist` VALUES ('".(int)$_GET['ban']."', '".( mtimetn() + $_POST['bantime'] *86400 )."', '');");	
	}
	
	if( isset($_GET['pardon']) ){
		mysql_query("DELETE FROM banlist WHERE usrid =".(int)$_GET['pardon']);
	}
	
	//edit user
	if( isset($_POST['editusr']) ){
		mysql_query("UPDATE `users` SET `email` = '".$_POST['email']."', `points` = '".(int)$_POST['points']."', `rank` = '".(int)$_POST['rank']."' WHERE `id` =".(int)$_POST['editusr']);	
	}
	
	if( isset($_POST['sendmailtoall']) ){
		$qr= mysql_query("SELECT `email` FROM `users`");
		while( $row= mysql_fetch_array($qr) ){
			email( $row['email'], $_POST['tittle'], $_POST['msg'] );
		}
	}
	//---
	
	require($pg.".php");
	include("../templates/sgex/admcp.php");
} else header("Location: ../index.php");
?>