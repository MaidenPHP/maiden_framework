<?php

/* --------------------------------------------------------------------------------------
                                      HIGHSCORES
   Credits         : phpSGEx by Aldrigo Raffaele
   Last modified by: Fhizban 09.06.2013
   Comments        : No changes
-------------------------------------------------------------------------------------- */

$qris= mysql_query("SELECT * FROM ".TB_PREFIX."users ORDER BY `points` DESC");

$body="<h2 class='news-title'><span class='news-date'></span>".$lang['scr_highscore']."</h2><div class='news-body'>
<table width='300' border='1' cellspacing='10' cellpadding='1'><tr><td><span class='Stile4'>".$lang['scr_position']."</span></td><td><span class='Stile4'>".$lang['scr_username']."</span></td><td><span class='Stile4'>".$lang['prf_points']."</span></td></tr>";

//mostra utenti
$i=1;
while( $riga= mysql_fetch_array($qris) ) {
	$body.= "<tr><td>".$i."</td><td><a href='?pg=profile&usr=".$riga['id']."'>".$riga['username']."</a>";
	if( $riga['rank'] >0 ) $body.=" [<font color='#00FF00'>A</font>]";
	$body.="</td><td>".$riga['points']."</td></tr>";
	$i++;
}

$body.="</table></div>";
?>