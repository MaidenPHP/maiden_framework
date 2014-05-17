<?php
if( isset($_GET['editusr']) ){ //edit user
	$uid= (int)$_GET['editusr'];
	$usrinf= mysql_fetch_array( mysql_query("SELECT * FROM `users` WHERE `id` =".$uid) );
	
	$body="<h3>User Info: ".$usrinf['username']."</h3>
	<form method='post' action='?pg=users'><input type='hidden' name='editusr' value='".$usrinf['id']."'>
	Email: <input type='text' name='email' value='".$usrinf['email']."'><br>
	Rank: <input type='number' name='rank' min='0' max='3' value='".$usrinf['rank']."'><br>
	Points: <input type='number' name='points' min='0' value='".$usrinf['points']."'><br>
	<input type='submit' value='Save'>
	</form>";
	
	$body.="<h3>City of ".$usrinf['username']."</h3><table border='0' width='21%'>";
	
	$qrct= mysql_query("SELECT * FROM `city` WHERE `owner` =".$uid);
	while( $row= mysql_fetch_array($qrct) ){
		$body.=	"<tr> <td>".$row['name']."<td> <td><a href='?pg=users&editcity=".$row['id']."'><img src='../img/icons/b_edit.png'></a></td> </tr>";
	}
	$body.="</table>";
} else if( isset($_GET['editcity']) ){ //edit city
	$cid= (int)$_GET['editcity'];
	$cinf= mysql_fetch_array( mysql_query("SELECT * FROM city WHERE id=".$cid) );
	$body="<h3>Edit city</h3><form method='post' action='?pg=users&editcity=$cid'> <input type='hidden' name='editct' value='$cid'>";
	$body.="City name: <input type='text' name='name' value='".$cinf['name']."'><br>";
	
	$body.="<table border='1'><tr>";
	$resnam= get_resources_name();
	for( $i=1; $i<=count($resnam); $i++) $body.="<td>".$resnam[$i]."</td>";
	$body.="</tr><tr>";
	//show city res
	$resd= mysql_query("SELECT * FROM resdata");
	while( $ftr= mysql_fetch_array($resd) ){
		$crs= mysql_fetch_array( mysql_query("SELECT * FROM `city_resources` WHERE `city_id` =$cid AND `res_id` =".$ftr['id']) );
		$body.="<td> <input type='text' name='res".$ftr['id']."' value='".$crs['res_quantity']."'> </td>";
	}
	$body.="</tr></table> <br><input type='submit' value='Save'></form>";
} else { //show all users
	$body="
	<form method='post' action='?pg=users'>Search user: <input type='text' name='searchusr' size='9'> <input type='submit' value='Search'></form>
	 <a href='?pg=users&showbanned=1'><input type='button' value='Show banned users'></a>
	<br><br><table border='1' width='50%'>";
	
	if( isset($_GET['showbanned']) ) $qr= mysql_query("SELECT * FROM `users` WHERE `banned`= 1");
	else if( !isset($_POST['searchusr']) ) $qr= mysql_query("SELECT * FROM `users`");
	else $qr= mysql_query("SELECT * FROM `users` WHERE `username` LIKE '".$_POST['searchusr']."'");
	
	if( mysql_num_rows($qr) ==0 ) $body.="<tr><td>BANLIST IS EMPITY!</td></tr>";
	else while( $row= mysql_fetch_array($qr) ){
		$body.="<tr> <td>".$row['username'];
		if( $row['rank'] >0 ) $body.=" [<font color='#00FF00'>A</font>]";
		$body.="</td> <td>".$row['points']."</td> <td><a href='?pg=users&editusr=".$row['id']."'><img src='../img/icons/b_edit.png' alt='Edit user'></a></td>
			<td>";
			
			if( $row['rank'] <1 ){
				if( $row['banned'] ==0 ) $body.="<form method='post' action='?pg=users&ban=".$row['id']."'><input type='number' name='bantime' min='-1' value='-1'><a href='#' onclick='this.form.sumbit()'><img src='../img/icons/ban.png' alt='Ban user'></a></form>";
				else $body.="<a href='?pg=users&pardon=".$row['id']."'><img src='../img/icons/pardon.png' alt='Unban user'></a>";;
			}
			$body.=" </td> </tr>";	
	}
	$body.="</table>";
}
?>