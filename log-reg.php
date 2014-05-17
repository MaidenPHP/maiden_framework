<?php
/* --------------------------------------------------------------------------------------
                                   LOGIN & REGISTRATION
   Credits         : phpSGEx by Aldrigo Raffaele
   Last modified by: raffa50 19.03.2014
   Comments        : fix registration
-------------------------------------------------------------------------------------- */

function isbanned($uid){
	$qrban= mysql_query("SELECT timeout FROM banlist WHERE usrid = $uid");
	if( mysql_num_rows($qrban) ==0 ) return false;
	$abq= mysql_fetch_array($qrban);
	if( $abq['timeout'] == -1 ) return true; //forever banned!
	if( $abq['timeout'] > mtimetn() ) return true;
	mysql_query("DELETE FROM banlist WHERE usrid = $uid");
	return false;
}

// --------------------------------------- LOGIN ------------------------------------- //
function login($username, $password) {		
		$password= strip_tags($password);
		$username= strip_tags($username);

		$q= "SELECT * FROM ".TB_PREFIX."users where username = '".mysql_real_escape_string($username)."' AND password = '".md5($password)."' LIMIT 1;";
		$result = mysql_query($q);
		$dbarray = mysql_fetch_array($result);
		if( isbanned($dbarray['id']) ) { header ("Location: index.php?msg=You are banned!"); }
		else {
			if( mysql_num_rows($result) >0 ) {
				$id=$dbarray['id'];
				$_SESSION['id']=$id;
				$_SESSION['ccity']=$dbarray['capcity'];
				$_SESSION['lang']=$dbarray['lang'];
				$_SESSION['rank']=$dbarray['rank'];
				//registra sul db la nuova date
				$mtimet=mtimetn();
				mysql_query("UPDATE `".TB_PREFIX."users` SET `last_log` =  NOW( ) ,`ip` = '".$_SERVER['REMOTE_ADDR']."' WHERE `id` =$id LIMIT 1 ;");
				
				//ip control
				$ipqr=mysql_query("SELECT * FROM `".TB_PREFIX."users` WHERE `ip` = '".$_SERVER['REMOTE_ADDR']."'");
				if( mysql_num_rows($ipqr) >0 ){
					$warntxt="Two users have the same ip!<br><table border='1'><tr><td>Username</td><td>Last log</td></tr>";
					while( $riga=mysql_fetch_array($ipqr) ){
						$warntxt.="<tr><td>".$riga['username']."</td><td>".$riga['last_log']."</td></tr>";		
					}
					$warntxt.="</table>";
					mysql_query("INSERT INTO `".TB_PREFIX."warn` (`id` ,`text`) VALUES (NULL , '$warntxt');");	
				}
			} else {
				header("Location: ?err=log");
				/*switch(CITY_SYS){
					case 3:
						return false;
					break;
					
					default:
						header("Location: ?err=log");
					break;
				}*/
			}
		}
}

