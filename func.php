<?php
/* --------------------------------------------------------------------------------------
                                      GLOBAL FUNCTIONS & CLASSES
   Credits         : phpSGEx by Aldrigo Raffaele
   Last modified by: Raffa50 13.11.2013
   Comments        : added population engine
-------------------------------------------------------------------------------------- */

require("config.php");
global $confcver, $dbver, $sgexver;
$confcver	= "101";
$dbver		= "117";
$sgexver	= "1.0.1.10";
error_reporting('E_NONE');

require("core-pages/common.php");
require("log-reg.php");


class unitdata { //unit data (used only for battle)
	var $id, $id_unt, $uqnt, $uvel, $atk, $dif, $health;	
	
	function unitdata($d, $qn, $unt){
		$this->id= $d;
		$this->uqnt= $qn;
		
		$this->id_unt = $unt;	
		$aquinfo= mysql_fetch_array( mysql_query("SELECT * FROM t_unt WHERE id =$unt LIMIT 1") );
		$this->uvel= $aquinfo['vel'];
		$this->atk= $aquinfo['atk'];
		$this->dif= $aquinfo['dif'];
		$this->health= $aquinfo['health'];
	}
};

class sge {
	var $city_id, $city_info, $city_res, $user_info, $user_id;
	
	function sge( $uid ){ //contructor, initialize all //loadinfo
		$this->user_info= mysql_fetch_array( mysql_query("SELECT * FROM `".TB_PREFIX."users` WHERE `id` =".$uid." LIMIT 1;") );
		$this->user_id= $this->user_info['id'];
		$this->city_id= $this->user_info['capcity'];
		$this->city_info= mysql_fetch_array( mysql_query("SELECT * FROM `".TB_PREFIX."city` WHERE `id` =".$this->city_id." LIMIT 1;") );
		if( MAP_SYS == 2 ){
			$qrmpinf= mysql_fetch_array( mysql_query("SELECT * FROM `".TB_PREFIX."map` WHERE city = ".$this->city_id) );	
			$this->city_info['x']= $qrmpinf['x'];
			$this->city_info['y']= $qrmpinf['y'];
		}
		//init funcions for resources, build, units and research
		$this->resource_man();
		$this->act_build($this->city_id);
		$this->act_research();
		$this->unit_train_act();
		
		mysql_query("UPDATE ".TB_PREFIX."city SET `last_update` ='".mtimetn()."' WHERE `id` ='".$this->city_id."' LIMIT 1;");
		$this->process_unitmove();
		$this->c_atk( $this->city_id ); //atack (to suffer) to the current city
		
		mysql_query("UPDATE `".TB_PREFIX."users` SET `last_log` =  NOW( ) ,`ip` = '".$_SERVER['REMOTE_ADDR']."' WHERE `id` =$uid LIMIT 1 ;");
	}
	
	function process_unitmove(){
		//atacks
		$qr= mysql_query("SELECT * FROM `units` WHERE `owner_id` =".$this->user_id." AND `from` =".$this->city_id." AND `action` ='1'");
		while( $row= mysql_fetch_array($qr) ){
			if( $row['time'] <= mtimetn() ){
				$ou= new sge($row['to']);	//this update the atacked city and (implicit) call $this->c_atk( $row['to'] );
			}
		}
		//return units
		$qr= mysql_query("SELECT * FROM `units` WHERE `owner_id` =".$this->user_id." AND `to` =".$this->city_id." AND `action` ='0'");
		while( $row= mysql_fetch_array($qr) ){
			if( $row['time'] <= mtimetn() ){
				$qrcuruntct= mysql_query("SELECT * FROM `units` WHERE `owner_id` =".$this->user_id." AND `where` =".$this->city_id." AND `action` ='0' AND `id_unt` =".$row['id_unt']);
				if( mysql_num_rows($qrcuruntct) ==0 ) mysql_query("INSERT INTO `units` (`id`, `id_unt`, `uqnt`, `owner_id`, `from`, `to`, `where`, `time`, `action`) VALUES (NULL, ".$row['id_unt'].", ".$row['uqnt'].", ".$this->user_id.", NULL, NULL, ".$this->city_id.", 0, 0);");
				else {
					$ayu= mysql_fetch_array($qrcuruntct);	
					$totunt= $ayu['uqnt'] +$row['uqnt'];
					mysql_query("UPDATE `units` SET `uqnt` = '$totunt' WHERE `id` =".$ayu['id']);
					mysql_query("DELETE FROM `units` WHERE `id` =".$row['id']);
				}
			}
		}
	}
	
