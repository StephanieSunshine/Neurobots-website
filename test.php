<?php
$flags = array();
$user = "";
$auth = false;
require_once('config/db.conf');
if(isset($_COOKIE['PHPSESSID'])){
foreach ($db->query('SELECT * FROM users WHERE login_hash = "'.addslashes($_COOKIE['PHPSESSID']).'" LIMIT 1') as $row) {
	$auth = true;
	echo $row['email'];
	$user = $row;
	$flags = preg_split('//', $user['flags'],-1,PREG_SPLIT_NO_EMPTY);
	}
	}
if (!$auth) echo "Welcome Guest";
?>
