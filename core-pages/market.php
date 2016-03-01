<?php

/* --------------------------------------------------------------------------------------
                                     MARKET
   Credits         : phpSGEx by Aldrigo Raffaele
   Last modified by: Raffa50 17.07.2013
   Comments        : Fix delete offer
-------------------------------------------------------------------------------------- */

$rescbr="";
$resd= mysql_query("SELECT * FROM `".TB_PREFIX."resdata`");
while( $fres= mysql_fetch_array($resd) ) {$rescbr.="<option value='".$fres['id']."'>".$fres['name']."</option>"; }

$body='';
//make offer
$body.="<form method='post' action='?pg=market'><table cellpadding='5' border='1'><tr><td>".$lang['mkt_offer_res']."</td><td>".$lang['mkt_need_res']."</td></tr> <tr><td><select name='resoff'>".$rescbr."</select> <input name='resoffqnt' type='number' min='1' value='1'></td><td><select name='resreq'>".$rescbr."</select> <input name='resreqqnt' type='number' min='1' value='1'></td><td><input type='submit' name='makeoffer' value='".$lang['mkt_make']."'></td></tr> </table></form> <br>";

$body.="<table width='512' border='1' cellspacing='0' cellpadding='5'>";
//show offers.
$arq= mysql_query("SELECT * FROM `".TB_PREFIX."market`");
if( mysql_num_rows($arq) >0 ){
	$body.="<tr><td><b>".$lang['mkt_offered']."</b></td><td><b>".$lang['mkt_wanted']."</b></td></tr>";
	while( $riga= mysql_fetch_array($arq) ) {
		$res1nam= mysql_fetch_array( mysql_query("SELECT `name` FROM `resdata` WHERE `id` =".$riga['resoff']." LIMIT 1;") );
		$res2nam= mysql_fetch_array( mysql_query("SELECT `name` FROM `resdata` WHERE `id` =".$riga['resreq']." LIMIT 1;") );
		
		$body.="<tr><td><div align='center'>".$res1nam['name']."<br>".$riga['resoqnt']."</div></td><td><div align='center'>".$res2nam['name']."<br>".$riga['resrqnt']."</div></td><td>";
		
		if( $riga['owner'] == $me->user_id ) $body.="<a href='?pg=market&deloff=".$riga['id']."'>Delete</a>";
		else $body.="<a href='?pg=market&aceptoff=".$riga['id']."'>".$lang['mkt_accept']."</a>";
		
		$body.="</td></tr>";
	} 
}
else $body.="<tr><td colspan='2'>".$lang['mkt_no_offers']."</td></tr>";
$body.="</table>";
?>