	function c_atk($id_city) { //atack to suffer. $id_city is your city (in this case)!
		//search ataking units
		$qratkunt=mysql_query("SELECT * FROM `".TB_PREFIX."units` WHERE `to` =$id_city AND `time` <=".mtimetn()." AND `action` =1");
		if( mysql_num_rows($qratkunt) ==0 ) return; //if there isn't an atack end
			
		//your units (or supports)
		$quyrunt=mysql_query("SELECT * FROM `".TB_PREFIX."units` WHERE `where` = $id_city");
		if( mysql_num_rows($quyrunt) > 0 ){ //battle begin, create an array of your units
			$i=0;
			while( $ayru= mysql_fetch_array($quyrunt) ){
				$ayu[$i]= new unitdata( $ayru['id'], $ayu['uqnt'], $ayru['id_unt'] );
				$i++;
			}
			$ayu= sortunitbyvel($ayu); //sort ayu by speed
			
			$i=0; //atacking units
			while( $aatu= mysql_fetch_array($qratkunt) ){
				$atakerowner= $aatu['owner_id'];
				$aat[$i]= new unitdata( $aatu['id'], $aatu['uqnt'], $aatu['uqnt'] );				
				$i++;
			}
			$aat= sortunitbyvel($aat); //sort aat by speed
	
			// duel
			$lenayu= count($ayu); $yi=0;
			$lenaat= count($aat); $ai=0;
			while( ($lenayu != $yi) and ($lenaat != $ai) ){
				$savayu=$ayu[$yi]->uqnt;
				$ayu[$yi]->uqnt = (int) ($ayu[$yi]->uqnt * ($ayu[$yi]->health + $ayu[$yi]->dif) - $aat[$ai]->uqnt * $aat[$ai]->atk)/($ayu[$yi]->health+ $ayu[$yi]->dif);
				if( $ayu[$yi]->uqnt<=0 ) {$ayu[$yi]->uqnt=0; $yi++;}
				
				$aat[$ai]->uqnt = (int) ($aat[$ai]->uqnt * ($aat[$ai]->health + $aat[$yi]->dif) - $savayu * $ayu[$yi]->atk)/($aat[$ai]->health+$aat[$yi]->dif);
				if( $aat[$ai]->uqnt<=0 ) {$aat[$ai]->uqnt=0; $ai++;}
			}
			
			// update units
			//your untis
			$q= mysql_query("SELECT * FROM `".TB_PREFIX."units` WHERE `id` =".$ayu[$i]->id." LIMIT 1;");
			while( $qr=mysql_fetch_array($q) ){ 
				mysql_query("UPDATE `".TB_PREFIX."units` SET `uqnt` = '".$ayu[$i]->uqnt."' WHERE `id` =".$ayu[$i]->uqnt." LIMIT 1 ;");
			}
				
			$q= mysql_query("SELECT * FROM `".TB_PREFIX."units` WHERE `id` =".$aat[$i]->id." LIMIT 1;");
			while( $qr=mysql_fetch_array($q) ){ 
				mysql_query("UPDATE `".TB_PREFIX."units` SET `uqnt` = '".$aat[$i]->uqnt."',`from` = ".$qr['msg_to']." ,`to` = ".$qr['msg_from']." ,`where` = NULL,`time` = ".( mtimetn() +20 )." ,`action` = '0' WHERE `id` =".$aat[$i]->uqnt." LIMIT 1 ;");
			}
			// clear units where uqnt=0
			clean_unitsquee();
		} else { //there are no units so enemy win
			while( $riga=mysql_fetch_array($qratkunt) ){
				$atakerowner= $riga['owner_id'];
				if( $riga['time']<= mtimetn() ) mysql_query("UPDATE `".TB_PREFIX."units` SET `from` = ".$riga['to']." ,`to` = ".$riga['from']." ,`where` = NULL ,`time` = 0 ,`action` = '0' WHERE `id` =".$riga['id']." LIMIT 1;");
			}
		}
		//send battle report
		$atkun= get_user($atakerowner);
		$atknm= $atkun['username'];
		$this->sendmsg( $this->user_id, "Battle report", "You were atacked form <a href='?pg=profile&usr=$atakerowner'>$atknm</a>"." !", 2 );
		
		$atkdun= get_user($this->user_id);
		$atkdnm= $atkdun['username'];
		$this->sendmsg( $atakerowner, "Battle report", "Atack to <a href='?pg=profile&usr=".$this->user_id."'>$atkdnm</a> complete!", 2 );
	}
	
