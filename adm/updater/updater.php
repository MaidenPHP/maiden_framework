<?php	

	function mysql_exec_batch ($p_query, $p_transaction_safe = true) {
	  if ($p_transaction_safe) {
		  $p_query = 'START TRANSACTION;' . $p_query . '; COMMIT;';
		};
	  $query_split = preg_split ("/[;]+/", $p_query);
	  foreach ($query_split as $command_line) {
		$command_line = trim($command_line);
		if ($command_line != '') {
		  $query_result = mysql_query($command_line);
		  if ($query_result == 0) {
			break;
		  };
		};
	  };
	  return $query_result;
	} 
	
	function updateDB($newver) {
		if( (int)db_ver() < (int)$newver ){
			if( (int)$newver == 107 ){ //update config
				mysql_query("UPDATE `conf` SET `server_name` = '".SERVER_NAME."', `server_desc_sub` = '".SUB_DESC."', `server_desc_main` = '".MAIN_DESC."', `template` = '".TEMPLATE."', `css` = '".CSS."', `MG_max_cap` = ".MG_max_cap." WHERE 1 LIMIT 1;");	
			}
			
			$str = file_get_contents("./adm/updater/".db_ver().".sql");
			//$str = preg_replace("'%PREFIX%'",TB_PREFIX,$str);
			$result = mysql_exec_batch($str);
		}
	}
	
?>