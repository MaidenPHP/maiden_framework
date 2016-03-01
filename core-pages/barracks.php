<?php
/* --------------------------------------------------------------------------------------
                                       BARRACKS
   Credits         : phpSGEx by Aldrigo Raffaele
   Last modified by: raffa50 19.06.2013
   Comments        : added health
-------------------------------------------------------------------------------------- */

$body="";
$head=file_get_contents("templates/js/barraks.js");

// --------------------------------- SHOW CURRENT UNITS QUEUE ---------------------------
$body.="<table width='600' cellpadding='1'>";
$bqs=mysql_query("SELECT * FROM ".TB_PREFIX."unit_que WHERE `city` ='".$me->city_id."'");
			
while( $rab = mysql_fetch_array($bqs) ){	
	$rtimr= $rab['end']-mtimetn();
	$cqbk= mysql_fetch_array( mysql_query("SELECT `name` FROM `".TB_PREFIX."t_unt` WHERE `id` =".$rab['id_unt']) );
		
	$body.= "<tr><td>".$cqbk['name']." (".$rab['uqnt'].")</td><td class='k'> <div id='blc' class='z'>".$rtimr."<br> 	<a href='?pg=buildings&amp;listid=1&amp;cmd=cancel&amp;planet=1'>Interrompere</a></div> <script language='JavaScript'>			pp = '".$rtimr."';";
	$body.=			"pk = '1';";
	$body.=			"pm = 'cancel';";
	$body.=		"pl = '".$me->city_id."';";
	$body.=			"t();";
	$body.=	"</script></td></tr>";
}
$body.="</table>";

// --------------------------------------- SHOW UNITS -----------------------------------
$body.="<table width='100%' border='1' cellspacing='0' cellpadding='1'>";
$qrtunits = mysql_query("SELECT * FROM ".TB_PREFIX."t_unt WHERE race=".$me->user_info['race']." OR race='0' ORDER BY `etime` ASC");
while( $row = mysql_fetch_array($qrtunits) ){
	$body.= "<form action='' method='post'><input type='hidden' name='act' value='aunt'> <input type='hidden' name='itunt' value='".$row['id']."'>";
	
	$trainunt = true;
	$reqb="";
	$ar= $me->can_trainunt_reqbuildcheck($row['id']);
	if( count($ar) >0 ){
		$trainunt = false;
		$untreqs= $lang['bld_requirements']."<br>";
		for( $i=0; $i < count($ar); $i++ ){
			$bn= mysql_fetch_array( mysql_query("SELECT `name` FROM `t_builds` WHERE `id` =".$ar[$i][0]) );
			$untreqs.= $bn['name']." ".$lang['bld_level'].$ar[$i][1]."<br>";
		}
		$reqb= "<span class='Stile3'>".$untreqs."</span>";
	} else {
		$maxunt= $me->ct_max_unt($row['id']);
		if ( $maxunt >0 ) {
			$trainb="<input name='qunt' id='qunt".$row['id']."' type='number' min='0' size='3' value='0' max='$maxunt' ><br><br>";
			$trainb.=Template::buttonsubmit($lang['amy_train']);		
			$trainb.="<br><br><span class='Stile1'>".$lang['amy_max_units'].": ".$maxunt."</span>";
		} else { 
			$trainunt = false; 
			$reqb= "<span class='Stile3'>".$lang['bld_no_resources']."</span>"; 
		}
	}
	
	$ar= $me->can_trainunt_reqresearchcheck($row['id']);
	if( count($ar) >0 ){
		$trainunt = false;
		$untreqs= "";
		for( $i=0; $i < count($ar); $i++ ){
			$bn= mysql_fetch_array( mysql_query("SELECT name FROM t_research WHERE id =".$ar[$i][0]) );	
			$untreqs.= $bn['name']." ".$lang['bld_level'].$ar[$i][1]."<br>";
		}
		$reqb.=$untreqs;
	}
	
	$timeend= $row['etime'];
	$rescbr= "";
	$resd= mysql_query("SELECT * FROM `".TB_PREFIX."resdata`");
	while( $ftr = mysql_fetch_array($resd) ){ 
		$qbudcost=mysql_query("SELECT * FROM `t_unt_resourcecost` WHERE `unit` =".$row['id']." AND `resource` =".$ftr['id']." LIMIT 1;");
		if( mysql_num_rows($qbudcost) >0 ){
			$acst= mysql_fetch_array($qbudcost);
			$thisrescost= $acst['cost'];	
		} else $thisrescost= 0; //else cost is 0!
		
		if ( !$config['FLAG_SZERORES'] && (int)$thisrescost > 0) {
			if( $config['FLAG_RESICONS'] ) $rescbr .= "<img src='". IRES . $ftr['ico'] . "'/> ";													
			if( $config['FLAG_RESLABEL'] ) $rescbr .= $ftr['name'] . ": ";
		
			$rescbr.= (int)$thisrescost." ";
		}
	}
	
	//number of units that do you have in your city
	$anul=mysql_fetch_array( mysql_query("SELECT * FROM ".TB_PREFIX."units WHERE id_unt='".$row['id']."' AND owner_id='".$me->user_id."' AND `where` = ".$me->city_id) );
	$ncu= $anul['uqnt'];
	if( $ncu=="" ) $ncu=0;
	
	if( $trainunt or $config['FLAG_SUNAVALB'] ) {
		$body.="<tr><td class='l'><div align='center'> <img src='".IUNT.$row['img']."'></div></td><td class='l'><div align='center'> ".$row['name']." (".$lang['amy_level'].": ".$ncu.")<br> <span class='Stile1'>".$rescbr."</span><br><br>".$lang['amy_time']. ": " . (int)$timeend." Sec</div></td><td class='l'><div align='center'><i> \"".$row['desc']."\"</i><br><br><img src='img/icons/health.gif'> ".$row['health']." <img src='img/icons/attack.gif'> ".$row['atk']." <img src='img/icons/defense.gif'> ".$row['dif']." <img src='img/icons/maneuver.gif'> ".$row['vel']."</div></td><td class='k'>";
		if( $trainunt ) $body.=$trainb;
		else $body .= Template::buttondisabled()."<br>" . $reqb;
	
		$body.="</td></tr></form>";
	}		

}

$body.="</table>";

?>