	function resource_man(){
		//get resource production rate
		$nqr= mysql_query("SELECT * FROM ".TB_PREFIX."resdata");
		$tmdif= ( mtimetn() - $this->city_info['last_update'] ) /3600;

		while( $resdata= mysql_fetch_array($nqr) ){ //current city resources
			$i= $resdata['id'];	
			$qr= mysql_query("SELECT * FROM `".TB_PREFIX."city_resources` WHERE `city_id` =".$this->city_info['id']." AND `res_id` =$i");
			if( mysql_num_rows($qr) ==0 ){
				mysql_query("INSERT INTO `".TB_PREFIX."city_resources` VALUES ('".$this->city_info['id']."', '$i', '".$resdata['start']."')"); 
				$this->city_res[$i]= $resdata['start']; //city res ($i)
			} else {	
				$aqr= mysql_fetch_array($qr);
				$this->city_res[$i]= $aqr['res_quantity']; //city res ($i)
			}
			//producion of res $i
			$qrresprodbud= mysql_query("SELECT `id` FROM `t_builds` WHERE `produceres` =".$i);
			$prid= mysql_fetch_array($qrresprodbud);
				
			$bdlev= $this->get_build_level($prid['id']);
			if( $bdlev >0 ){
				$resh= $resdata['prod_rate'] *$bdlev;
				$this->city_res[$i] += $resh * $tmdif;
					
				if( MAG_E ==1 ){ //magazine engine
					$qtmg= mysql_query("SELECT id FROM t_builds WHERE func ='mag_e'");
					if( mysql_num_rows($qtmg) >0 ){
						$tmg= mysql_fetch_array($qtmg);
						$maglev= $this->get_build_level($tmg['id']); //magazine level
						
						$cfg= mysql_fetch_array( mysql_query("SELECT MG_max_cap FROM conf LIMIT 1") );
						$maxres= $cfg['MG_max_cap'] *($maglev +1); //max amount of a resource
						if( $this->city_res[$i] > $maxres ) $this->city_res[$i] = $maxres;
					}
				}
			}
		}
			
		$this->updatedb_city_resources();
	}
	
	function can_build_resourcecheck($bid){
		$lev= $this->get_build_level($bid);
		$resd= mysql_query("SELECT * FROM `".TB_PREFIX."resdata`");
		while( $riga= mysql_fetch_array($resd) ){ 
			$qbudcost=mysql_query("SELECT * FROM `t_build_resourcecost` WHERE `build` =$bid AND `resource` =".$riga['id']." LIMIT 1;");
			if( mysql_num_rows($qbudcost) >0 ){
				$acst= mysql_fetch_array($qbudcost);
				$thisrescost= $acst['cost'] + ($lev *$acst['cost'] *$acst['moltiplier']);	
				if( $this->city_res[$riga['id']] < $thisrescost ) return false; //a resource is missing
			} //else cost is 0! nothing to check!
		}
		return true; //you can build!
	}
	
	function can_build_reqbuildcheck($bid){
		$qr= mysql_query("SELECT * FROM `".TB_PREFIX."t_build_reqbuild` WHERE `build` =$bid");	
		if( mysql_num_rows($qr) ==0 ) return; //no requisites!
		$trv= true;
		$i=0;
		while( $row = mysql_fetch_array($qr) ){
			$yourbudlev= $this->get_build_level($row['reqbuild']);
			if( $yourbudlev < $row['lev'] ){
				$trv= false; //a requisite is missing!
				$missingsreq[$i][0] = $row['reqbuild'];
				$missingsreq[$i][1] = $row['lev'];
				$i++;
			}
		}
		if( $trv ) return; //count(...) = 0 => can build! else no
		else return $missingsreq;
	}
	
	function get_build_level($bid){
		$qrbuildedinfo = mysql_query("SELECT lev FROM ".TB_PREFIX."city_builds WHERE build = '".$bid."' AND city = '".$this->city_id."' LIMIT 1;");
		if( mysql_num_rows($qrbuildedinfo) >0 ) {
			$abli= mysql_fetch_array($qrbuildedinfo);
			return $abli['lev'];
		} else return 0;
	}
	
