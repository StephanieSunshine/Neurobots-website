<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>neuroBots</title>
<meta name="keywords" content="turntable, turntable.fm, bots, turntable bots, turntable.fm bots, " />
<meta name="description" content="Neurobots is a platform to make using bots with turntable easy!" />
<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css' />
  <link href="/public.css" type="text/css" rel="stylesheet" /> 

	<script type="text/javascript" src="js/jquery.min.js"></script> 
	<script type="text/javascript" src="js/jquery.scrollTo-min.js"></script> 
	<script type="text/javascript" src="js/jquery.localscroll-min.js"></script> 
	<script type="text/javascript" src="js/init.js"></script> 
    
    <link rel="stylesheet" href="css/slimbox2.css" type="text/css" media="screen" /> 
    <script type="text/JavaScript" src="js/slimbox2.js"></script> 

</head> 
<body> 
<div id="templatemo_header">
    <div id="site_title"><h1><a href="http://www.neurobots.net/" title="neuroBots">neuroBots</a></h1></div>
</div>
<div id="templatemo_main">
    <div id="content"> 
		<div id="home" class="section">
        	
			<div id="home_about" class="box">
        <?php
        $user = "";
$auth = false;
$magic_key = "";
require_once('config/db.conf');
foreach ($db->query('SELECT * FROM users WHERE login_hash = "'.addslashes($_COOKIE['PHPSESSID']).'" LIMIT 1') as $row) {
  $auth = true;
  $magic_key = $row['magic_key'];
  echo "<h2>Verified</h2>";
  echo "Welcome back " . $row['email'];
  echo '<p>Thank you.  You are now verified.</p>';
  echo '<p>Make sure you read the <a href="http://wiki.neurobots.net/">wiki</a> before you start.  </p>';
  $user = $row;
  }
if ($auth) {
  $flags = preg_split('//', $user['flags'],-1,PREG_SPLIT_NO_EMPTY);
  if(!in_array("A",$flags)) $flags[] = "A";
  $packed_flags = implode("", $flags);
  $db->exec('update users SET flags="'.$packed_flags.'" WHERE login_hash="'.addslashes($_COOKIE['PHPSESSID']).'" LIMIT 1');
  $db->exec('CREATE TABLE bot_acls_'.$magic_key.' (userid text NOT NULL, access_level tinyint(4) NOT NULL, comment text)');
  $db->exec('CREATE TABLE bot_adverts_'.$magic_key.' (message text, delay int(11) DEFAULT NULL)');
  $db->exec('CREATE TABLE bot_blacklist_'.$magic_key.' (userid text NOT NULL, reason text NOT NULL)');
  $db->exec('CREATE TABLE bot_events_'.$magic_key.' (event text NOT NULL, delivery_method tinyint(1) NOT NULL, pre_text text, post_text text, include_name tinyint(1))');
  $db->exec('CREATE TABLE bot_triggers_'.$magic_key.' (use_trigger_switch tinyint(1) DEFAULT NULL,use_strict_matching tinyint(1) DEFAULT NULL, trigger_phrase text, pre_name_response text, post_name_response text, pre_command_fail text, post_command_fail text, action text, access_level tinyint(4), use_name_switch tinyint(1), use_saying_switch tinyint(1), command_description text)');
  $db->exec('CREATE TABLE bot_sayings_'.$magic_key.' ( saying text )');
  $db->exec('CREATE TABLE bot_ustats_'.$magic_key.'  ( userid varchar(25), name text, last_seen text, songs_played bigint unsigned default 0, songs_awesomed bigint unsigned default 0, songs_lamed bigint unsigned default 0, songs_snagged bigint unsigned default 0, songs_shared bigint unsigned default 0, UNIQUE (userid))');
  $db->exec('CREATE TABLE bot_sstats_'.$magic_key.' ( songid varchar(33), title text, artist text, times_played bigint unsigned default 1, times_awesomed bigint unsigned default 0, times_lamed bigint unsigned default 0, times_snagged bigint unsigned default 0, first_user_played text, UNIQUE(songid))');

}else{
  echo "<h2>Verification Failed</h2><p>Verification Failed.</p>";
}
echo '</div>';

            if($auth) {
            echo '<a href="/console/"><div class="box home_box1 color1">
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <Center><h3>Console</h3></center>
            </div></a>
            <a href="/store/"><div class="box home_box1 color2">
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <Center><h3>Store</h3></center>
            </div></a>';            
            }else{
            echo '<a href="#Create"><div class="box home_box1 color1">
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <Center><h3>Create</h3></center>
            </div></a>
            <a href="#Login"><div class="box home_box1 color2">
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <Center><h3>Login</h3></center>
            </div></a>';
            }
            ?>
            
            <a href="/index.php#Stats"><div class="box home_box1 color3">
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <Center><h3>Stats</h3></center>
            </div></a>

            <a href="/index.php#Contact"><div class="box home_box1 color4 no_mr">
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <Center><h3>Contact</h3></center>
            </div></a>
               
        </div> <!-- END of home -->
</div>
</div>
<div id="templatemo_footer">
    Copyright © 2013 <a href="#">neuroBots.net</a> | Template designed by <a href="http://www.templatemo.com" target="_parent">Free CSS Templates</a>
</div>

</body> 
</html>
