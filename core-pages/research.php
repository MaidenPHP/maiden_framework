<?php
/* --------------------------------------------------------------------------------------
                                      RESEARCH
   Credits         : phpSGEx by Aldrigo Raffaele
   Last modified by: Aldrigo Raffaele 09.07.2013
   Comments        : Fix maxlev check
-------------------------------------------------------------------------------------- */

	$body="";
	$head=file_get_contents("templates/js/research.js");

// --------------------------------- SHOW CURRENT RESEARCH QUEUE ------------------------
$body.="<table width='600' cellpadding='1'>";
$rqs=mysql_query("SELECT * FROM ".TB_PREFIX."city_research_que WHERE `usr` ='".$me->user_id."'");
			
while( $rab = mysql_fetch_array($rqs) ){	
	$rtimr= $rab['end']-mtimetn();
	$cqbk= mysql_fetch_array( mysql_query("SELECT `name` FROM `".TB_PREFIX."t_research` WHERE `id` =".$rab['res_id']) );	
	$lp = $me->get_research_level($rab['res_id']) +1;
	$body.= "<tr><td>".$cqbk['name']." (".$lang['lab_level'].": ".$lp.")</td><td class='k'> <div id='blc' class='z'>".$rtimr."<br> <a href='?pg=research&amp;listid=1&amp;cmd=cancel&amp;city=1'>" . $lang['lab_cancel'] . "</a></div> <script language='JavaScript'> pp = '".$rtimr."'; pk = '1'; pm = 'cancel'; pl = '".$me->city_id."'; t(); </script></td></tr>";
}
$body.="</table>";
	
// -------------------------------------- SHOW RESEARCHES -------------------------------
	$body.="<table width='100%' border='1' cellspacing='0' cellpadding='1'>";
	
	$qrresearch= mysql_query("SELECT * FROM t_research WHERE `arac` =0 OR `arac` =".$me->user_info['race']." ORDER BY `time` ASC");
	while( $researchrow= mysql_fetch_array($qrresearch) ){
		$lev= $me->get_research_level($researchrow['id']);
		$timend= $me->research_calc_time( $researchrow['time'], $researchrow['time_mpl'], $lev );
		//resource info
		$rescbr="";
		$resd= mysql_query("SELECT * FROM `".TB_PREFIX."resdata`");
		while( $ftr= mysql_fetch_array($resd) ){ 
			$qrrescost= mysql_query("SELECT * FROM t_research_resourcecost WHERE research =".$researchrow['id']." AND resource =".$ftr['id']." LIMIT 1;");
			if( mysql_num_rows($qrrescost) >0 ){
				$acst= mysql_fetch_array($qrrescost);
				$thisrescost= $acst['cost'] + ($lev *$acst['cost'] *$acst['moltiplier']);	
			} else $thisrescost= 0; //else cost is 0!
			
			if ( !$config['FLAG_SZERORES'] && (int)$thisrescost > 0) {
				if( $config['FLAG_RESICONS'] ) $rescbr .= "<img src='". IRES . $ftr['ico'] . "'/> ";														
				if( $config['FLAG_RESLABEL'] ) $rescbr .= $ftr['name'] . ": ";
		
				$rescbr.= (int)$thisrescost." ";
			}
		}
		
		if( $researchrow['maxlev'] ==0 || $lev < $researchrow['maxlev'] ){	
			if( $me->can_research_resourcecheck($researchrow['id']) ){
				$build=true;
				$ar = $me->can_research_reqbuildcheck($researchrow['id']);	
				if( count($ar) !=0 ){
					$build= false;
					$buildreqs= $lang['lab_requirements'].":<br>";
					for( $i=0; $i < count($ar); $i++ ){
						$bn= mysql_fetch_array( mysql_query("SELECT `name` FROM `t_builds` WHERE `id` =".$ar[$i][0]." LIMIT 1") );
						$buildreqs.= $bn['name']." ".$lang['lab_level'].$ar[$i][1]."<br>";
					}
				}
				
				if ($build) {
					$contb= "<a href='?pg=research&research=".$researchrow['id']."'>".Template::link("",$lang['lab_research'])."</a>";
				} else {
					$contb= "<span class='Stile3'>".$buildreqs."</span>";
				}
			
			} else {
				$build= false;
				$contb= $lang['bld_no_resources'];
			}
		} else {
			$build= false;
			$contb="<div align='center'><span class='Stile3'>Max level!</span></div>";	
		}
		
		if( $config['FLAG_SUNAVALB'] ) {
			$body.="<tr><td><img src='img/research/".$researchrow['img']."'></td><td>".$researchrow['name']." (". $lang['lab_level'] . ": ".$lev.")<br><span class='Stile1'>$rescbr </span><br>Time: ".sectotime($timend)."</td> <td>$contb</td></tr>";
		} else {
			if ($build) {
				$body.="<tr><td><img src='img/research/".$researchrow['img']."'></td><td>".$researchrow['name']." (". $lang['lab_level'] . ": ".$lev.")<br><span class='Stile1'>$rescbr </span><br>Time: ".sectotime($timend)."</td> <td>$contb</td></tr>";
			}		
		}

	}
	
	$body.="</table>";
?>