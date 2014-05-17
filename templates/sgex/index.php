<?php
if( isset($secure) ){
        echo "<!DOCTYPE html><html><head>
    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
    <title>".$config['server_name']."</title>

        <link rel='shortcut icon' href='favicon.ico' >
        <meta content='favicon.ico' itemprop='image' />
    <link rel='stylesheet' href='templates/sgex/style.css' type='text/css' media='screen' />
    <!--[if IE 6]><link rel='stylesheet' href='style.ie6.css' type='text/css' media='screen' /><![endif]-->
    <!--[if IE 7]><link rel='stylesheet' href='style.ie7.css' type='text/css' media='screen' /><![endif]-->

    <script type='text/javascript' src='templates/sgex/jquery.js'></script>
    <script type='text/javascript' src='templates/sgex/script.js'></script>
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
                                 <td align='right'><img src='img/sgex.png' width=350 height=100/></td>
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
                    <div class='art-content-layout-row'>
                        <div class='art-layout-cell art-sidebar1'>
<div class='art-box art-vmenublock'>
    <div class='art-box-body art-vmenublock-body'>
                <div class='art-bar art-vmenublockheader'>
                    <h3 class='t'>PhpSgeX</h3>
                </div>
                <div class='art-box art-vmenublockcontent'>
                    <div class='art-box-body art-vmenublockcontent-body'>
                <ul class='art-vmenu'>
        <li>
                <a href='index.php' class='active'>Main</a>
                <ul class='active'>
                        <li> <a href='?pg=credits'>Credits</a> </li>
                        <li> <a href='https://sourceforge.net/apps/phpbb/phpstrategygame'>Forum</a> </li>
                </ul>
        </li>   
</ul>
                
                                                <div class='cleared'></div>
                    </div>
                </div>
                <div class='cleared'></div>
    </div>
</div>
<div class='art-box art-block'>
    <div class='art-box-body art-block-body'>
                <div class='art-bar art-blockheader'>
                    <h3 class='t'>Login</h3>
                </div>
                <div class='art-box art-blockcontent'>
                    <div class='art-box-body art-blockcontent-body'>
					<form name='reg_login' id='reg_login' method='post' action=''>
                        <input type='hidden' name='act' value='reg_login'>
                        <table border='0'>
                        <tr><td>Nik</td><td><input type='text' name='reg_nickname' id='reg_nickname' size='18' required></td></tr>
                        <tr><td>Password</td><td><input type='password' name='reg_password' id='reg_password' size='18' required></td></tr>
                        <tr><td colspan='2' align='center'>
                            <span class='art-button-wrapper'>
                                <span class='art-button-l'> </span>
                                <span class='art-button-r'> </span>
                                <a href='?pg=register'><input type='button' value=' ".$lang['reg_register']." ' class='art-button' onClick=\"location.href='?pg=register'\"></a>
                            </span>
                            
                            <span class='art-button-wrapper'>
                                <span class='art-button-l'> </span>
                                <span class='art-button-r'> </span>
                                <input type='submit' value=' ".$lang['reg_login']." ' class='art-button'>
                            </span>
							<div> <a href='?pg=register&act=recoverpass'>Reset Password</a> </div>
                        </td></tr>
                    </table>
                </form>                
                    <div class='cleared'></div>
                    </div>
                </div>
                <div class='cleared'></div>
    </div>
</div>

<div class='art-box art-block'>
    <div class='art-box-body art-block-body'>
                <div class='art-box art-blockcontent'>
                    <div class='art-box-body art-blockcontent-body'>
                <p>PhpSgeX Version: <span class='Stile3'>".$sgexver."</span></p>                
                    <div class='cleared'></div>
                    </div>
                </div>
                <div class='cleared'></div>
    </div>
</div>

                          <div class='cleared'></div>
                        </div>
                        <div class='art-layout-cell art-content'>
<div class='art-box art-post'>
    <div class='art-box-body art-post-body'>
<div class='art-post-inner art-article'>
                                <div class='art-postmetadataheader'>
                                        ".$config['server_desc_main']."
                                </div>
                                <div class='art-postcontent'>
<div class='art-content-layout'>
    <div class='art-content-layout-row'>
    <div class='art-layout-cell layout-item-0' style='width: 100%;'>";
                        if( $pg != "index" ) echo $body;
                        else include('templates/'.$config['template'].'/index_content.php');
    echo "</div>
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