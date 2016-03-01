<?php
//add unit
$racsel="<select name='race'><option value='0'>All</option>";
$qrac= mysql_query("SELECT * FROM races");
while( $racr= mysql_fetch_array($qrac) ) $racsel.="<option value='".$racr['id']."'>".$racr['rname']."</option>"; 
$racsel.="</select>";

$rescost="";
$qr= mysql_query("SELECT * FROM resdata");
while( $ftr= mysql_fetch_array($qr) ) { $rescost.=$ftr['name'].": <input type='text' size='3' name='res".$ftr['id']."' value='0'> <br>"; }

$body.="<form action='?pg=units' method='post'></table> <br><h2>".$lang['adm_addunit']."</h2>
<table border='1' width='105%'> <tr><td>".$lang['adm_name']."/".$lang['reg_race']."/".$lang['adm_image']."</td> <td>Atk/Dif/Vel</td> <td>Cost</td><td>".$lang['adm_desc']."</td></tr>
<tr><td>Name:<br> <input type='text' name='name' size='15'> <br>Race: <br>$racsel <br>Image: <br><input type='text' name='img' value='null.gif'></td>
<td>Health: <input type='text' name='health' value='100' size='3'><br>Atk: <input type='text' size='3' name='atk' value='0'> <br>Dif: <input type='text' size='3' name='dif' value='0'> <br>Vel: <input type='text' size='3' name='vel' value='0'></td> <td>$rescost Time: <input type='text' name='time' size='3' value='0'></td><td><textarea name='desc'></textarea></td> <td><input type='image' src='../img/icons/add-icon.png' onclick='document.thisform.submit()' name='addunt' value=' ' /></td>  </tr>
</table></form>";

//edit units
$body.= "<br><br><form method='get' action=''><input type='hidden' name='pg' value='units'>
Show by race: <select name='srac'><option value='0'>All</option>";

$qrac= mysql_query("SELECT * FROM races");
while( $racr= mysql_fetch_array($qrac) ) $body.= "<option value='".$racr['id']."'>".$racr['rname']."</option>"; 

$body.="</select> <input type='submit' value='ok'></form><br>
<table border='1' width='106%'> <tr><td>".$lang['adm_name']."/".$lang['adm_image']."/".$lang['reg_race']."</td> <td>Atk/Dif/Vel</td> <td>Cost</td><td>".$lang['adm_requirements']."</tr>";

$rac=0;
$qr= mysql_query("SELECT * FROM t_unt");
if( isset($_GET['srac']) && $_GET['srac'] >0 ){ $qr= mysql_query("SELECT * FROM t_unt WHERE race =".(int)$_GET['srac']); $rac=(int)$_GET['srac']; }

while( $row= mysql_fetch_array($qr) ){
	$racsel="<select name='race'><option value='0'>All</option>";
	$qrac= mysql_query("SELECT * FROM races");
	while( $racr= mysql_fetch_array($qrac) ) { 
		$racsel.="<option value='".$racr['id']."'";
		if( $racr['id']==$row['race'] ) $racsel.="selected";
		$racsel.=">".$racr['rname']."</option>"; 
	}
	$racsel.="</select>";
	
	$rescost="";
	$qrres= mysql_query("SELECT * FROM resdata");
	while( $ftr= mysql_fetch_array($qrres) ){
		$qrcost= mysql_query("SELECT * FROM `t_unt_resourcecost` WHERE `unit` =".$row['id']." AND resource =".$ftr['id']);
		if( mysql_num_rows($qrcost) ==0 ) $cost=0;
		else {
			$ar= mysql_fetch_array($qrcost);
			$cost= $ar['cost'];	
		}
		$rescost.=$ftr['name'].": <br><input type='text' size='3' name='res".$ftr['id']."' value='$cost'><br>";
	}
	
	$body.="<form method='post' action='?pg=units&srac=$rac'><input type='hidden' name='editunt' value='".$row['id']."'>
	<tr><td style='text-align: left'>".$lang['adm_name'].": <br><input type='text' size='17' name='name' value='".$row['name']."'> <br>".$lang['adm_image']."<input type='text' name='img' value='".$row['img']."' size='17'> <br>".$lang['reg_race'].": <br>$racsel <br>Desc: <br><textarea name='desc'>".$row['desc']."</textarea></td> 
	<td>Health: <input type='text' name='health' value='".$row['health']."' size='3'><br> Atk: <br><input type='text' size='3' name='atk' value='".$row['atk']."'><br>
	Dif: <br><input type='text' size='3' name='dif' value='".$row['dif']."'><br> Vel: <br><input type='text' size='3' name='vel' value='".$row['vel']."'></td> <td>$rescost Time: <input type='text' size='2' name='time' value='".$row['etime']."'></td>
	 <td>";
	//unit build req
	$qrreqbud= mysql_query("SELECT * FROM `t_unt_reqbuild` WHERE `unit` =".$row['id']);
	$i=1;
	while( $arb= mysql_fetch_array($qrreqbud) ) {
		$body.="<select name='reqbud$i'>";
		
		$qrgetbuilds= mysql_query("SELECT * FROM t_builds");
		while( $gb= mysql_fetch_array($qrgetbuilds) ){
			$body.="<option value='".$gb['id']."' ";
			if( $gb['id']==$arb['reqbuild'] ) $body.="selected";
			$body.=">".$gb['name']."</option>";
		}
			
		$body.="</select>: <input type='text' name='levbud$i' value='".$arb['lev']."' size='1'></nobr> <br>";
		$i++;
	}
	
	$body.="<nobr> <div id='radd".$row['id']."' style='display:none;'><select name='reqbud$i'>";
	
	$qrgetbuilds= mysql_query("SELECT * FROM t_builds");
	while( $gb= mysql_fetch_array($qrgetbuilds) ) $body.="<option value='".$gb['id']."'>".$gb['name']."</option>";
	
	$body.="</select>: <input type='text' name='levbud$i' value='0' size='1'></div></nobr> <br><a href='#' onclick=\"javascript: showHide(radd".$row['id'].");\">".$lang['adm_add']."</a></td><td>";
	
	//unit research req
	$qrreqresearch= mysql_query("SELECT * FROM t_unt_req_research WHERE unit =".$row['id']);
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
	</td><td><input type='image' src='../img/icons/b_edit.png' onclick='document.thisform.submit()' /><a href='?pg=units&delunt=".$row['id']."'><img src='../img/icons/x.png' /></a></td></tr></form>";	
}
$body.="</table>";
?>