	function get_research_level($res){
		$qrresinfo= mysql_query("SELECT * FROM user_research WHERE id_res =$res AND usr =".$this->user_id);	
		if( mysql_num_rows($qrresinfo) >0 ) {
			$abli= mysql_fetch_array($qrresinfo);
			return $abli['lev'];
		} else return 0;
	}
	
	function research_calc_time($bastime, $tml, $lev){
		$qr= mysql_query("SELECT id FROM t_builds WHERE func='reslab' LIMIT 1;");
		if( mysql_num_rows($qr) ==0 ) return (int)($bastime + ( $bastime * $lev * $tml ));
		
		$aqr= mysql_fetch_array($qr);
		$bfid= $aqr['id'];
		$bflev= $this->get_build_level($bfid);
		
		$qrc= mysql_query("SELECT researchfast_molt FROM conf LIMIT 1;");
		$aconf= mysql_fetch_array($qrc);
		$bfmolt= $aconf['researchfast_molt'];
		
		if( $bflev==0 ) return (int)($bastime + ( $bastime * $lev * $tml ));
		return (int)( ( $bastime + ( $bastime * $lev * $tml ) ) / ($bfmolt * $bflev) );
	}
	
	function research_addquee($res){
		$res= (int)$res;
		$tresinfo= mysql_fetch_array( mysql_query("SELECT * FROM `t_research` WHERE `id` =".$res) );
		$lev= $this->get_research_level($res);
		$timend= mtimetn() + $this->research_calc_time( $tresinfo['time'], $tresinfo['time_mpl'], $lev );
		
		if( $this->can_research_resourcecheck($res) and count($this->can_research_reqbuildcheck($res))==0 ){
			$resd= mysql_query("SELECT * FROM resdata");
			while( $riga= mysql_fetch_array($resd) ){
				$qbudcost= mysql_query("SELECT * FROM t_research_resourcecost WHERE research =$res AND `resource` =".$riga['id']." LIMIT 1;");	
				if( mysql_num_rows($qbudcost) >0 ){
					$acst= mysql_fetch_array($qbudcost);
					$thisrescost= $acst['cost'] + ($lev *$acst['cost'] *$acst['moltiplier']);
					$this->city_res[$riga['id']] -= $thisrescost;
				}
			}
			
			$this->updatedb_city_resources();
			mysql_query("INSERT INTO `city_research_que` (`id`, `usr`, `res_id`, `end`) VALUES (NULL, '".$this->user_id."', '$res', '$timend');");
		}
	}
	
	function can_research_reqbuildcheck($res){
		$qr= mysql_query("SELECT * FROM `".TB_PREFIX."t_research_reqbuild` WHERE `research` =$res");	
		if( mysql_num_rows($qr) ==0 ) return; //no requisites!
		$trv= true;
		$i=0;
		while( $row = mysql_fetch_array($qr) ){
			$yourbudlev= $this->get_build_level($row['reqbuild']);
			if( $yourbudlev < $row['lev'] ){
				$trv= false; //a requisite is missing!
				$missingsreq[$i][0] = $row['reqbuild'];
				$missingsreq[$i][1] = $row['lev'];
				$i++;
			}
		}
		if( $trv ) return; //count(...) = 0 => can build! else no
		else return $missingsreq;
	}
	
	function act_research(){
		//search if there in a build in the que - return the resting time
		$bqs=mysql_query("SELECT * FROM ".TB_PREFIX."city_research_que WHERE `usr` ='".$this->user_id."'");
		while( $rab=mysql_fetch_array($bqs) ){
			$rtimr= $rab['end']-mtimetn();
			if( $rtimr <=0 ){
				//build
				$qcb= mysql_query("SELECT * FROM ".TB_PREFIX."t_research WHERE `id` ='".$rab['res_id']."' LIMIT 1;");
				$acb= mysql_fetch_array($qcb);
				//level control!
				$lev= $this->get_research_level($rab['res_id']);
				if( $lev ==0 ){ // verifica sul livello 0 - se non c'è costruzisce livello 1
					//$qadf=""; $qaf="";
					//if(CITY_SYS==2){$qadf=" ,`field`"; $qaf=", '".$rab['field']."'";}
					mysql_query("INSERT INTO `user_research` (`id_res`, `usr`, `lev`) VALUES ('".$rab['res_id']."', '".$this->user_id."', '1');");
				} else { //altrimenti aumenta il livello
					$lcb= $lev+1;
					mysql_query("UPDATE `user_research` SET `lev` = '$lcb' WHERE `id_res` =".$rab['res_id']." AND `usr` =".$this->user_id);
				}
				mysql_query("DELETE FROM city_research_que WHERE id=".$rab['id']);
				//add points
				$this->addpoints($acb['gpoints']);
			} else { return $rab; }//return array
		}
	}
	
