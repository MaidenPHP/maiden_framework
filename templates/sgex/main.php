<?php
$quc= mysql_query("SELECT * FROM `units` WHERE `owner_id` =".$me->user_id." AND `where` =".$me->city_id." AND `action` =0");
$body.="<h2>Units in town</h2>
<table border='1' cellpadding='5'>";
if( mysql_num_rows($quc)==0 ) $body.="<tr><td>NO UNITS IN TOWN!</td></tr>";
else{ 
	$body.="<tr><th>Unit</th> <th>Ammount</th></tr>";
	while( $row= mysql_fetch_array($quc) ){
		$untinf= mysql_fetch_array( mysql_query("SELECT name FROM t_unt WHERE id=".$row['id_unt']) );
		$body.="<tr><td>".$untinf['name']."</td> <td>".$row['uqnt']."</td></tr>";
	}
}
$body.="</table>

<h2>Units movements</h2><table border='1' cellpadding='5'>";
$qrvm= mysql_query("SELECT * FROM `units` WHERE `owner_id` =".$me->user_id." AND `from` =".$me->city_id." AND `action` !=0");
if( mysql_num_rows($qrvm) ==0 ) $body.="<tr><td>NO MOVEMENTS!</td></tr>";
else {
	$body.="<tr><td>Unit</td> <td>Ammount</td> <td>Destination</td> <td>Time left</td></tr>";
	while( $row= mysql_fetch_array($qrvm) ){
		$body.="<tr><td>".$row['id_unt']."</td> <td>".$row['uqnt']."</td> <td>".$row['to']."</td> <td>".( $row['time'] -mtimetn() )."</td></tr>";
	}
}
$body.="</table>";
?>