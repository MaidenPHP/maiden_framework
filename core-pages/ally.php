<?php

/* --------------------------------------------------------------------------------------
                                      ALLIANCES
   Credits         : phpSGEx by Aldrigo Raffaele
   Last modified by: Fhizban 07.07.2013
   Comments        : Fix ally creation and edit
-------------------------------------------------------------------------------------- */

if( $me->user_info['ally_id']==0 and !isset($_GET['showally']) ){
	// if no ally
	$body.="<p>".$lang['aly_no_alliance']."</p>
	<h2 class='news-title'><span class='news-date'></span>".$lang['aly_ally_create']."</h2>
<div class='news-body'> <form method='post' action=''> <input type='hidden' name='pg' value='ally'><label>".$lang['aly_ally_name'].": <input type='text' name='name' id='aname' size='20'></label><label> <input type='submit' name='createally' value='".$lang['aly_create']."'></label></form></div>"; 
	$body.= "<h2 class='news-title'>".$lang['aly_search']."</h2><div class='news-body'><br><p>".$lang['aly_join']."<form method='get' action=''> <input type='hidden' name='pg' value='ally'> ".$lang['aly_ally_name'].": <input type='text' name='searchally' id='aname' size='20'> <input type='submit' value='".$lang['aly_search']."'></form></div>"; 	
} else { //you are in an alliance, show alliance info
	if( isset($_GET['showally']) ) $allyid= (int)$_GET['showally'];
	else $allyid= $me->user_info['ally_id'];
	
	$allyinf= mysql_fetch_array( mysql_query("SELECT * FROM ally WHERE id =".$allyid) );
	
	if( !isset($_GET['editally']) ){ //show ally
		if( $allyinf['owner'] == $me->user_id ) $body.="<a href='?pg=ally&editally=1'><input type='button' value='Edit'></a>";
		if( !isset($_GET['showally']) ) $body.= " <a href='?allyleave=1'><input type='button' value='Leave ally'></a>";
		$body.= "<table border='1' width='100%'><tr><td colspan='2'><h3>Alliance: ".$allyinf['name']." (".get_ally_points($allyid)." points)</h3></td></tr>
		<tr> <td width='50%'>".$allyinf['desc']."</td> <td><h4>Alliance members:</h4><br>";
			
		//show users in the ally
		$qrallyusr= mysql_query("SELECT * FROM users WHERE ally_id =".$allyid);
		while( $au= mysql_fetch_array($qrallyusr) ) $body.= $au['username']."<br>";
	} else { //edit ally
		$head.="<script type='text/javascript' src='templates/js/nicEdit.js'></script> <script type='text/javascript'>bkLib.onDomLoaded(nicEditors.allTextAreas);</script>";
		$body.="<form method='post' action='?pg=ally'> <input type='hidden' name='saveally' value='".$allyinf['id']."'>
			Name: <input type='text' name='name' value='".$allyinf['name']."'> <br><br>
			Ally description: <br>
			<textarea name='allydesc' cols='45' rows='5'>".$allyinf['desc']."</textarea><br>
			<input type='submit' value='save'>
		</form>";	
	}
		
	$body.= "</td> </tr>";
	$body.="</table>";
}

if( isset($_GET['searchally']) ){
	$alnm= mysql_real_escape_string($_GET['searchally']);
	$qr= mysql_query("SELECT * FROM `ally` WHERE `name` LIKE '%".$alnm."%'");	
	if( mysql_num_rows($qr) ==0 ) $body.="<h3>No ally found!</h3>";
	else{
		$body.="<br><table border='1'>";
		while( $row= mysql_fetch_array($qr) ){
			$body.="<tr><td><a href='?pg=ally&showally=".$row['id']."'>".$row['name']."</a></td></tr>";	
		}
		$body.="</table>";
	}
}
?>