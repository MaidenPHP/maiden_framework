<?php

/* --------------------------------------------------------------------------------------
                                      COMMON FUNCTIONS
   Credits         : phpSGEx by Aldrigo Raffaele
   Last modified by: Raffa50 14.01.2014
   Comments        : Added isonline funcion
-------------------------------------------------------------------------------------- */

function db_connect() {  //conection to de db (parameters define in config,php wich was included)
	mysql_connect(SQL_SERVER, SQL_USER, SQL_PASS);
	mysql_select_db(SQL_DB);
}

function mtimetn(){  //time stamp now
	//data oggi
	$dt=date("j");
	$mt=date("n");
	$yt=date("y");
	//ore oggi
	$timenow=getdate();
	$hn=substr("0" . $timenow["hours"], -2);
	$mih=substr("0" . $timenow["minutes"], -2);
	$sn=substr("0" . $timenow["seconds"], -2);
	$mtimet=mktime($hn,$mih,$sn,$mt,$dt,$yt); //data e ora oggi pronto per le operazioni e per il db!

	return $mtimet;
}

function sectotime($sec){
	$s = $sec % 60;
	$m = $sec / 60;
	$h = $m / 60;
	$m = $m % 60;
	
	$r = (int)$h."h ".(int)$m."m ".(int)$s."s ";
	return $r;	
}

function clean_unitsquee(){ //delete row from units if uqnt=0
	mysql_query("DELETE FROM `".TB_PREFIX."units` WHERE `uqnt` =0");
}

function get_resources_name(){
	$qr= mysql_query("SELECT `name` FROM `".TB_PREFIX."resdata`");	
	$i=1;
	while( $row = mysql_fetch_array($qr) ){
		$res[$i] = $row["name"];
		$i++;
	}
	return $res;
}

// -------------------------------- GET_RESOURCE_ICONS -------------------------------- //
function get_resource_icons(){
	$qr= mysql_query("SELECT `ico` FROM `".TB_PREFIX."resdata`");	
	$i=1;
	while( $row = mysql_fetch_array($qr) ){
		$res[$i] = $row["ico"];
		$i++;
	}
	return $res;
}

function get_user($id){
	$un= mysql_fetch_array( mysql_query("SELECT * FROM users WHERE id= $id LIMIT 1;") );
	return $un;
}

function isonline($id){
	$un= get_user($id);
	$ll= $un['last_log'];
	
	$ymg = explode(" ", $ll);
	$s = explode("-", $ymg[0]);
	$Y = $s[0];
	$M = $s[1];
	$G = $s[2];
	
	$z = explode(":", $ymg[1]);
	$h = $z[0];
	$m = $z[1];
	$s = $z[2];
	
	$tmll= ( mtimetn() - mktime($h,$m,$s,$M,$G,$Y) ) / 60;
	return $tmll < 5;	
}

function get_city($id){
	$ct= mysql_fetch_array( mysql_query("SELECT * FROM city WHERE id= $id LIMIT 1;") );
	return $ct;
}

function db_ver() {  //return PhpSgeX DB version (useful for update the db!)
	$svqr=mysql_query("SELECT sge_ver FROM ".TB_PREFIX."conf");
	$vvc=mysql_fetch_array($svqr);
	if($svqr) return $vvc['sge_ver'];
	else return false;
}

function get_ally_points($ally){
	$pt=0;
	$qr= mysql_query("SELECT points FROM `users` WHERE `ally_id` =".(int)$ally);	
	while( $row= mysql_fetch_array($qr) ){
		$pt += $row['points'];	
	}
	return $pt;
}

function sortunitbyvel($aunt){ //sort units by speed, used in battle sys (must be rewritten with mergesort)
	$sav= new unitdata;
	for( $i=0; $i < count($aunt); $i++ ){
		$vel= $aunt[$i]->uvel;
		for( $j=$i+1; $j < count($aunt); $j++){
			if(	$aunt[$j]->uvel > $vel ){
				$vel= $aunt[$j]->uvel;
				$k= $j;
			}
		}
		$sav= $aunt[$i];
		$aunt[$i]= $aunt[$k];
		$aunt[$k]= $sav;
	}	
	return $aunt;
}

function bud_func(){ //show buildings func
	$bf= array();
	$bf[0]= "none";
	
	$bf[]="barraks";
	$bf[]="reslab";
	$bf[]="buildfaster";
	
	if( MAG_E==1 ) $bf[]="mag_e";	
	if( POP_E==1 ) $bf[]="pop_e";
	return $bf;
}
?>