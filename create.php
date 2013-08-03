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
             <h2>Almost done</h2>
	<?php
    function magic($length = 32) {
      $key = '';
      $values = preg_split('//',"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789");
          for($i = 0; $i < $length; $i ++) {
          $key .= $values[mt_rand(0,count($values)-1)];
      }

      return $key;
  }
$user = "";
$auth = false;
require_once('config/db.conf');
foreach ($db->query('SELECT * FROM users WHERE email = "'.addslashes($_POST['email']).'" LIMIT 1') as $row) {
	$auth = true;
	echo "<p>Your email address already exists in our system.  Please go back and try a different address</p>";
	$user = $row;
	}
if(!$auth) {
  $unique = false;
  $magic = magic(32);
   while($unique == false){
    $unique = true;
    foreach($db->query("select id from users where magic_key=".$db->quote($magic)) as $row) {
      $unique = false;
      $magic = magic(32);
    }
   }

	$db->exec('INSERT INTO users ( login_hash, email, password, owner_userid, bot_userid, bot_authid, bot_roomid, role, magic_key ) VALUES ( "'.addslashes($_COOKIE['PHPSESSID']).'", "'.addslashes($_POST['email']).'", "'.md5($_POST['password1']).'", "'.addslashes($_POST['ouserid']).'", "'.addslashes($_POST['userid']).'", "'.addslashes($_POST['authid']).'", "'.addslashes($_POST['roomid']).'", "user", "'.$magic.'")');
$to      = $_POST['email'];
$subject = 'Account verification for neurobots.net';
$message = file_get_contents('config/verify_header.conf') .'<a href="http://'.$_SERVER['HTTP_HOST'].'/verify.php?ticket='.$_COOKIE['PHPSESSID'].'">http://'.$_SERVER['HTTP_HOST'].'/verify.php?ticket='.$_COOKIE['PHPSESSID'].'</a><br/>'  . file_get_contents('config/verify_header.conf');
$headers = 'From: neurobotsnet@gmail.com' . "\r\n" .
    'Reply-To: neurobotsnet@gmail.com' . "\r\n" ;
$headers  .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
mail($to, $subject, $message, $headers);	
	
	echo '<p>While we are creating your bot please check your email for your verification email at '.$to.'.  Once you have verified your email address you can login by going <a href="/index.php#Login">here</a>.</p>';
	}
	?>            
	    </div>
            
            <a href="/index.php#Create"><div class="box home_box1 color1">
            	<!-- <a href="#Create"><img src="images/templatemo_services.jpg" alt="Create" /></a> -->
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <Center><h3>Create</h3></center>
            </div></a>
            
            <a href="/index.php#Login"><div class="box home_box1 color2">
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <Center><h3>Login</h3></center>
            </div></a>
            
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
