<?php
require_once('../config/db.conf');
echo $_GET['PHPSESSID'];
if(isset($_GET['PHPSESSID'])){
	$sess = addslashes($_GET['PHPSESSID']);
	foreach ($db->query("SELECT * from users WHERE login_hash='".$sess."' LIMIT 1") as $row) {
		$db->exec("update users set login_hash='!' where magic_key='".$row['magic_key']."'");
	}
}

?>