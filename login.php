<?php
$user = "";
$auth = false;
$pass = md5($_POST['password']);
require_once('config/db.conf');
foreach ($db->query('SELECT * FROM users WHERE email = '.$db->quote($_POST['email']).' AND password='.$db->quote($pass).' AND flags IS NOT NULL LIMIT 1') as $row) {
	$auth = true;
	$db->exec('UPDATE users SET login_hash="'.$_COOKIE['PHPSESSID'].'" WHERE email="'.addslashes($_POST['email']).'" LIMIT 1');
	header("Location: http://".$_SERVER['HTTP_HOST']."/");
	// $user = $row;
	}
	if(!$auth) { 
		setcookie("invalidlogin","true");
	header("Location: http://".$_SERVER['HTTP_HOST']."/#Login");
	}
?>
