<?php
if( isset($secure) ){
	if( isset($_GET['act']) && $_GET['act'] == "recoverpass" ){ 
		$body="<form action='index.php' method='post'>
			<h2>Recover Password</h2>
			<div>
				Email: <input type='email' name='email' required>
				".Template::button( "Recover", "type='submit' name='recoverpass'" )."
			</div>
		</form>";
	} 
	else if( isset($_GET['resetpw']) && isset($_GET['id']) ){
		$id= (int)$_GET['id'];
		$hash= mysql_real_escape_string($_GET['resetpw']);
		$qr= mysql_query("SELECT `until` FROM `user_passrecover` WHERE `usrid` = 1 AND `hash` = 'asd';");
		
		if( mysql_num_rows($qr) < 1 ){
			$body="<b>Invalid!</b>";
		} else {
			$body="<form method='post' action='index.php'>
				<input type='hidden' name='id' value='$id'>
				<input type='hidden' name='hash' value='$hash'>
				New password: <input type='password' name='newpass' required> 
				".Template::button( "Reset", "type='submit' name='resetpass'" )."
			</form>";
		}
	} else {
		$body="<form action='index.php' method='post' name='frmreg'>
			   <input type='hidden' name='act' value='register'>
			   <table border='0' class='tbleft'>
					<tr><td>".$lang['reg_language'].":</td><td><select name='lang'>";

					$dir = './lang';
					  $handle = opendir($dir);
					  // Lettura...
					while( $files = readdir($handle) ) {
						// Escludo gli elementi '.' e '..' e stampo il nome del file...
						if( $files != '.' && $files != '..' && $files != 'index.php' ){  
							$body.= '<option ';
							if( LANG==substr($files,0,-4) ) $body.= 'selected';
							$body.= '>'.substr($files,0,-4).'</option>';
						}
					}

					$body.="</select>
					</td></tr>
					<tr><td>".$lang['reg_nickname']."*:</td> <td><input type='text' name='rnik' id='rnik' required></td> </tr>
					<tr><td>".$lang['reg_password']."*:</td> <td><input type='password' name='rpass' id='rpass' required></td> </tr>
					<tr><td>E-mail*: </td><td><input type='email' name='email' id='email' required></td></tr>
					<tr><td>".$lang['reg_city'].":</td><td><input type='text' name='rcct' id='rcct'></td>
				</table>
				<h2 class='news-title'><span class='news-date'></span>".$lang['reg_race']."</h2>
				<div class='race-list'>
				<br>
				<table width='200' border='1' cellspacing='0' cellpadding='9'>";
				
				//mostra razze
				$sris="SELECT * FROM ".TB_PREFIX."races";
				$qris=mysql_query($sris);

				$i=1;
				while ( $riga=mysql_fetch_array($qris) ) {
					if ($i==1){
						$body.= "<tr><td><input name='rac' type='radio' id='r1' value='1' checked></td><td><img src='".IRACE.$riga['img']."'><b>".$riga['rname']."</b></td><td>".$riga['rdesc']."</td></tr>";
					}
					else {
						$body.= "<tr><td><input name='rac' type='radio' id='r1' value='".$i."'></td><td><img src='".IRACE.$riga['img']."'><b>".$riga['rname']."</b></td><td>".$riga['rdesc']."</td></tr>";
					}
					$i = $i + 1;
				}
				
				$body.="
				</table>
				<p>
				  ".Template::button( $lang['reg_register'], "type='submit' name='reg' id='reg'" )."
				</p>
				
				</form></div>"; 
    }
}
?>