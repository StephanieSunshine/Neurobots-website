<?php
require_once('../config/db.conf');
$magic_key = $_POST['magic_key'];
$db->exec("truncate table bot_sayings_".$magic_key);
$sayings = array();
if ($_FILES["file"]["error"] > 0)
  {
  echo "Error: " . $_FILES["file"]["error"] . "<br>";
  }
else
  {
	$ext = end(explode(".", $_FILES["file"]["name"]));
	if('CSV' == strtoupper($ext)) {
  		if(($handle = fopen($_FILES["file"]["tmp_name"], 'r')) !== FALSE) {
			$counter=0;
    			while (($data = fgetcsv($handle, 1000, ',', '"')) !== FALSE) {
            			$db->exec("INSERT INTO bot_sayings_".$magic_key." set saying=".$db->quote($data[0]));
				$counter++;
    				}
    		fclose($handle);
		echo "Loaded $counter sayings<br />You may close this window now";
		}		
	}else{
		echo "Error: Bad File Type. File Type Found: ".$_FILES["file"]["type"];
	}
  }

?> 
