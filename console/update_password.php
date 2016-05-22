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
foreach($db->query("select password from users where magic_key=".$db->quote($magic_key)." limit 1") as $pass){
  if($pass['password'] == md5($_POST['opass'])) {
    $db->query("update users set password=".$db->quote(md5($_POST['npass1'])).", login_hash='!' where magic_key=".$db->quote($magic_key));
    echo '<center><h3>Your password has been reset.  Please relogin.</h3></center>';
  }else{
    echo '<center><h3>Your old password did not match. Please try again</h3></center>';
  }
}
    echo '<script>setTimeout( function () { window.opener.location.reload(true); window.close(); }, 2000);</script>';
?> 
</body>
</html>
