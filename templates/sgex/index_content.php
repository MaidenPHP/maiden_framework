<table border='0' cellpadding='5' cellspacing='5'><tr><td>
    <table border='1' cellpadding='9' style='background:#666' bordercolor='#000000' align='left'><tr><td style='text-align:left'>
    <form method='get' action=''>
        <p><?php echo $lang['reg_language'];?>: <select name='lang' onChange='this.form.submit()'>
        <?php
            $dir = './lang';
            $handle = opendir($dir);
            // Lettura...
            while( $files = readdir($handle) ) {
            // Escludo gli elementi '.' e '..' e stampo il nome del file...
                if ($files != '.' && $files != '..'){  
                    echo '<option ';
                    if( LANG==substr($files,0,-4) ) echo 'selected';
                    echo '>'.substr($files,0,-4).'</option>';
                }
            }
        ?>
        </select></p>
    </form>
    <p><?php echo $lang['idx_numplayers']; ?>: <span class='Stile1'><?php echo $tusr; ?></span></p>
    <p><?php echo $lang['idx_lastreg']; ?>: <span class='Stile1'><?php echo $lastreg['username']; ?></span></p>
    </td></tr></table> </td>
<td><p style='margin: 10px 15px;'><?php include('plugins/fb/fb-like.php'); ?></p></td></tr></table>

<br />
<div><p>
<?php
	$cinf= mysql_fetch_array( mysql_query('SELECT * FROM conf LIMIT 1') );
	echo $cinf['news1'];
?></p></div>