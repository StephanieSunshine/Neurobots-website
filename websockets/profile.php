<?php
function preg_grep_keys( $pattern, $input, $flags = 0 )
{
    $keys = preg_grep( $pattern, array_keys( $input ), $flags );
    $vals = array();
    foreach ( $keys as $key )
    {
        $vals[$key] = $input[$key];
    }
    return $vals;
}

require_once('../config/db.conf');
foreach ($db->query('SELECT * FROM users WHERE bot_userid = "'.addslashes($_GET['userid']).'" AND magic_key = "'.addslashes($_GET['magic_key']).'" LIMIT 1') as $row) {
	$row['password'] = "";

	echo base64_encode(preg_replace('/NULL/','nil', preg_replace( '/\)$/','}',preg_replace( '/array \(/', " { ",var_export(preg_grep_keys("/^\D+/",$row), $return = true)))));	
	#echo preg_replace( '/\)$/','}',preg_replace( '/array \(/', " { ",var_export(preg_grep_keys("/^\D+/",$row), $return = true)));
	}
?>