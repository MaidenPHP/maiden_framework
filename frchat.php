<?php

/* --------------------------------------------------------------------------------------
                                     CHAT SYSTEM
   Credits         : phpSGEx by Aldrigo Raffaele
   Last modified by: Fhizban 07.07.2013
   Comments        : No changes
-------------------------------------------------------------------------------------- */

require("func.php");
db_connect();
session_start();

if( isset($_SESSION['id']) && isset($_REQUEST['act']) ){
	if($_REQUEST['act']=="chat_rel"){
		$query=mysql_query("SELECT * FROM `".TB_PREFIX."chat` ORDER BY `id` DESC");	
		while( $lsm= mysql_fetch_array($query) ){
			$usrinf= get_user($lsm['usrid']);
			echo "<a href='?pg=profile&usr=".$lsm['usrid']."' target='_blank'>".$usrinf['username']."</a> : ".$lsm['msg']."<br>";
		}
	}	
	
	if($_REQUEST['act']=="chat_sendm"){
		$msg=html_entity_decode( htmlentities($_REQUEST['msg']) );	
		mysql_query("INSERT INTO `".TB_PREFIX."chat` (`id` ,`usrid` ,`msg` ,`sent_on`) VALUES (NULL, '".(int)$_SESSION['id']."', '$msg', CURRENT_TIMESTAMP);");
	}
}

?>