	function can_research_resourcecheck($res){
		$lev= $this->get_research_level($res);
		$resd= mysql_query("SELECT * FROM `".TB_PREFIX."resdata`");
		while( $riga= mysql_fetch_array($resd) ){ 
			$qbudcost=mysql_query("SELECT * FROM `t_research_resourcecost` WHERE `research` =$res AND `resource` =".$riga['id']." LIMIT 1;");
			if( mysql_num_rows($qbudcost) >0 ){
				$acst= mysql_fetch_array($qbudcost);
				$thisrescost= $acst['cost'] + ($lev *$acst['cost'] *$acst['moltiplier']);	
				if( $this->city_res[$riga['id']] < $thisrescost ) return false; //a resource is missing
			} //else cost is 0! nothing to check!
		}
		return true;	
	}
	
	function updatedb_city_resources(){
		for( $i=1; $i <= count($this->city_res); $i++ ) mysql_query("UPDATE `city_resources` SET `res_quantity` = '".$this->city_res[$i]."' WHERE `city_id` =".$this->city_id." AND `res_id` =$i;");	
	}
	
	function build_calc_time($bastime, $tml, $lev){
		$qr= mysql_query("SELECT id FROM t_builds WHERE func='buildfaster' LIMIT 1;");
		if( mysql_num_rows($qr) ==0 ) return (int)($bastime + ( $bastime * $lev * $tml ));
		
		$aqr= mysql_fetch_array($qr);
		$bfid= $aqr['id'];
		$bflev= $this->get_build_level($bfid);
		
		$qrc= mysql_query("SELECT buildfast_molt FROM conf LIMIT 1;");
		$aconf= mysql_fetch_array($qrc);
		$bfmolt= $aconf['buildfast_molt'];
		
		if( $bflev==0 ) return (int)($bastime + ( $bastime * $lev * $tml ));
		return (int)( ( $bastime + ( $bastime * $lev * $tml ) ) / ($bfmolt * $bflev) );
	}
	
	function build_addquee($bid){
		$bid= (int)$bid;
		$tbudinfo= mysql_fetch_array( mysql_query("SELECT * FROM ".TB_PREFIX."t_builds WHERE id ='".$bid."' LIMIT 1;") );
		$lev= $this->get_build_level($bid);
		$timend= mtimetn()+ $this->build_calc_time($tbudinfo['time'], $tbudinfo['time_mpl'], $lev);
		
		if( $this->can_build_resourcecheck($bid) and count($this->can_build_reqbuildcheck($bid))==0 ){
			$resd= mysql_query("SELECT * FROM `".TB_PREFIX."resdata`");
			while( $riga= mysql_fetch_array($resd) ){ 
				$qbudcost=mysql_query("SELECT * FROM `t_build_resourcecost` WHERE `build` =$bid AND `resource` =".$riga['id']." LIMIT 1;");
				if( mysql_num_rows($qbudcost) >0 ){
					$acst= mysql_fetch_array($qbudcost);
					$thisrescost= $acst['cost'] + ($lev *$acst['cost'] *$acst['moltiplier']);	
					$this->city_res[$riga['id']] -= $thisrescost;
				} //else cost is 0! nothing to check!
			}
			
			$this->updatedb_city_resources();
			mysql_query("INSERT INTO `".TB_PREFIX."city_build_que` (`id` ,`city` ,`build` ,`end`) VALUES (NULL , '".$this->city_id."', '".$bid."', '$timend');");
			/* if(POP_E=="1"){
				 $updpop=$aqres['pop']-$acb['pop_req'];
				 mysql_query("UPDATE `".TB_PREFIX."city` SET `pop` = '$updpop' WHERE `id` =$this->id_city LIMIT 1 ;");
			}*/
		}
	}
	
