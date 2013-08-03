<?php

require_once('../config/db.conf');

$magickey = addslashes($_GET['magic_key']);
$reason = addslashes($_GET['reason']);
$target = addslashes($_GET['target']);
	file_put_contents('bllog', var_export($_GET, $return = true));
	$db->exec('INSERT INTO bot_blacklist_'.$magickey.' SET userid="'.$target.'", reason="'.$reason.'"');

?>