<?php
function get_build_resource_cost($bid=0){
	$rescost="<table width='100%' border='0' class='tbleft'>";
	$qrres= mysql_query("SELECT * FROM resdata");
	while( $ftr= mysql_fetch_array($qrres) ) 
		if( $bid==0 ) $rescost.="<tr><td>".$ftr['name'].":</td> <td><input type='text' name='res".$ftr['id']."' value='0' size='3'></td> 
								<td>X: <input type='text' size='3' name='rmpl".$ftr['id']."' value='0'></td></tr>";
		else {
			$qrcost= mysql_query("SELECT * FROM `t_build_resourcecost` WHERE `build` =$bid AND resource =".$ftr['id']);
			if( mysql_num_rows($qrcost) ==0 ) { $cost=0; $molt= 0; }
			else {
				$ar= mysql_fetch_array($qrcost);
				$cost= $ar['cost'];	
				$molt= $ar['moltiplier'];
			}
			$rescost.="<tr><td>".$ftr['name'].":</td> <td><input type='text' size='3' name='res".$ftr['id']."' value='$cost'></td>
			<td>X: <input type='text' size='3' name='rmpl".$ftr['id']."' value='".$molt."'></td></tr>";
		}
	
	if( $bid==0 ) $timemolt= $time=0; 
	else{
		$far= mysql_fetch_array( mysql_query("SELECT `time`, `time_mpl` FROM `t_builds` WHERE `id` =$bid") );
		$time = $far['time'];
		$timemolt = $far['time_mpl'];
	}
	$rescost.="<tr><td>Time:</td> <td><input type='text' name='time' size='3' value='$time'></td> <td>X: <input type='text' name='timempl' value='$timemolt' size='3'></td></tr>
	</table>";
	
	return $rescost;
}

//add new build
$racsel="<select name='race'><option value='0'>All</option>";
$qrac= mysql_query("SELECT * FROM races");
while( $racr= mysql_fetch_array($qrac) ) $racsel.="<option value='".$racr['id']."'>".$racr['rname']."</option>"; 
$racsel.="</select>";

$prodsel="<select name='prod'><option value='0'>None</option>";
$qprod= mysql_query("SELECT * FROM resdata");
while( $rprod= mysql_fetch_array($qprod) ) $prodsel.="<option value='".$rprod['id']."'>".$rprod['name']."</option>"; 
$prodsel.="</select>";

$rescost="";
$qrres= mysql_query("SELECT * FROM resdata");
while( $ftr= mysql_fetch_array($qrres) ) $rescost.=$ftr['name'].": <input type='text' name='res".$ftr['id']."' value='0' size='3'> X: <input type='text' size='3' name='rmpl".$ftr['id']."' value='0'><br>";

//select for resource function
$bf= bud_func();
$budfunc="<select name='budfunc'>";
for( $i=0; $i< count($bf); $i++ ){
	$budfunc.= "<option value='".$bf[$i]."'>".$bf[$i]."</option>";	
}
$budfunc.="</select>";

$body.="<form method='post' action='?pg=buildings'> <h2>".$lang['adm_addbuild']."</h2> 
<table border='1' width='100%' class='tbleft'> <tr><td>".$lang['adm_name']."/".$lang['adm_image']."/".$lang['reg_race']."</td> <td>".$lang['adm_prodres']."</td> <td>Cost / Moltiplier per level</td> </tr>
<tr><td>
	<table border='0' width='100%' class='tbleft'><tr><td>".$lang['adm_name'].":</td> <td><input type='text' name='name' size='15'></td></tr>
		<tr><td>".$lang['adm_image'].":</td> <td><input type='text' name='img' value='null.gif' size='9'></td></tr>
		<tr><td>".$lang['reg_race'].":</td> <td>$racsel</td></tr>
	</table>
	</td><td>
		<table border='0' width='100%' class='tbleft'><tr><td>".$lang['adm_prodres'].":</td> <td>$prodsel</td></tr>
			<tr><td>Function:</td> <td>$budfunc</td></tr>
			<tr><td>Max Lev:</td> <td><input name='maxlev' type='text' value='0' size='3'></td></tr>
		</table>
	</td><td>".get_build_resource_cost()."</td> <td><input type='image' src='../img/icons/add-icon.png' onclick='document.thisform.submit()' name='addbuild' value=' ' /></td>  </tr>
</table></form>";

//edit builds
$body.= "<form method='get' action=''><input type='hidden' name='pg' value='buildings'>
<br><br>Show by race: <select name='srac'><option value='0'>All</option>";