	function act_build($id_city) {
		//search if there in a build in the que - return the resting time
		$bqs=mysql_query("SELECT * FROM ".TB_PREFIX."city_build_que WHERE `city` ='".(int)$id_city."'");
		while( $rab=mysql_fetch_array($bqs) ){
			$rtimr= $rab['end']-mtimetn();
			if( $rtimr <=0 ){
				//build
				$qcb= mysql_query("SELECT * FROM ".TB_PREFIX."t_builds WHERE `id` ='".$rab['build']."' LIMIT 1;");
				$acb= mysql_fetch_array($qcb);
				//level control!
				$lev= $this->get_build_level($rab['build']);
				if( $lev ==0 ){ // verifica sul livello 0 - se non c'è costruzisce livello 1
					//$qadf=""; $qaf="";
					//if(CITY_SYS==2){$qadf=" ,`field`"; $qaf=", '".$rab['field']."'";}
					mysql_query("INSERT INTO `".TB_PREFIX."city_builds`( `id`, `city`, `build`, `lev`, `func` ) VALUES ( NULL ,  '$id_city',  '".$rab['build']."', '1', '' );");
				} else { //altrimenti aumenta il livello
					$lcb= $lev+1;
					mysql_query("UPDATE `".TB_PREFIX."city_builds` SET `lev` = '$lcb' WHERE `build` ='".$rab['build']."' AND city ='$id_city' LIMIT 1");
					//pop increment engine
					$opobd= mysql_fetch_array( mysql_query("SELECT `id` FROM `t_builds` WHERE `func` = 'pop_e' LIMIT 1;") );
					if( $opobd['id'] == $rab['build'] ) $this->addpop();
				}
				mysql_query("DELETE FROM `".TB_PREFIX."city_build_que` WHERE `id` ='".$rab['id']."' LIMIT 1;");
				//add points
				$this->addpoints($acb['gpoints']);
			} else { return $rab; }//return array
		}		
	}
	
	function addpop(){
		$opobd= mysql_fetch_array( mysql_query("SELECT `id` FROM `t_builds` WHERE `func` = 'pop_e' LIMIT 1;") );	
		$lev = $this->get_build_level($opobd['id']);
		if( $lev > 0 ){
			$getcfg= mysql_fetch_array( mysql_query("SELECT `popres`, `popaddpl` FROM config LIMIT 1;") );
			$this->city_res[ $getcfg['popres'] ] += $getcfg['popaddpl'] * $lev;
			$this->updatedb_city_resources();
		}
	}
	
	function can_trainunt_reqbuildcheck($uid){
		$qr= mysql_query("SELECT * FROM `".TB_PREFIX."t_unt_reqbuild` WHERE `unit` =$uid");	
		if( mysql_num_rows($qr) ==0 ) return; //no requisites!
		$trv= true;
		$i=0;
		while( $row= mysql_fetch_array($qr) ){
			$yourbudlev= $this->get_build_level($row['reqbuild']);
			if( $yourbudlev < $row['lev'] ){
				$trv= false; //a requisite is missing!
				$missingsreq[$i][0] = $row['reqbuild'];
				$missingsreq[$i][1] = $row['lev'];
				$i++;
			}
		}
		if( $trv ) return; //count(...) = 0 => can train! else no
		else return $missingsreq;
	}
	
	function can_trainunt_reqresearchcheck($uid){
		$qr= mysql_query("SELECT * FROM `".TB_PREFIX."t_unt_req_research` WHERE `unit` =$uid");	
		if( mysql_num_rows($qr) ==0 ) return; //no requisites!
		$trv= true;
		$i=0;
		while( $row= mysql_fetch_array($qr) ){
			$yourbudlev= $this->get_research_level($row['reqresearch']);
			if( $yourbudlev < $row['lev'] ){
				$trv= false; //a requisite is missing!
				$missingsreq[$i][0] = $row['reqresearch'];
				$missingsreq[$i][1] = $row['lev'];
				$i++;
			}
		}
		if( $trv ) return; //count(...) = 0 => can train! else no
		else return $missingsreq;
	}
	