// ------------------------------------- REGISTER ------------------------------------ //
function register() {
	//$fbid=$_POST['fbid'];
	$nik= mysql_real_escape_string( strip_tags($_POST['rnik']));
	$pass= md5($_POST['rpass']);
	$mail= mysql_real_escape_string( $_POST['email'] );
	$race= (int)$_POST['rac'];
	$ncity= mysql_real_escape_string( $_POST['rcct'] );
	$lang= $_POST['lang'];
	
	$cqr= mysql_query("SELECT * FROM conf LIMIT 1;");
	if( mysql_num_rows($cqr) !=0 ) $config= mysql_fetch_array($cqr);
	else {
		$config['Map_max_x']=500;
		$config['Map_max_y']=500;
		$config['Map_max_z']=12;
	}
		
	//if( isset($_POST['fbid']) ){ $pass="0"; $mail="0"; }
	if (/*!isset($_POST['fbid']) &&*/ strlen($nik) < 3 or strlen($pass) < 3 ) { echo "Registration  is not valid! go back and fill all the form!"; return false; }
	else {
		if($ncity=="") $ncity="City of ".$nik;
	
		//Generating user id
		$qns=mysql_query("SELECT id FROM ".TB_PREFIX."users ORDER BY `id` DESC LIMIT 1;");
		if( mysql_num_rows($qns) >0 ){
			$aliu= mysql_fetch_array($qns);
			$id= $aliu['id']+1;
			$rank= 0;
		} else { $id= 1; $rank= 3; }
	
		//and city id
		$sqd=mysql_query("SELECT id FROM ".TB_PREFIX."city ORDER BY `id` DESC");
		if( mysql_num_rows($sqd) >0 ){
			$alic= mysql_fetch_array($sqd);
			$cidr= $alic['id']+1;
		} else $cidr= 1;

		$mtimet=mtimetn();
		//check if user or email already exist!
		if( mysql_num_rows( mysql_query("SELECT * FROM ".TB_PREFIX."users WHERE username='$nik' OR email='$mail' LIMIT 1") ) >=1 ) { 
			echo "username or email already exist(s)!"; 
			return false; 
		} else {
			$reg="INSERT INTO `".TB_PREFIX."users` (`id`, `username`, `password`, `race`, `capcity`, `email`, `timestamp_reg`, `rank`, `tut`, `lang`) VALUES ($id, '$nik', '$pass', '$race', '$cidr', '$mail', '$mtimet', $rank,'-1', '$lang')";
			$q_reg=mysql_query($reg);
		
		//if( isset($_POST['fbid']) ){ mysql_query("UPDATE `".TB_PREFIX."users` SET `last_log` = NOW( ) ,`fbuid` = '$fbid' WHERE `id` =$id LIMIT 1 ;"); } 
	
		// map sys registration begin \\
		switch(MAP_SYS){
		case 1: //ogame sys; generate coords for map sys 1
			do{
				$galaxy=mt_rand(0, $config['Map_max_x']);
				$system=mt_rand(0, $config['Map_max_y']);
				$pos=mt_rand(1, $config['Map_max_z']);	
		
				$pvc=mysql_num_rows( mysql_query("SELECT * FROM ".TB_PREFIX."city WHERE galaxy='$galaxy' AND system='$sistem' AND pos='$pos'") );
		
			} while ($pvc!=0);
		
		
			$cin="INSERT INTO ".TB_PREFIX."city (id, owner, name, last_update, galaxy, system, pos) VALUES ($cidr, '$id', '".mysql_real_escape_string($ncity)."', '$mtimet', '$galaxy', '$system', '$pos')"; 
			mysql_query($cin);
		break;
		case 2: 
			//travian sys generate x,y
			do{
				$x=mt_rand(0, $config['Map_max_x']);
				$y=mt_rand(0, $config['Map_max_y']);
				
				$pvc=mysql_num_rows( mysql_query("SELECT * FROM `".TB_PREFIX."map` WHERE `x` =$x AND `y` =$y") );
			}while($pvc!=0);
			mysql_query("INSERT INTO `".TB_PREFIX."map` (`x`, `y`, `type`, `city`) VALUES ('$x', '$y', 'v1', '$cidr');");
			
			$cin="INSERT INTO ".TB_PREFIX."city (id, owner, name, last_update) VALUES ($cidr, '$id', '".mysql_real_escape_string($ncity)."', '$mtimet')";
			mysql_query($cin);
		break;
		case 3:
			//ikariam/grepolis sys
			//generate isle and isle position!
			$numisl=mysql_num_rows( mysql_query("SELECT * FROM `".TB_PREFIX."isle`") );
			
			$ipd[1]="a";
			$ipd[2]="b";
			$ipd[3]="c";
			$ipd[4]="d";
			do{
				$islid=mt_rand(1,$numisl);
				$islpos=mt_rand(1,4);
				$vsplic=mysql_fetch_array( mysql_query("SELECT * FROM `".TB_PREFIX."isle` WHERE `id` =".$islid) );
				$sicp=$vsplic['pos_'.$ipd[$islpos]];	
			}while($sicp!=0);	
			
			$cin="INSERT INTO ".TB_PREFIX."city (id, owner, name, last_update) VALUES ($cidr, '$id', '".mysql_real_escape_string($ncity)."',".$startres." '$mtimet')";
			mysql_query($cin);
			
			$isi="UPDATE `".TB_PREFIX."isle` SET `pos_".$islpos."` = '$cidr' WHERE `id` =".$islid." LIMIT 1 ;";
			mysql_query($isi);	
		break;		
		}	
		
		//insert resources
		$resd= mysql_query("SELECT * FROM `".TB_PREFIX."resdata`");
		while( $fres = mysql_fetch_array($resd) ){
			mysql_query("INSERT INTO `".TB_PREFIX."city_resources` (`city_id`, `res_id`, `res_quantity`) VALUES ('".$cidr."', '".$fres['id']."', '".$fres['start']."');");
		}
		
		$cfg= mysql_fetch_array( mysql_query("SELECT server_name FROM conf LIMIT 1") );
		mail( $mail, "Registration to ".$cfg['server_name'], "Welcome to ".$cfg['server_name'].chr(13)."Username: $nik ".chr(13)."Password: ".$_POST['rpass'] );
		
		header("Location: index.php"); }
	} 
}

function recoverpass( $email ){
	mysql_query("SELECT * FROM `users` WHERE `email` = '$email' LIMIT 1");
}
?>