<?php
/* --------------------------------------------------------------------------------------
                                      BUILDINGS
   Credits         : phpSGEx by Aldrigo Raffaele
   Last modified by: Aldrigo Raffaele 09.07.2013
   Comments        : Fix maxlev check
-------------------------------------------------------------------------------------- */

$body="";
$head=file_get_contents("templates/js/buildings.js");

// --------------------------------- SHOW CURRENT BUILDING QUEUE ------------------------
$body.="<table width='600' cellpadding='1'>";
$bqs=mysql_query("SELECT * FROM ".TB_PREFIX."city_build_que WHERE `city` ='".$me->city_id."'");
			
while( $rab = mysql_fetch_array($bqs) ){	
	$rtimr= $rab['end']-mtimetn();
	$cqbk= mysql_fetch_array( mysql_query("SELECT `name`, `func` FROM `".TB_PREFIX."t_builds` WHERE `id` =".$rab['build']) );
		
	$lp = $me->get_build_level($rab['build']) +1;
	$body.= "<tr><td>".$cqbk['name']." (".$lang['bld_level'].": ".$lp.")</td><td class='k'> <div id='blc' class='z'>".$rtimr."<br> <a href='?pg=buildings&amp;listid=1&amp;cmd=cancel&amp;city=1'>" . $lang['bld_cancel'] . "</a></div> <script language='JavaScript'> pp = '".$rtimr."'; pk = '1'; pm = 'cancel'; pl = '".$me->city_id."'; t(); </script></td></tr>";
}
$body.="</table>";

// ------------------------------- SHOW BUILDINGS (CITY SYSTEM 1) -----------------------
$body.="<table width='100%' border='1' cellspacing='0' cellpadding='1'>";
	
$qrbuilds= mysql_query("SELECT * FROM ".TB_PREFIX."t_builds WHERE `arac` =0 OR `arac` =".$me->user_info['race']." ORDER BY `time` ASC");
while( $ftbudrow = mysql_fetch_array($qrbuilds) ){
	$lev= $me->get_build_level($ftbudrow['id']);
	//resource info
	$rescbr="";
	$resd= mysql_query("SELECT * FROM `".TB_PREFIX."resdata`");
	while( $riga= mysql_fetch_array($resd) ){ 
		$qbudcost=mysql_query("SELECT * FROM `t_build_resourcecost` WHERE `build` =".$ftbudrow['id']." AND `resource` =".$riga['id']." LIMIT 1;");
		if( mysql_num_rows($qbudcost) >0 ){
			$acst= mysql_fetch_array($qbudcost);
			$thisrescost= $acst['cost'] + ($lev *$acst['cost'] *$acst['moltiplier']);	
		} else $thisrescost= 0; //else cost is 0!
		
		if ( !$config['FLAG_SZERORES'] && (int)$thisrescost > 0) {
			if( $config['FLAG_RESICONS'] ) $rescbr .= "<img src='". IRES . $riga['ico'] . "'/> ";														
			if( $config['FLAG_RESLABEL'] ) $rescbr .= $riga['name'] . ": ";
		
			$rescbr.= (int)$thisrescost." ";
		}
	}
	//can build?
	if( $ftbudrow['maxlev'] ==0 || $lev < $ftbudrow['maxlev'] ){
		if( $me->can_build_resourcecheck($ftbudrow['id']) ){
			$build= true;
			
			$ar = $me->can_build_reqbuildcheck($ftbudrow['id']);	
			if( count($ar) !=0 ){
				$build= false;
				$buildreqs= $lang['bld_requirements'].":<br>";
				for( $i=0; $i < count($ar); $i++ ){
					$bn= mysql_fetch_array( mysql_query("SELECT `name` FROM `t_builds` WHERE `id` =".$ar[$i][0]." LIMIT 1") );
					$buildreqs.= $bn['name']." (" . $lang['bld_level'] . " " . $ar[$i][1].")<br>";
				}
			}
	
			if ($build) {
				if ($lev <= 0) {
					$ctb="<div align='center'><a href='?pg=buildings&bid=".$ftbudrow['id']."'><b>".Template::link("",$lang['bld_build'])."</b></a></div>";
				} else {
					$ctb="<div align='center'><a href='?pg=buildings&bid=".$ftbudrow['id']."'><b>".Template::link("",$lang['bld_build'])."</b></a></div>";
				}
			} else {
				$ctb= "<div align='center'><span class='Stile3'>".$buildreqs."</span></div>";
			}
			
		} else {
			$build= false;
			$ctb="<div align='center'><span class='Stile3'>".$lang['bld_no_resources']."</span></div>";
		}
	} else {
		$build= false;
		$ctb="<div align='center'><span class='Stile3'>Max level!</span></div>";	
	}
	
	$tm= $me->build_calc_time($ftbudrow['time'], $ftbudrow['time_mpl'], $lev);
	
	if( $config['FLAG_SUNAVALB'] ) {
		$body.="<tr><td class='l'><div align='center'> <img border='0' src='".IBUD.$ftbudrow['img']."'></div></td><td class='k'><div align='center'> ".$ftbudrow['name']." (".$lang['bld_level'].": ".$lev.")</div><span class='Stile1'>".$rescbr."</span><br>".$lang['bld_time'] . ": " . sectotime($tm) . " </td><td class='k'><div align='center'><i> \"".$ftbudrow['desc']."</i> \"</div></td><td class='k'>".$ctb."</td></tr>";
	} else {
		if ($build) {
			$body.="<tr><td class='l'><div align='center'> <img border='0' src='".IBUD.$ftbudrow['img']."'></div></td><td class='k'><div align='center'> ".$ftbudrow['name']." (".$lang['bld_level'].": ".$lev.")</div><span class='Stile1'>".$rescbr."</span><br>".$lang['bld_time'] . ": " . sectotime($tm) . " </td><td class='k'><div align='center'><i> \"".$ftbudrow['desc']."</i> \"</div></td><td class='k'>".$ctb."</td></tr>";		
		}
	}

}
	
$body.="</table>";

?>