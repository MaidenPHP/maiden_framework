<?php
if( isset($_GET['lang']) ) $lng= $_GET['lang'];
else $lng= "en";
include("../lang/$lng.php");

if ( file_exists("../config.php") ) header ("Location: ../index.php");
else {
	$secure= true;
	$body="";
	$step="";
	session_start();
	
	if( isset($_GET['lang']) ) $_SESSION['lang']= $_GET['lang'];
	else if( !isset($_REQUEST['step']) ) $_SESSION['lang']="en";
	
	if( isset($_REQUEST['step']) ){
		$step= $_REQUEST['step'];
	
		switch($step){
			case "mysqldone":
				if( isset($_SESSION['mhost']) ) break;
				$con= mysql_connect( $_POST['mhost'], $_POST['muser'], $_POST['mpass'] );
				if( !$con or $_POST['mhost']=="" or $_POST['muser']=="" ){
					$body.="<h2>Mysql Connection Error!</h2>";
					$step="";	
				}
				
				if( isset($_POST['ins_createdb']) && $_POST['ins_createdb']=="on" ){ 
					$con= mysql_query("CREATE DATABASE ".$_POST['mdb']." DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
				} else  $con= mysql_select_db($_POST['mdb']);
				
				if( !$con ){
					$body.="<h2>Database Connection Error!</h2>";
					$step="";	
				}
				
				$_SESSION['mhost']= $_POST['mhost'];
				$_SESSION['muser']= $_POST['muser'];
				$_SESSION['mpass']= $_POST['mpass'];
				$_SESSION['mdb']= $_POST['mdb'];
			break;	
		}
	}
	
	if( $step=="" ){
		if (!defined('PHP_VERSION_ID')) {
			$version = explode('.', PHP_VERSION);
			define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
		}

		$body.= $lang['ins_installintro']."	
		<br><div class='box' style='width: 100% !important;'>";
		
		include("../core-pages/credits.php");
		
		$body.="</div><br>
		<h3><br>PHP version: ".phpversion();
		if( PHP_VERSION_ID >= 50200 ) $body.="&nbsp; OK";
		else $body.="&nbsp; <span class='Stile3'>Not Supported! Update PHP (to: 5.2.0)!</span>";

		 $dir = '../lang';
		 $handle = opendir($dir);
		 $optlangs="";
		 while( $files= readdir($handle) ) {
			if( $files != '.' && $files != '..' && $files != 'index.php' ) {
				$optlangs.= '<option ';
				if( $lng == substr($files,0,-4) ) $optlangs.='selected';
				$optlangs.= '>'.substr($files,0,-4).'</option>';
			}
		 }

		$body.="</h3><br>
		<p>Language / ßçûê 
		<form method='get' action=''>
			<select name='lang' id='lang' onChange='this.form.submit();'>
				$optlangs      
			</select>
		</form></p> <form method='post' action='?step=mysqldone'> <p>&nbsp;</p>";

		$pg= "Mysql Configuration";
		$body.= "<table border='1'> <tr><td>Mysql Host:</td><td><input type='text' name='mhost' required></td></tr>
			<tr><td>Mysql User:</td><td><input type='text' name='muser' required></td></tr>
			<tr><td>Mysql Pass:</td><td><input type='text' name='mpass'></td></tr>
			<tr><td>Mysql Database:</td><td><input type='checkbox' name='ins_createdb'> ".$lang['ins_createdb']."
				<br><input type='text' name='mdb' required>
			</td></tr>
			<tr><td colspan='2'><input type='submit' value='".$lang['ins_next']."'></td></tr>
			</table></form>";	
	} else if( $step=="mysqldone" ){
		$pg= "Game Configuration";
		$body.="<form method='post' action='do.php'><table border='0'>
		<tr> <td>Game Name</td> <td><input type='text' name='gname' required></td> </tr>
		<tr> <td>Game Short Desc</td> <td><input type='text' name='gdesc'></td> </tr>
		<tr> <td>Game Main Desc</td> <td><input type='text' name='mandesc'></td> </tr>
		
		<tr> <td>Template</td> <td><select name='templ'>";
		
		$dir= '../templates';
        $handle= opendir($dir);
        // Lettura...
        while( $files= readdir($handle) ) {
        	// Escludo gli elementi '.' e '..' e stampo il nome del file...
            if ($files != '.' && $files != '..' && $files != 'js' ){  
				$body.= '<option>'.$files.'</option>';
            }
        }
		
		$body.="</select></td> </tr>
		<tr> <td>Map Sys</td> <td><select name='mnu_map'> <option value='1'>Map Sys 1 / Ogame</option>
			<option value='2'>Map Sys 2 / Travian</option> </select></td> </tr>
		<tr> <td>City sys</td> <td><select name='cts'> <option value='1'>City Sys 1 / Ogame</option> </select></td> </tr>
		<tr> <td>Magazine Engine</td> <td> <select name='mge'> <option value='0' selected >OFF</option> 
			<option value='1'>ON</option> </select></td></tr>
		
		<tr> <td colspan='2'><input type='submit' name='inst' value='Install!'> (By pressing next you will acept PhpSgeX license)</td> </tr>
		</table></form>";	
	}
	
	include("../templates/sgex/install.php");
}
?>