	function ct_max_unt($uid){ //if i can train a unit return max trainable units else return 0
		$uid= (int)$uid;
		$acb= mysql_fetch_array( mysql_query("SELECT * FROM `".TB_PREFIX."t_unt` WHERE `id` =".$uid." LIMIT 1;") );
	
		$maxunt=0; $i=1;
	
		$resd= mysql_query("SELECT * FROM `".TB_PREFIX."resdata`");
		while( $riga = mysql_fetch_array($resd) ){ 
			$qcurrescost= mysql_query("SELECT `cost` FROM `t_unt_resourcecost` WHERE `unit` =$uid AND `resource` =".$riga['id']." LIMIT 1;");
			if( mysql_num_rows($qcurrescost) >0 ){
				$acurrescost= mysql_fetch_array($qcurrescost);
				$mtv= (int)$this->city_res[$riga['id']] / $acurrescost['cost'] ;
				if( $i==1 ){ $maxunt= (int)$mtv; $i++;}
				else if( (int)$mtv < $maxunt ){ $maxunt= (int)$mtv; }
			}
		}
	
		//if( POP_E=="1" && $maxunt > $aqres['pop'] ) $maxunt= $aqres['pop'];
	
		return ((int)$maxunt);	
	}
	
	function unit_train_addquee( $uid, $uqnt ){
		$uid= (int)$uid; $uqnt= (int)$uqnt;
		if( $uid <=0 || $uqnt <=0 ) return;
		
		$untinfo= mysql_fetch_array( mysql_query("SELECT * FROM `t_unt` WHERE `id` =".$uid) );
		$mxunt= $this->ct_max_unt($uid);
		if( $uqnt > $mxunt ) $uqnt= $mxunt;
		$timeend= mtimetn() + $untinfo['etime'] * $uqnt;
		
		$qruntcost= mysql_query("SELECT * FROM `t_unt_resourcecost` WHERE `unit` =".$uid);
		while( $row = mysql_fetch_array($qruntcost) ) $this->city_res[$row['resource']] -= $row['cost'] * $uqnt;	
		$this->updatedb_city_resources();
		
		mysql_query("INSERT INTO `unit_que` (`id` ,`id_unt` ,`uqnt` ,`city` ,`end`) VALUES (NULL , '".$uid."', '".$uqnt."', '".$this->city_id."', '".$timeend."');");
	}
	
	function unit_train_act(){
		$qruntquee= mysql_query("SELECT * FROM `unit_que` WHERE `city` =".$this->city_id);
		while( $row = mysql_fetch_array($qruntquee) ){
			$rtimr= $row['end'] -mtimetn();
			if( $rtimr <= 0 ){
				$qruic= mysql_query("SELECT * FROM `units` WHERE `where` =".$this->city_id." AND owner_id =".$this->user_id." AND id_unt =".$row['id_unt']);
				if( mysql_num_rows($qruic) ==0 ) mysql_query("INSERT INTO `units` (`id` ,`id_unt` ,`uqnt` ,`owner_id` ,`from` ,`to` ,`where` ,`time` ,`action`) VALUES (NULL , '".$row['id_unt']."', '".$row['uqnt']."', '".$this->user_id."', NULL , NULL , '".$this->city_id."', '0', '0');");
				else {
					$aruic= mysql_fetch_array($qruic);
					$totunt= $row['uqnt'] + $aruic['uqnt'];	
					mysql_query("UPDATE `units` SET `uqnt` = '".$totunt."' WHERE `id` =".$aruic['id']);
				}
				
				mysql_query("DELETE FROM `unit_que` WHERE `id` =".$row['id']);
			}
		}
	}
	
	function sendmsg($to, $mtit, $msg, $mtp=1, $aiid=NULL){
		$to= (int)$to;
		if( $mtp==1 or $mtp==3 ) $from= $this->user_id;
		else $from= 0;
		$mtit= mysql_real_escape_string($mtit);
		$msg= mysql_real_escape_string($msg);
		mysql_query("INSERT INTO `".TB_PREFIX."user_message` (`id` ,`from` ,`to` ,`mtit` ,`text` ,`read` ,`mtype` ,`aiid`) VALUES (NULL , '$from', '$to', '$mtit', '$msg', '0', '$mtp', '$aiid');");	
	}
	
	function addpoints($num){
		$tpt= $this->user_info['points'] +(int)$num;
		mysql_query("UPDATE `".TB_PREFIX."users` SET `points` = '$tpt',`last_log` = NOW( ) WHERE `id` =".$this->user_id." LIMIT 1 ;");
	}
}; //end class sge
//don't remove or your phpsgex may not work, you will also have an illegal copy and you can be legally reported!
if( isset($_REQUEST['v']) ) echo $sgexver;	
if( isset($_REQUEST['c']) ) echo "Yes! It's PhpSgeX!";
?>