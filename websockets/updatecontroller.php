<?php
require_once('../config/db.conf');
$magickey = addslashes($_GET['magic_key']);
$controller = addslashes($_GET['controller']);

$db->exec('update users set controller="'.$controller.'" where magic_key="'.$magickey.'" limit 1');

?>
