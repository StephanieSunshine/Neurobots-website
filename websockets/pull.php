<?php
	require_once('../config/db.conf');
	if(array_key_exists('bot_userid', $_GET)  && array_key_exists('magic_key', $_GET)) {
		$botUserid = addslashes($_GET['bot_userid']);
		$magicKey = addslashes($_GET['magic_key']);
		//$db->exec("create table bot_arrays_".bot['bot_userid'] .' ( array_name TEXT, value TEXT )');
        //$db->exec('create table bot_hashes_'.bot['bot_userid'] .' ( hash_name TEXT, hash_key TEXT, hash_value TEXT)');
//{
//"employees": [
//{ "firstName":"John" , "lastName":"Doe" }, 
//{ "firstName":"Anna" , "lastName":"Smith" }, 
//{ "firstName":"Peter" , "lastName":"Jones" }
//]
//}
//		create table bot_adverts_50f58fc1aaa5cd56ea1d4ca0 ( message text, delay int );# MySQL returned an empty result set (i.e. zero rows).

//create table bot_triggers_50f58fc1aaa5cd56ea1d4ca0 ( use_trigger_switch boolean, use_strict_matching boolean, trigger_phrase text, response text, action text );# MySQL returned an empty result set (i.e. zero rows).

		echo("{\n");
		
		foreach ($db->query("SELECT * from users WHERE magic_key='".$magicKey."' LIMIT 1") as $row) {
			echo('"flags":'.json_encode($row['flags']).','."\n");
			echo('"bot_userid":'.json_encode($row['bot_userid']).', '."\n");
		    echo('"bot_authid":'.json_encode($row['bot_authid']).', '."\n");
		    echo('"bot_roomid":'.json_encode($row['bot_roomid']).', '."\n");
		    echo('"command_trigger":'.json_encode($row['command_trigger']).', '."\n");
		    echo('"room_description":'.json_encode($row['room_description']).', '."\n");
		    echo('"stats_bg_color":"'.$row['stats_bg_color'].'", '."\n");
		    echo('"start_slide":"'.$row['start_slide'].'", '."\n");
		    echo('"start_queue":"'.$row['start_queue'].'", '."\n");
		    echo('"mods_to_lvl1":"'.$row['mods_to_lvl1'].'", '."\n");
		    echo('"start_stats":"'.$row['start_stats'].'", '."\n");
		    echo('"start_autodj":"'.$row['start_autodj'].'", '."\n");
		    echo('"stats_color_theme":"'.$row['stats_color_theme'].'", '."\n");
		    echo('"switch_alonedj":"'.$row['switch_alonedj'].'", '."\n");
		    echo('"switch_autorequeue":"'.$row['switch_autorequeue'].'", '."\n");
			echo('"owner_userid":"'.$row['owner_userid'].'",'."\n");
		}
		
		echo('"events": ['."\n");
		foreach ($db->query("SELECT * from bot_events_$magicKey ORDER BY event") as $row) {
			echo('{"event":"'.$row['event'].'",');
		    echo('"delivery_method":"'.$row['delivery_method'].'", ');
		    echo('"pre_text":'.json_encode($row['pre_text']).', ');
		    echo('"post_text":'.json_encode($row['post_text']).', ');
			echo('"include_name":"'.$row['include_name'].'"},'."\n");
		    }
		echo("{}\n],\n");

		echo('"acl": ['."\n");
		foreach ($db->query("SELECT * from bot_acls_$magicKey ORDER BY comment") as $row) {
			echo('{"userid":"'.$row['userid'].'",');
			echo('"comment":'.json_encode($row['comment']).',');
			echo('"access_level":"'.$row['access_level'].'"},'."\n");
		    }
		echo("{}\n],\n");

		echo('"adverts": ['."\n");
		foreach ($db->query("SELECT * from bot_adverts_$magicKey") as $row) {
			echo('{"message":'.json_encode($row['message']).',');
			echo('"delay":"'.$row['delay'].'"},'."\n");
		    }
		echo("{}\n],\n");
		 
		echo('"triggers": ['."\n");
		foreach ($db->query("SELECT * from bot_triggers_$magicKey ORDER BY trigger_phrase") as $row) {
		    echo('{"use_trigger_switch":"'.$row['use_trigger_switch'].'", ');
		    echo('"use_name_switch":"'.$row['use_name_switch'].'", ');
		    echo('"use_saying_switch":"'.$row['use_saying_switch'].'", ');
		    echo('"use_strict_matching":"'.$row['use_strict_matching'].'", ');
		    echo('"trigger_phrase":"'.$row['trigger_phrase'].'", ');
		    echo('"pre_name_response":'.json_encode($row['pre_name_response']).', ');
		    echo('"post_name_response":'.json_encode($row['post_name_response']).', ');
		    echo('"pre_command_fail":'.json_encode($row['pre_command_fail']).', ');
		    echo('"post_command_fail":'.json_encode($row['post_command_fail']).', ');
		    echo('"command_description":'.json_encode($row['command_description']).', ');
		    echo('"access_level":"'.$row['access_level'].'", ');		    
		    echo('"action":"'.$row['action'].'"},'."\n");
		}
		echo("{}\n],\n");
		
		echo('"blacklist": ['."\n");
		foreach ($db->query("SELECT * from bot_blacklist_$magicKey") as $row) {
			echo('{"userid":"'.$row['userid'].'", ');
			echo('"reason":'.json_encode($row['reason']).'},'."\n");
		}
		echo("{}\n],\n");

		echo('"pkg_b_data": ['."\n");
		foreach ($db->query("SELECT * from pkg_b_data where magic_key='$magicKey'") as $row) {
			echo('{"anti_idle":"'.$row['anti_idle'].'", ');

			echo('"last_fm_e":"'.$row['last_fm_e'].'", ');
			echo('"last_fm_t":"'.$row['last_fm_t'].'", ');
			echo('"wikipedia_e":"'.$row['wikipedia_e'].'", ');
			echo('"wikipedia_t":"'.$row['wikipedia_t'].'", ');
			echo('"weather_e":"'.$row['weather_e'].'", ');
			echo('"weather_t":"'.$row['weather_t'].'", ');
			echo('"user_lookup_e":"'.$row['user_lookup_e'].'", ');
			echo('"user_lookup_t":"'.$row['user_lookup_t'].'", ');
			echo('"dice_6_e":"'.$row['dice_6_e'].'", ');
			echo('"dice_6_t":"'.$row['dice_6_t'].'", ');
			echo('"dice_20_e":"'.$row['dice_20_e'].'", ');
			echo('"dice_20_t":"'.$row['dice_20_t'].'", ');
			echo('"8ball_e":"'.$row['8ball_e'].'", ');
			echo('"8ball_t":"'.$row['8ball_t'].'", ');
			echo('"ai_w_msg":"'.$row['ai_w_msg'].'", ');
			echo('"ai_msg":"'.$row['ai_msg'].'", ');
			echo('"ai_w_msg_t":"'.$row['ai_w_msg_t'].'", ');
			echo('"ai_msg_t":"'.$row['ai_msg_t'].'", ');

			echo('"8ball_1":"'.$row['8ball_1'].'", ');
			echo('"8ball_2":"'.$row['8ball_2'].'", ');
			echo('"8ball_3":"'.$row['8ball_3'].'", ');
			echo('"8ball_4":"'.$row['8ball_4'].'", ');
			echo('"8ball_5":"'.$row['8ball_5'].'", ');
			echo('"8ball_6":"'.$row['8ball_6'].'", ');
			echo('"8ball_7":"'.$row['8ball_7'].'", ');
			echo('"8ball_8":"'.$row['8ball_8'].'", ');
			echo('"8ball_9":"'.$row['8ball_9'].'", ');
			echo('"8ball_10":"'.$row['8ball_10'].'", ');
			echo('"8ball_11":"'.$row['8ball_11'].'", ');
			echo('"8ball_12":"'.$row['8ball_12'].'", ');
			echo('"8ball_13":"'.$row['8ball_13'].'", ');
			echo('"8ball_14":"'.$row['8ball_14'].'", ');
			echo('"8ball_15":"'.$row['8ball_15'].'", ');
			echo('"8ball_16":"'.$row['8ball_16'].'", ');
			echo('"8ball_17":"'.$row['8ball_17'].'", ');
			echo('"8ball_18":"'.$row['8ball_18'].'", ');
			echo('"8ball_19":"'.$row['8ball_19'].'", ');
			echo('"8ball_20":"'.$row['8ball_20'].'", ');
			echo('"horoscope_e":"'.$row['horoscope_e'].'"},'."\n");
		}

		echo("{}\n]\n");
		
		echo("}\n");
	}else{
		echo("Parse Data Failed");
	} 
?>
