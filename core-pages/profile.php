<?php 
/* --------------------------------------------------------------------------------------
                                      PROFILE
   Credits         : phpSGEx by Aldrigo Raffaele
   Last modified by: raffa50 19.06.2013
   Comments        : No changes
-------------------------------------------------------------------------------------- */

// template \\
$body="<form method='get' action=''> <input type='hidden' name='pg' value='profile'>
Search user: <input type='text' name='snusr'> <input type='submit' value='search'> </form>";

if( isset($_GET['snusr']) ){ //search user
	$suq=mysql_query("SELECT * FROM `".TB_PREFIX."users` WHERE `username` LIKE '%".mysql_real_escape_string($_GET['snusr'])."%'");
	
	$body.="<h2 class='news-title'><span class='news-date'></span>User founded</h2><div class='news-body'>";
	$body.="<table>";
	while( $riga=mysql_fetch_array($suq) ){
			$body.="<tr><td><a href='?pg=profile&usr=".$riga['id']."'>".$riga['username']."</a></td>";
			if( isset($_GET['inv']) ) $body.="<td><a href='?plsinv=".$riga['id']."'><input type='button' value='invite'></a></td>";
			$body.="</tr>";
	}
	$body.="</table></div>";
}

if( isset($_GET['usr']) ){ //show user info
	$wusr=(int)$_GET['usr'];
	$u2p = get_user( $wusr );
	
	// ally name
	if($u2p['ally_id']==0){$allyn="".$lang['prf_no_ally']."";}
	else{$aan=mysql_fetch_array( mysql_query("SELECT * FROM `".TB_PREFIX."ally` WHERE `id` =".$u2p['ally_id']) );
		$allyn=$aan['name'];
	}

	// city name
	if(!empty($u2p['username'])){
		 // ally name
		 $ncp2=mysql_fetch_array( mysql_query("SELECT * FROM `".TB_PREFIX."city` WHERE `id` =".$u2p['capcity']." LIMIT 1;") );
		 $qsur=mysql_fetch_array( mysql_query("SELECT `rname` , `img` FROM `".TB_PREFIX."races` WHERE `id` =".$u2p['race']." LIMIT 1;") );
	}
	
	$online=" Offline";
	if( isonline($wusr) ) $online = " Online";
	$body.="<h2 class='news-title'><span class='news-date'></span>".$lang['reg_user']." ".$u2p['username'].$online."</h2><div class='news-body'> 
	<table border='1' cellspacing='3' cellpadding='3'> <tr><th>".$lang['reg_race'].":</th> <td>".$qsur['rname']."</td></tr>
	<tr><th>".$lang['prf_points'].":</th> <td>".$u2p['points']."</td></tr>
	<tr><th>".$lang['prf_last_login'].":</th> <td>".$u2p['last_log']."</td></tr>
	<tr><th>".$lang['prf_alliance'].":</th> <td><a href='?pg=ally&showid=".$u2p['ally_id']."'>".$allyn."</a>";
	
	if( $u2p['ally_id']==0 and $me->user_info['ally_id'] !=0 ) $body.= "<div> <form action='' method='post'>
	<input type='hidden' name='usr' value='".$u2p['id']."'><input type='submit' name='allyinvite' value='Invite'></form> </div>";
	
	$body.="</td> </tr>"; //you can't send a message to yourself
	if($wusr!=$_SESSION['id']) $body.="<tr> <td colspan='2'><form method='get' action=''> <div align='center'> <input type='hidden' name='pg' value='message'><input type='hidden' name='act' value='smsg'> <input type='hidden' name='ito' value='".$wusr."'> <input type='submit' value='".$lang['prf_send_pm']."'> </div> </form>";   
	$body.="</td></tr>
	<tr><th>Email: </th> <td>".$u2p['email']."</td> </tr>
	</table></div>";
	
	//show user city(s)
	$body.="<h2 class='news-title'><span class='news-date'></span>".$lang['prf_cityof']." ".$u2p['username']."</h2><div class='news-body'><table cellpadding='3' border='1'>";
	$qrsc=mysql_query("SELECT * FROM `".TB_PREFIX."city` WHERE `owner` =".$wusr);
	while( $riga=mysql_fetch_array($qrsc) ){
		if( MAP_SYS ==1 ) $pos = "<a href='?pg=map&gal=".$riga['galaxy']."&sys=".$riga['system']."'>".$riga['galaxy']." ".$riga['system']." ".$riga['pos']."</a>";
		else if( MAP_SYS ==2 ){
			$ct_pos= mysql_fetch_array( mysql_query("SELECT * FROM `".TB_PREFIX."map` WHERE `city` =".$riga['id']." LIMIT 1;") );
			$pos = "<a href='?pg=map2&x=".$ct_pos['x']."&y=".$ct_pos['y']."'>".$ct_pos['x']." ".$ct_pos['y']."</a>";
		}
		
		$body.="<tr><td>".$riga['name']."</td><td>".$pos."</td></tr>";	
	}
	$body.="</table></div>";
	
}
?>
