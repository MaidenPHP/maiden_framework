<?php

/* --------------------------------------------------------------------------------------
                                      SETTINGS
   Credits         : phpSGEx by Aldrigo Raffaele
   Last modified by: Fhizban 09.06.2013
   Comments        : No changes
-------------------------------------------------------------------------------------- */

$body= "<form method='post' action='?pg=settings'>".$lang['reg_language'].": <select name='lang'>";

$dir = './lang';
$handle = opendir($dir);
// Lettura...
while( $files= readdir($handle) ) {
// Escludo gli elementi '.' e '..' e stampo il nome del file...
	if( $files != '.' && $files != '..' && $files != 'index.php' ){  
		$body.= '<option ';
		if( $_SESSION['lang']==substr($files,0,-4) ) $body.= 'selected';
		$body.= '>'.substr($files,0,-4).'</option>';
	}
}
$body.="</select>
<br><input type='submit' name='editset' value='Save'> </form>";
?>