$qrac= mysql_query("SELECT * FROM races");
while( $racr= mysql_fetch_array($qrac) ) $body.= "<option value='".$racr['id']."'>".$racr['rname']."</option>"; 

$body.="</select> <input type='submit' value='ok'></form><br>
<table border='1' width='105%'> <tr> <td>Name/Img/Race</td> <td>".$lang['adm_prodres']."</td> <td>Cost / Moltiplier</td> <td>".$lang['adm_requirements']."</td> </tr>";

if( isset($_GET['srac']) && $_GET['srac'] >0 ) $qr= mysql_query("SELECT * FROM t_builds WHERE arac =".(int)$_GET['srac']);
else $qr= mysql_query("SELECT * FROM t_builds");
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
	//select for resource production
	$prodsel="<select name='prod'><option value='0'>None</option>";
	$qprod= mysql_query("SELECT * FROM resdata");
	while( $rprod= mysql_fetch_array($qprod) ) { 
		$prodsel.="<option value='".$rprod['id']."'";
		if( $rprod['id']==$row['produceres'] ) $prodsel.="selected";
		$prodsel.=">".$rprod['name']."</option>"; 
	}
	$prodsel.="</select>";
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
	//select for resource function
	$bf= bud_func();
	$budfunc="<select name='budfunc'>";
	for( $i=0; $i< count($bf); $i++ ){
		$budfunc.= "<option ";
		if( $bf[$i]==$row['func'] ) $budfunc.="selected ";
		$budfunc.= "value='".$bf[$i]."'>".$bf[$i]."</option>";	
	}
	$budfunc.="</select>";
	
	$body.="<form method='post' action='?pg=buildings'><input type='hidden' name='editbuild' value='".$row['id']."' >
	<tr> <td style='text-align: left'>Name: <br><input type='text' name='name' value='".$row['name']."' size='15'> <br>".$lang['adm_image'].": <br><input type='text' name='img' value='".$row['img']."' size='9'> <br>".$lang['reg_race'].": <br>$racsel</td> 
	<td>".$lang['adm_prodres'].": <br>$prodsel<br><br>Function: <br>$budfunc <br>Max Lev: <br><input name='maxlev' type='text' value='".$row['maxlev']."' size='3'></td> 
	<td>".get_build_resource_cost($row['id'])."</td> 
	<td><nobr>";
	
	$qrreqbud= mysql_query("SELECT * FROM `t_build_reqbuild` WHERE `build` =".$row['id']);
	$i=1;
	while( $arb= mysql_fetch_array($qrreqbud) ) {
		$body.="<select name='reqbud$i'>";
		
		$qrgetbuilds= mysql_query("SELECT * FROM t_builds");
		while( $gb= mysql_fetch_array($qrgetbuilds) ){
			if( $gb['id']!=$row['id'] ) {
				$body.="<option value='".$gb['id']."' ";
				if( $gb['id']==$arb['reqbuild'] ) $body.="selected";
				$body.=">".$gb['name']."</option>";
			}
		}
			
		$body.="</select>: <input type='text' name='levbud$i' value='".$arb['lev']."' size='1'></nobr> <br>";
		$i++;
	}
	
	$body.="<nobr> <div id='radd".$row['id']."' style='display:none;'><select name='reqbud$i'>";
	
	$qrgetbuilds= mysql_query("SELECT * FROM t_builds");
	while( $gb= mysql_fetch_array($qrgetbuilds) ) if( $gb['id']!=$row['id'] ) $body.="<option value='".$gb['id']."'>".$gb['name']."</option>";
	
	$body.="</select>: <input type='text' name='levbud$i' value='0' size='1'></div></nobr> <br><a href='#' onclick=\"javascript: showHide(radd".$row['id'].");\">".$lang['adm_add']."</a> </td>
	<td>";
	
	//build research req
	$qrreqresearch= mysql_query("SELECT * FROM t_build_req_research WHERE build =".$row['id']);
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
	
	$body.="</select>: <input type='text' name='levresearch$i' value='0' size='1'></div></nobr> <br><a href='#' onclick=\"javascript: showHide(raddrs".$row['id'].");\">".$lang['adm_add']."</a></td>
	<td><input type='image' src='../img/icons/b_edit.png' onclick='document.thisform.submit()' />
	<a href='?pg=buildings&delbuild=".$row['id']."'><img src='../img/icons/x.png' /></a></td> </tr></form>";	
}

$body.="</table>";
?>