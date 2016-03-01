<?php
if( isset($secure) ){
	echo "<!DOCTYPE html>
	<html>
	<head>
		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
		<title>".$config['server_name']."</title>
	
		<link rel='shortcut icon' href='favicon.ico' >
		<link rel='stylesheet' href='templates/sgex/style.css' type='text/css' media='screen' />
		<!--[if IE 6]><link rel='stylesheet' href='style.ie6.css' type='text/css' media='screen' /><![endif]-->
		<!--[if IE 7]><link rel='stylesheet' href='style.ie7.css' type='text/css' media='screen' /><![endif]-->
	
		<script src='templates/sgex/jquery.js'></script>
		<script src='templates/sgex/script.js'></script>
		<script src='templates/js/showhide.js'></script>
		$head
	   <style type='text/css'>
	.art-post .layout-item-0 { padding-right: 10px;padding-left: 10px; }
	   .ie7 .art-post .art-layout-cell {border:none !important; padding:0 !important; }
	   .ie6 .art-post .art-layout-cell {border:none !important; padding:0 !important; }
	   </style>
	
	</head>
	<body $blol>
	<div id='art-page-background-middle-texture'>
	<div id='art-main'>
		<div class='cleared reset-box'></div>
		<div class='art-header'>
			<div class='art-header-position'>
				<div class='art-header-wrapper'>
					<div class='cleared reset-box'></div>
					<div class='art-header-inner'>
					<div class='art-logo'>
									 <h1 class='art-logo-name'>
									 <table border='0' width='100%'><tr>
									 <td><a href='#'>".$config['server_name']."</a><br><h2 class='art-logo-text'>".$config['server_desc_sub']."</h2></td>
									 <td align='right'><img src='img/sgex.png' width=350 height=100 /></td>
									 </tr></table>
									 </h1>
									</div>
					</div>
				</div>
			</div>
			
		</div>
		<div class='cleared reset-box'></div>
		<div class='art-box art-sheet'>
			<div class='art-box-body art-sheet-body'>
				<div class='art-layout-wrapper'>
					<div class='art-content-layout'>
						<div class='art-content-layout-row'>";
						 include('menu.php');
						echo "<div class='art-layout-cell art-content'>
	<div class='art-box art-post'>
		<div class='art-box-body art-post-body'>
	<div class='art-post-inner art-article'>
									<div class='art-postmetadataheader'>
											";
													if( isset($me) ){
														echo $me->user_info['username']."
														<select name='city'>";
															
														$ycqr= mysql_query("SELECT * FROM `city` WHERE `owner` =".$me->user_id);
														while( $row= mysql_fetch_array($ycqr) ) 
															echo "<option>".$row['name']."</option>";
															
														echo "</select>
														 <a href='?pg=message'>Messages($nummsg)</a><br><hr>";
														$resnames= get_resources_name();
														$resicons= get_resource_icons();
														for( $i=1; $i<= count($me->city_res); $i++ ){
														
															if ($config['FLAG_SZERORES'] || !$config['FLAG_SZERORES'] && (int)$me->city_res[$i] >0) {
																if( $config['FLAG_RESICONS'] ) echo "<img src='". IRES . $resicons[$i] . "'/> ";
																if( $config['FLAG_RESLABEL'] ) echo $resnames[$i] . ": ";

																echo (int)$me->city_res[$i] . ' ';
															
																if( MAG_E ==1 ){ //magazine engine
																	$qtmg= mysql_query("SELECT id FROM t_builds WHERE func ='mag_e'");
																	if( mysql_num_rows($qtmg) >0 ){
																		$tmg= mysql_fetch_array($qtmg);
																		$maglev= $me->get_build_level($tmg['id']); //magazine level
																		$maxres= $config['MG_max_cap'] *( $maglev +1 );
																		if( $config['popres'] != $i ) echo "/$maxres ";
																	}
														
																}
														
															}
														}
															
													}
												
											echo "
																				</div>
									<div class='art-postcontent'>
	<div class='art-content-layout'>
		<div class='art-content-layout-row'>
		<div class='art-layout-cell layout-item-0' style='width: 100%;'>
			  <p>".$body."</p>
		</div>
		</div>
	</div>
						  </div>
					<div class='cleared'></div>
					</div>
	
			<div class='cleared'></div>
		</div>
	</div>
	
							  <div class='cleared'></div>
							</div>
						</div>
					</div>
				</div>
				<div class='cleared'></div>";
				require('templates/sgex/footer.php'); 
				echo "<div class='cleared'></div>
			</div>
		</div>
		<div class='cleared'></div>
		<div class='cleared'></div>
	</div>
	</div>
	
	</body>
	</html>";
}
?>