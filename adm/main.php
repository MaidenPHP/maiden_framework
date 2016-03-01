<?php
$head.="<script src='../templates/js/nicEdit2.js'></script>
<script>
	bkLib.onDomLoaded(function() {
        new nicEditor().panelInstance('msg');
	});
</script>
<script src='../templates/js/getlastversion.js'></script>
<script>get_last_version();</script>";
$bol="onLoad='showHide(MAIL);'";
$body="<table width='100%' cellpadding='1' cellspacing='1' border='1'>
<tr> <th>".$lang['adm_fast_commands']."</th> <th>Game Data</th> <th>".$lang['adm_info']."</th> <th>Rates</th> </tr>
 <tr><td>
		<div><form method='post' action=''><input type='hidden' name='act' value='cclean'>
             	<input type='submit' name='clean_chat' value='".$lang['adm_clear_chat']."'>
             </form>
             You have PhpSgeX v<span class='Stile1'>".$sgexver."</span><br>
			 Latest aviable: <span id='laver' class='Stile3'>...</span><br>
			 DataBase v<span class='Stile3'>".$dbver."</span><br>
             PHP v<span class='Stile1'>".phpversion()."</span><br>
        </div>
        </td>
        <td>
			<div><span class='Stile1'>".mysql_num_rows( mysql_query("SELECT `id` FROM `".TB_PREFIX."resdata`") )."</span> ".$lang['adm_resources']."<br>
				<span class='Stile1'>".mysql_num_rows( mysql_query("SELECT id FROM `".TB_PREFIX."races`") )."</span> ".$lang['adm_races']."<br>
				<span class='Stile1'>".mysql_num_rows( mysql_query("SELECT id FROM `".TB_PREFIX."t_unt`") )."</span> ".$lang['adm_units']."<br>
				<span class='Stile1'>".mysql_num_rows( mysql_query("SELECT id FROM `".TB_PREFIX."t_research`") )."</span> ".$lang['adm_research']."<br>
															  
			</div></td>
            <td>
				<div>".$lang['reg_language']." <span class='Stile1'>".LANG."</span> <br>
					Map System <span class='Stile1'>".MAP_SYS."</span> <br>
					City system <span class='Stile1'>".CITY_SYS."</span> <br>
					Magazine Engine <span class='Stile1'>".MAG_E."</span> <br>
				</div>
            </td>
			<td>Baracks time divider per level: <span class='Stile1'>".$config['baru_tmdl']."</td>
            </tr></table>
            <br>                 
			<div class='art-postmetadataheader'>
				<h2 class='art-postheader'><a href='#' class='active' onclick='showHide(SQL);'>".$lang['adm_adv_sql_commands']." [+/-]</a></h2>
			</div><br>
			<div id='SQL' style='display: none;'>
				<form method='post' action=''><input type='hidden' name='act' value='sqlqr'>
					".$lang['adm_insert_sql_query'].": <br><textarea name='sqlqr' cols='45' rows='5'></textarea><br><br>
					<input type='submit' value=' Execute Query '>
				</form>
			</div>
			<br>                 
			<div class='art-postmetadataheader'>
				<h2 class='art-postheader'><a href='#' class='active' onclick='showHide(MAIL);'>Send email to all players [+/-]</a></h2>
			</div><br>
			<div id='MAIL'>
				<form method='post' action=''>
					Tittle: <input type='text' name='tittle' required>
					Message: <br>
					<textarea id='msg' name='msg' cols='50' required></textarea> <br>
					<input type='submit' value='Send' name='sendmailtoall'>
				</form>
			</div>";

$qrwarns=mysql_query("SELECT * FROM `".TB_PREFIX."warn`");	
if( mysql_num_rows($qrwarns) >0 ){											  
	$body.="<table border='1'>";
	while( $wm=mysql_fetch_array($qrwarns) ) {
		$body.="<tr><td>".$wm['text']."</td></tr>";	
	}
} else $body.="<p>No Warns</p>";
?>