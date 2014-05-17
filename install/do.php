<?php
session_start();
if( isset($_SESSION['mhost']) ){
	mysql_connect( $_SESSION['mhost'], $_SESSION['muser'], $_SESSION['mpass'] );
	mysql_select_db( $_SESSION['mdb'] );
	
	require("proc.php");
	makeconfig();
	//createdb
	$str = file_get_contents("./sql/phpsgex.sql");
	//$str = preg_replace("'%PREFIX%'",$_POST['prefix'],$str);
	mysql_exec_batch($str);
	
	switch($_POST['mnu_map']){		
		case 1:
			createTBmap1();
		break;
		
		case 2:
			createTBmap2();
		break;
			
		/*case 3:
			createTBmap3();
		break;*/
	}
	
	if( $_POST['cts']=="2" ){
		$str = file_get_contents("./sql/city_sys2.sql");
		//$str = preg_replace("'%PREFIX%'",$_POST['prefix'],$str);
		mysql_exec_batch($str);
	}
	
	if( $_POST['pope']=="1" ){
		mysql_query("ALTER TABLE `".TB_PREFIX."t_builds` ADD `pop_req` SMALLINT( 3 ) NOT NULL DEFAULT '0' COMMENT 'pop requested' AFTER `desc` ,ADD `pop_mpl` DOUBLE NOT NULL DEFAULT '0' COMMENT 'pop moltiplier per level' AFTER `pop_req` ;");
		mysql_query("ALTER TABLE `".TB_PREFIX."t_unt` ADD `pop_req` SMALLINT( 3 ) NOT NULL DEFAULT '1' COMMENT 'pop requested' AFTER `vel`;");	
		mysql_query("ALTER TABLE `".TB_PREFIX."city` ADD `pop` SMALLINT( 3 ) NOT NULL DEFAULT '100' AFTER `name` ;");
	}
		
	//save config to db
	mysql_query("UPDATE `conf` SET `server_name` = '".$_POST['gname']."', `server_desc_sub` = '".$_POST['gdesc']."', `server_desc_main` = '".$_POST['mandesc']."', `template` = '".$_POST['templ']."', `css` = '".$_POST['css']."' WHERE 1 LIMIT 1;");
		
	mysql_close();
    session_destroy();
    header("Refresh: 2; URL=../index.php");
    echo "Installation complete! Redirecting...";
	
} else echo "Warning! Something went wrong! Turn back and retry!";
?>