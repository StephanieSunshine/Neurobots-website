<?php

require_once('../config/db.conf');
$id = addslashes($_GET['query']);
$found = false;
foreach ($db->query("SELECT * from users WHERE owner_userid='".$id."' LIMIT 1") as $row) {
	$found = true;
}

foreach ($db->query("SELECT * from users WHERE bot_userid='".$id."' LIMIT 1") as $row) {
	$found = true;
}

if($found){ echo "true"; }else{ echo "false"; }

?>