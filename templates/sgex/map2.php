<?php 
if( isset($secure) ){
	if( !isset($_GET['x']) ){
		$x= $me->city_info['x'];
		$y= $me->city_info['y'];
	}
	else{
		$x=(int)$_GET['x'];
		$y=(int)$_GET['y'];
	}
	//if there isn't a request for viewing a city
	if( isset($_GET['wci']) ){
		$scinfo= mysql_fetch_array( mysql_query("SELECT * FROM city WHERE id =".(int)$_GET['wci']) );
		$ownname= mysql_fetch_array( mysql_query("SELECT username FROM users WHERE id =".$scinfo['owner']) );
		$body.="<table width='50%' border='1' cellpadding='5'>
			<tr><td>City</td><td>".$scinfo['name']."</td></tr>
			<tr><td>Owner</td><td><a href='?pg=profile&usr=".$scinfo['owner']."'>".$ownname['username']."</a></td></tr>";
			
		if( $scinfo['owner'] != $me->user_id ) $body.="<tr><td>Actions</td><td><a href='?pg=battle&p=".(int)$_GET['wci']."'>Atack</a></td></tr>";
			
		$body.="</table>";	
	}
	else if( isset($_GET['field']) ){
		if( mysql_num_rows( mysql_query("SELECT * FROM map WHERE x = $x AND y = $y") ) ==0 ){
			$body="<table border='1' cellpadding='5' cellspacing='5'>
			<tr><td colspan='2'>Empity field</td></tr>
			<tr><td>X:</td><td>$x</td></tr>
			<tr><td>Y:</td><td>$y</td></tr>
			<tr><td colspan='2'> <a href='?pg=battle&colnize=-1&x=$x&y=$y'><img src='img/icons/colonize.png' title='colonize'></a> </td></tr>
			</table>";
		} else header("Location: index.php?pg=map2");
	}
	else {
		$body="<script type='text/javascript'>
		function vlinf(vl,pl,al){
			var p_vl = document.getElementById('vl');
			var p_pl = document.getElementById('pl'); 
			var p_al = document.getElementById('al');
			p_vl.innerHTML = vl;
			p_pl.innerHTML = pl;
			p_al.innerHTML = al;
		}
		</script>

		<p><form action='' method='get'><input type='hidden' name='pg' value='map2'>X: <input name='x' type='text' value='$x' size='3' /> y: <input name='y' type='text' value='$y' size='3' /> 
		<input name='' value='OK' type='submit' /></form></p>
	<p>
	<table border='2' cellpadding='9' cellspacing='9'>
	<tr><td>&nbsp;</td><td><div align='center'><a href='?pg=map2&x=$x&y=".($y-1)."'><img src='./templates/sgex/map2/map_n.png' /></a></div></td> <td>&nbsp;</td>
	</tr>
	<tr>
		<td valign='middle'><div align='center'><a href='?pg=map2&x=".($x-1)."&y=$y'><img src='./templates/sgex/map2/map_w.png' /></a></div></td>
		
		<td>";
		  $mplarge=10;
		  
		  for($k=0; $k< $mplarge; $k++){
				$body.= "<p style='margin: -3px'>";
				for($i=0; $i< $mplarge; $i++){
					$mqf=mysql_query('SELECT * FROM `'.TB_PREFIX.'map` WHERE `x` ='.($x+$i).' AND `y` ='.($y+$k).' LIMIT 1;');
					if(mysql_num_rows($mqf)!=0){
						$mpd=mysql_fetch_array($mqf); 
						$cinfo=mysql_fetch_array( mysql_query('SELECT * FROM `'.TB_PREFIX.'city` WHERE `id` ='.$mpd['city'].' LIMIT 1;') );
						$usrinfo=mysql_fetch_array( mysql_query('SELECT * FROM `'.TB_PREFIX.'users` WHERE `id` ='.$cinfo['owner'].' LIMIT 1;') );
						
						$body.="<a title='".($x+$i).",".($y+$k)." | ".$cinfo['name']."' href='?pg=map2&wci=".$mpd['city']."' onMouseOver='vlinf('".$cinfo['name']."','".$usrinfo['username']."','".$usrinfo['ally_id']."');'><img src='templates/sgex/map2/".$mpd['type'].".png' border='0' /></a>"; 
					} else {
						$body.="<a title='".($x+$i).",".($y+$k)."' href='?pg=map2&field=1&x=".($x+$i)."&y=".($y+$k)."'><img border='0' src='templates/sgex/map2/gras1.png' /></a>"; 
					}	
				}
			  
		  }
		  $body.="
		</td>
	
		<td><div align='center'><a href='?pg=map2&x=".($x+1)."&y=$y'><img src='./templates/sgex/map2/map_e.png' /></a></div></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><div align='center'><a href='?pg=map2&x=$x&y=".($y+1)."'><img src='./templates/sgex/map2/map_s.png' /></a></div></td>
		<td>&nbsp;</td>
	</tr>
	</table>
	</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>"; 
	}
}
?>