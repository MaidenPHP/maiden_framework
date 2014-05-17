<?php 

/* --------------------------------------------------------------------------------------
                                    MAP
   Credits         : phpSGEx by Aldrigo Raffaele
   Last modified by: Fhizban 09.06.2013
   Comments        : No changes
-------------------------------------------------------------------------------------- */

//recupero planet
if( isset($_GET['gal']) ){
	$galaxy=(int)$_GET['gal'];
	$system=(int)$_GET['sys'];
} else {
	$galaxy= 0;
	$system= 0;
}
	
$gal=$galaxy;
$sys=$system;
	
// tempalte \\
$body="<form action='' method='get'><input type='hidden' name='pg' value='map' /><h2 class='news-title'>World Map</h2><div class='news-body'>  <table width='85%' border='0' cellspacing='0' cellpadding='10'> <tr> <td width='42'>Galaxy</td> <td width='40'>Sistem</td> <td width='24'>&nbsp;</td> <td width='111'>cerca utente</td> </tr> <tr> <td> <a href='?pg=map&gal=".($gal-1)."&sys=".$sys."'><input type='button' value='&larr;' /></a><input type='text' style='text-align:center;' name='gal' id='gal' value='".$galaxy."' size='3'><a href='?pg=map&gal=".($gal+1)."&sys=".$sys."'><input type='button' value='&rarr;' /></a> </td> <td> <a href='?pg=map&gal=".$gal."&sys=".($sys-1)."'><input type='button' value='&larr;' /></a><input type='text' style='text-align:center;' name='sys' id='sys' value='".$system."' size='3'><a href='?pg=map&gal=".$gal."&sys=".($sys+1)."'><input type='button' value='&rarr;' /></a> </td> </tr> <tr><td colspan='2'><div align='center'><input type='submit' value='Visualize'></div></td></form> <td>&nbsp;</td> <form method='get' action=''> <input type='hidden' name='pg' value='profile'> <td><input type='text' name='snusr'></td></form> </tr> </table> <p>&nbsp;</p> <table width='600' border='1' cellspacing='0' cellpadding='5'> <tr> <td width='61'><div align='center'>Postion</div></td> <td width='62'>Planet</td> <td width='98'>Name</td> <td width='99'>Player</td> <td width='88'>Ally</td> <td width='93'>Actions</td> </tr>";
//mostra pianeti
for( $i=1; $i< $config['Map_max_z']; $i++ ) { 
	$sris="SELECT * FROM ".TB_PREFIX."city WHERE galaxy='".$galaxy."' AND system='".$system."' AND `pos` = ".$i;
	$qris=mysql_query($sris);
	
	if( mysql_num_rows($qris)==1 ){
		$riga=mysql_fetch_array($qris);
		$mcuid=$riga['owner'];
		$auin= mysql_fetch_array( mysql_query("SELECT username,ally_id,rank FROM ".TB_PREFIX."users WHERE id='$mcuid'") );
		$cun=$auin['username'];
		$aacu=$auin['ally_id'];
		
		$qan= mysql_fetch_array( mysql_query("SELECT name FROM ".TB_PREFIX."ally WHERE id='$aacu'") );
		if( $qan ) $uan="<a href='?pg=ally&showid=".$aacu."'>".$qan['name']."</a>";
		else $uan="None";
		
		$ra="";
		if($auin['rank']>0){$ra="[<font color='#00FF00'>A</font>]";}
		if($auin['rank']!=3 and $mcuid!=$me->user_id and ($auin['ally_id']!=$me->user_info['ally_id'] or $auin['ally_id']=="0") ) $atkb="<a href='?pg=battle&p=".$riga['id']."'><img src='img/icons/attack.jpg' border='0'></a>";
		else $atkb="&nbsp;";
			
		$body.= "<tr><td><div align='center'>".$i."</div></td><td>&nbsp;</td><td>".$riga['name']."</td><td>".$ra." <a href='?pg=profile&usr=".$mcuid."'>".$cun." <img src='img/icons/m.jpg' border='0'></a></td><td>".$uan."</td><td>".$atkb."</td></tr>";
	} else {$body.= "<tr><td><div align='center'>".$i."</div></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td><a href='?pg=battle&colnize=$i&gal=$gal&sys=$sys'><img src='img/icons/colonize.png' title='colonize'></a></td></tr>";}
}
	
$body.="  </table></div>";
?>