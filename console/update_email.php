<html>
<head>
<link href="/private.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="/js/jquery.min.js"></script> 
<script type="text/javascript" src="/js/init.js"></script> 
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css' />
<link rel="stylesheet" href="/juithemes/dark-hive/jquery-ui.css" />
<link rel="stylesheet" href="/juithemes/dark-hive/jquery-ui.min.css" />
<link rel="stylesheet" href="/juithemes/dark-hive/jquery.ui.theme.css" />
</head>

<body style="background: #000; color:#fff; padding: 20px">
<?php
require_once('../config/db.conf');
$magic_key = $_POST['magic_key'];
foreach($db->query("select email from users where magic_key=".$db->quote($magic_key)." limit 1") as $row){
    $db->query("update users set login_hash='!' where magic_key=".$db->quote($magic_key));
    $link = base64_encode(base64_encode(base64_encode($row['email']." ".$magic_key." ".$_POST['email'])));
    $full_link = "http://www.neurobots.net/console/verify_email.php?hash=".$link;
    $to      = $_POST['email'];
$subject = 'Account verification for neurobots.net';
$message = 'Please click here to update your email: <a href="'.$full_link.'">Click Here</a>';
$headers = 'From: neurobotsnet@gmail.com' . "\r\n" .
    'Reply-To: neurobotsnet@gmail.com' . "\r\n" ;
$headers  .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
mail($to, $subject, $message, $headers);	
}
    echo '<script>setTimeout( function () { window.opener.location.reload(true); window.close(); }, 2000);</script>';
?> 
</body>
</html>
