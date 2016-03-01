<?php
//add new res
$racsel="<select name='race'><option value='0'>All</option>";
$qrac= mysql_query("SELECT * FROM races");
while( $racr= mysql_fetch_array($qrac) ) $racsel.="<option value='".$racr['id']."'>".$racr['rname']."</option>"; 
$racsel.="</select>";

function get_rescost( $rid=0 ){
	$rescost="<table border='0'>";
	$qrres= mysql_query("SELECT * FROM resdata");
	while( $ftr= mysql_fetch_array($qrres) ){
		if( $rid ==0 ) $rescost.="<tr><td>".$ftr['name'].":</td> <td><input type='text' name='res".$ftr['id']."' value='0' size='3'></td>
		<td>X: <input type='text' size='3' name='rmpl".$ftr['id']."' value='0'></td></tr>";
		else {
			$qrcost= mysql_query("SELECT * FROM `t_build_resourcecost` WHERE `build` =$rid AND resource =".$ftr['id']);
			if( mysql_num_rows($qrcost) ==0 ) { $cost=0; $molt= 0; }
			else {
				$ar= mysql_fetch_array($qrcost);
				$cost= $ar['cost'];	
				$molt= $ar['moltiplier'];
			}
			$rescost.="<tr><td>".$ftr['name'].":</td> <td><input type='text' size='3' name='res".$ftr['id']."' value='$cost'></td>
			 <td>X: <input type='text' size='3' name='rmpl".$ftr['id']."' value='".$molt."'></td></tr>";
		}
	}
	$rescost.="</table>";
	return $rescost;
}


$body.="<form method='post' action='?pg=research'></table> <h2>".$lang['adm_addresearch']."</h2> <table border='1' width='100%'> <tr> <td>".$lang['adm_name']."</td> <td>Cost / Moltiplier per level</td> </tr>
<tr> <td>".$lang['adm_name']." <input type='text' name='name'> <br>".$lang['adm_image']." <input type='text' name='img' value='null.gif' size='9'> <br>".$lang['reg_race']." $racsel <br>Max lev: <input type='text' name='maxlev' value='0' size='3'></td> 
<td>".get_rescost()." Time: <input type='text' name='time' size='3' value='0'> X: <input type='text' name='timempl' value='0' size='3'></td> <td><input type='image' src='../img/icons/add-icon.png' onclick='document.thisform.submit()' name='addresearch' value=' ' /></td> </tr> </table></form>";

//edit res
$body.="<br><br><table border='1' width='100%'>";
$qr= mysql_query("SELECT * FROM t_research");
while( $row= mysql_fetch_array($qr) ){
	//select for race
	$racsel="<select name='race'><option value='0'>All</option>";
	$qrac= mysql_query("SELECT * FROM races");
	while( $racr= mysql_fetch_array($qrac) ) { 
		$racsel.="<option value='".$racr['id']."'";
		if( $racr['id']==$row['arac'] ) $racsel.="selected";
		$racsel.=">".$racr['rname']."</option>"; 
	}
	$racsel.="</select>";
	//select for resource cost
	$rescost="";
	$qrres= mysql_query("SELECT * FROM resdata");
	while( $ftr= mysql_fetch_array($qrres) ){
		$qrcost= mysql_query("SELECT * FROM `t_build_resourcecost` WHERE `build` =".$row['id']." AND resource =".$ftr['id']);
		if( mysql_num_rows($qrcost) ==0 ) { $cost=0; $molt= 0; }
		else {
			$ar= mysql_fetch_array($qrcost);
			$cost= $ar['cost'];	
			$molt= $ar['moltiplier'];
		}
		$rescost.=$ftr['name'].": <input type='text' size='3' name='res".$ftr['id']."' value='$cost'>
		 X: <input type='text' size='3' name='rmpl".$ftr['id']."' value='".$molt."'><br>";
	}
	
	$qrreqbud= mysql_query("SELECT * FROM `t_research_reqbuild` WHERE `research` =".$row['id']);
	$i=1; $reqbud="";
	while( $arb= mysql_fetch_array($qrreqbud) ) {
		$reqbud.="<select name='reqbud$i'>";
		
		$qrgetbuilds= mysql_query("SELECT * FROM t_builds");
		while( $gb= mysql_fetch_array($qrgetbuilds) ){
			if( $gb['id']!=$row['id'] ) {
				$reqbud.="<option value='".$gb['id']."' ";
				if( $gb['id']==$arb['reqbuild'] ) $reqbud.="selected";
				$reqbud.=">".$gb['name']."</option>";
			}
		}
			
		$reqbud.="</select>: <input type='text' name='levbud$i' value='".$arb['lev']."' size='1'></nobr> <br>";
		$i++;
	}
	
	$reqbud.="<nobr> <div id='radd".$row['id']."' style='display:none;'><select name='reqbud$i'>";
	$qrgetbuilds= mysql_query("SELECT * FROM t_builds");
	while( $gb= mysql_fetch_array($qrgetbuilds) ) if( $gb['id']!=$row['id'] ) $reqbud.="<option value='".$gb['id']."'>".$gb['name']."</option>";
	
	$reqbud.="</select>: <input type='text' name='levbud$i' value='0' size='1'></div></nobr> <br><a href='#' onclick=\"javascript: showHide(radd".$row['id'].");\">".$lang['adm_add']."</a>";
	
	$body.="<form method='post' action='?pg=research'><input type='hidden' name='editresearch' value='".$row['id']."' >
	<tr> <td style='text-align: left'>Name: <br><input type='text' name='name' value='".$row['name']."' size='15'> <br>".$lang['adm_image'].": <br><input type='text' name='img' value='".$row['img']."' size='9'> <br>".$lang['reg_race'].": <br>$racsel <br>Max lev: <br><input type='text' name='maxlev' value='".$row['maxlev']."' size='3'></td> 
	<td>$rescost Time: <input type='text' name='time' value='".$row['time']."' size='3'> X: <input type='text' size='3' name='timempl' value='".$row['time_mpl']."'></td> <td>$reqbud</td> 
	
	<td>";
	
	//research research req
	$qrreqresearch= mysql_query("SELECT * FROM t_research_req_research WHERE research =".$row['id']);
	$i=1;
	while( $arb= mysql_fetch_array($qrreqresearch) ){
		$body.="<select name='reqresearch$i'>";	
		
		$qrgetresearch= mysql_query("SELECT * FROM t_research");
		while( $gb= mysql_fetch_array($qrgetresearch) ){
			$body.="<option value='".$gb['id']."' ";
			if( $gb['id']==$arb['reqresearch'] ) $body.="selected";
			$body.=">".$gb['name']."</option>";	
		}
		
		$body.="</select> <input type='text' name='levresearch$i' value='".$arb['lev']."' size='1'> <br>";
		$i++;
	}
	
	$body.="<nobr> <div id='raddrs".$row['id']."' style='display:none;'><select name='reqresearch$i'>";
	
	$qrgetreseach= mysql_query("SELECT * FROM t_research");
	while( $gb= mysql_fetch_array($qrgetreseach) ) $body.="<option value='".$gb['id']."'>".$gb['name']."</option>";
	
	$body.="</select>: <input type='text' name='levresearch$i' value='0' size='1'></div></nobr> <br><a href='#' onclick=\"javascript: showHide(raddrs".$row['id'].");\">".$lang['adm_add']."</a>
	</td>
	
	<td><input type='image' src='../img/icons/b_edit.png' onclick='document.thisform.submit()' /> <a href='?pg=research&delresearch=".$row['id']."'><img src='../img/icons/x.png' /></a></td> </tr>";
}
$body.="</table>";
?>