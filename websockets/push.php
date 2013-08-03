<?php
#file_put_contents('ping', 'touch');
$botid = addslashes($_POST['bot_userid']);
$magickey = addslashes($_POST['magic_key']);
$command = addslashes($_POST['command']);
$ads = array();
$triggers = array();
$triggerUnique = array();
$blacklist = array();
$blacklistUniqueUserid = "";
$blacklistUniqueReason = "";
$adsUniqueMsg = "";
$adsUniqueDelay = 0;
$log = "";
$events = array();
$eventsUnique = array();
$acls = array();
$aclsUnique = array();

require_once('../config/db.conf');

function update_ads() {
	global $db, $botid, $magickey, $command, $ads, $triggers, $triggerUnique, $adsUniqueMsg, $adsUniqueDelay, $log;
		$log .= "\nInside update_ads";
		foreach($_POST as $key => $value){
			//Traverse $_POST looking for answers
			if (preg_match('/^admsg/', $key)) {
				if(preg_match('/^admsg(\d+)/', $key, $match)){
					if(strcmp('', $value)) { $ads[$match[1]]['msg'] = $value; }
				}else{
					if(strcmp('', $value)) { $adsUniqueMsg = $value; }
				}
			}elseif (preg_match('/^addelay/', $key)) {
				if(preg_match('/^addelay(\d+)/', $key, $match)){
					if(strcmp('', $value)) { $ads[$match[1]]['delay'] = $value; }
				}else{
					if(strcmp('', $value)) { $adsUniqueDelay = $value; }
				}
			}
		}
		array_push($ads, array('msg' => $adsUniqueMsg, 'delay' => $adsUniqueDelay));
		$db->exec("TRUNCATE TABLE bot_adverts_".$magickey);
		foreach ($ads as $key){
		if (strcmp($key['msg'],'')) { 
				//This should be alot more nimble then this, but wtf?
				//strcmp seemes to be inversed to what I would expect
				//addslashes is too extreme
				preg_replace('"', '\"', $key['msg']);
				preg_replace('"', '\"', $key['delay']);				
			$db->exec("INSERT INTO bot_adverts_".$magickey." SET message=".$db->quote($key['msg']).", delay=".$db->quote($key['delay']));
			$log .= "INSERT INTO bot_adverts_".$magickey." SET message=".$db->quote($key['msg']).", delay=".$db->quote($key['delay'])."\n";

			}
		}
}

function update_blacklist() {
	global $db, $botid, $magickey, $command, $blacklist, $blacklistUniqueUserid, $blacklistUniqueReason, $log;
		$log .= "\nInside update_blacklist";
		foreach($_POST as $key => $value){
			//Traverse $_POST looking for answers
			if (preg_match('/^userid/', $key)) {
				if(preg_match('/^userid(\d+)/', $key, $match)){
					if(strcmp('', $value)) { $blacklist[$match[1]]['userid'] = $value; }
				}else{
					if(strcmp('', $value)) { $blacklistUniqueUserid = $value; }
				}
			}elseif (preg_match('/^reason/', $key)) {
				if(preg_match('/^reason(\d+)/', $key, $match)){
					if(strcmp('', $value)) { $blacklist[$match[1]]['reason'] = $value; }
				}else{
					if(strcmp('', $value)) { $blacklistUniqueReason = $value; }
				}
			}
		}
		array_push($blacklist, array('userid' => $blacklistUniqueUserid, 'reason' => $blacklistUniqueReason));
		$db->exec("TRUNCATE TABLE bot_blacklist_".$magickey);
		foreach ($blacklist as $key){
		if (strcmp($key['userid'],'')) { 
				//This should be alot more nimble then this, but wtf?
				//strcmp seemes to be inversed to what I would expect
				preg_replace('"', '\"', $key['userid']);
				preg_replace('"', '\"', $key['reason']);
			$db->exec("INSERT INTO bot_blacklist_".$magickey." SET userid=".$db->quote($key['userid']).", reason=".$db->quote($key['reason']));
			$log .= "INSERT INTO bot_blacklist_".$magickey." SET userid=".$db->quote($key['userid']).", reason=".$db->quote($key['reason'])."\n";

			}
		}
}

function update_triggers() {
	global $db, $botid, $magickey, $command, $ads, $triggers, $triggerUnique, $adsUniqueMsg, $adsUniqueDelay, $log;
	$log .= "\nInside update_triggers\n";
		foreach($_POST as $key => $value){
			switch(true){
				case preg_match('/^useSaying/', $key):
					$log .= "UseSaying Found\n";
					if(preg_match('/^useSaying(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $triggers[$match[1]]['use_saying_switch'] = 1; }
					}else{
						if(strcmp('', $value)) { $triggerUnique['use_saying_switch'] = 1; }
					}
					break;
				case preg_match('/^useName/', $key):
					$log .= "UseName Found\n";
					if(preg_match('/^useName(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $triggers[$match[1]]['use_name_switch'] = 1; }
					}else{
						if(strcmp('', $value)) { $triggerUnique['use_name_switch'] = 1; }
					}
					break;
				case preg_match('/^useTrigger/', $key):
					$log .= "UseTrigger Found\n";
					if(preg_match('/^useTrigger(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $triggers[$match[1]]['use_trigger_switch'] = 1; }
					}else{
						if(strcmp('', $value)) { $triggerUnique['use_trigger_switch'] = 1; }
					}
					break;
				case preg_match('/^useStrict/', $key):
					$log .= "useStrict Found\n";
					if(preg_match('/^useStrict(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $triggers[$match[1]]['use_strict_matching'] = 1; }
					}else{
						if(strcmp('', $value)) { $triggerUnique['use_strict_matching'] = 1; }
					}
					break;
				case preg_match('/^triggerPhrase/', $key):
					$log .= "triggerPhrase Found\n";
					if(preg_match('/^triggerPhrase(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $triggers[$match[1]]['trigger_phrase'] = htmlentities($value); }
					}else{
						if(strcmp('', $value)) { $triggerUnique['trigger_phrase'] = htmlentities($value); }
					}
					break;
				case preg_match('/^preNameResponse/', $key):
					$log .= "pre response Found\n";
					if(preg_match('/^preNameResponse(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $triggers[$match[1]]['pre_name_response'] = htmlentities($value); }
					}else{
						if(strcmp('', $value)) { $triggerUnique['pre_name_response'] = htmlentities($value); }
					}
					break;
				case preg_match('/^postNameResponse/', $key):
					$log .= "post response Found\n";
					if(preg_match('/^postNameResponse(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $triggers[$match[1]]['post_name_response'] = htmlentities($value); }
					}else{
						if(strcmp('', $value)) { $triggerUnique['post_name_response'] = htmlentities($value); }
					}
					break;
				case preg_match('/^preCommandFail/', $key):
					$log .= "Pre command_fail Found\n";
					if(preg_match('/^preCommandFail(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $triggers[$match[1]]['pre_command_fail'] = htmlentities($value); }
					}else{
						if(strcmp('', $value)) { $triggerUnique['pre_command_fail'] = htmlentities($value); }
					}
					break;
				case preg_match('/^postCommandFail/', $key):
					$log .= "Post command_fail Found\n";
					if(preg_match('/^postCommandFail(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $triggers[$match[1]]['post_command_fail'] = htmlentities($value); }
					}else{
						if(strcmp('', $value)) { $triggerUnique['post_command_fail'] = htmlentities($value); }
					}
					break;
				case preg_match('/^action/', $key):
					$log .= "action Found\n";
					if(preg_match('/^action(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $triggers[$match[1]]['action'] = $value; }
					}else{
						if(strcmp('', $value)) { $triggerUnique['action'] = $value; }
					}
					break;
				case preg_match('/^access_level/', $key):
					$log .= "access_level Found\n";
					if(preg_match('/^access_level(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $triggers[$match[1]]['access_level'] = $value; }
					}else{
						if(strcmp('', $value)) { $triggerUnique['access_level'] = $value; }
					}
					break;
				case preg_match('/^commandDescription/', $key):
					$log .= "command_description Found\n";
					if(preg_match('/^commandDescription(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $triggers[$match[1]]['command_description'] = $value; }
					}else{
						if(strcmp('', $value)) { $triggerUnique['command_description'] = $value; }
					}
					break;

			}
}
$log .= '$_POST Scan done'."\n";
//Hardwork done, add the floater
		if (strcmp($triggerUnique['trigger_phrase'],'')) { array_push($triggers, $triggerUnique); }
//Phase II
		$log .= "TRUNCATE TABLE bot_triggers_".$magickey."\n";
		$db->exec("TRUNCATE TABLE bot_triggers_".$magickey);
		foreach ($triggers as $key){
		if (strcmp($key['trigger_phrase'],'')) { 
				//This should be alot more nimble then this, but wtf?
				//strcmp seemes to be inversed to what I would expect
			if(!isset($key['use_trigger_phrase'])) { $key['use_trigger_phrase'] = 0; }
			if(!isset($key['use_name_switch'])) { $key['use_name_swtich'] = 0; }
			if(!isset($key['use_saying_switch'])) { $key['use_saying_switch'] = 0; }
			if(!isset($key['use_strict_matching'])) { $key['use_strict_matching'] = 0; }

			$log .= "INSERT INTO bot_triggers_".$magickey." SET use_name_switch=".$db->quote($key['use_name_switch']).", use_saying_switch=".$db->quote($key['use_saying_switch']).", use_trigger_switch=".$db->quote($key['use_trigger_switch']).", use_strict_matching=".$db->quote($key['use_strict_matching']).", trigger_phrase=".$db->quote($key['trigger_phrase']).", pre_name_response=".$db->quote($key['pre_name_response']).", post_name_response=".$db->quote($key['post_name_response']).", pre_command_fail=".$db->quote($key['pre_command_fail']).", post_command_fail=".$db->quote($key['post_command_fail']).", access_level=".$db->quote(addslashes($key['access_level'])).", action=".$db->quote($key['action']).", command_description=".$db->quote(strip_tags($key['command_description'],'<br><font><a><img>'))."\n"; 
			$db->exec("INSERT INTO bot_triggers_".$magickey." SET use_name_switch=".$db->quote($key['use_name_switch']).", use_saying_switch=".$db->quote($key['use_saying_switch']).", use_trigger_switch=".$db->quote($key['use_trigger_switch']).", use_strict_matching=".$db->quote($key['use_strict_matching']).", trigger_phrase=".$db->quote($key['trigger_phrase']).", pre_name_response=".$db->quote($key['pre_name_response']).", post_name_response=".$db->quote($key['post_name_response']).", pre_command_fail=".$db->quote($key['pre_command_fail']).",  post_command_fail=".$db->quote($key['post_command_fail']).", access_level=".$db->quote(addslashes($key['access_level'])).", action=".$db->quote($key['action']).", command_description=".$db->quote(htmlentities(strip_tags($key['command_description'],'<br><font><a><img><p>')))); 
			 }
		}
}

function update_acl() {
	global $db, $botid, $magickey, $command, $acls, $aclsUnique, $log;
	$log .= "\nInside update_events\n";
		foreach($_POST as $key => $value){
			switch(true){
				case preg_match('/^userid/', $key):
					$log .= "userid Found\n";
					if(preg_match('/^userid(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $acls[$match[1]]['userid'] = $value; }
					}else{
						if(strcmp('', $value)) { $aclsUnique['userid'] = $value; }
					}
					break;
				case preg_match('/^comment/', $key):
					$log .= "Comment Found\n";
					if(preg_match('/^comment(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $acls[$match[1]]['comment'] = $value; }
					}else{
						if(strcmp('', $value)) { $aclsUnique['comment'] = $value; }
					}
					break;

				case preg_match('/^level/', $key):
					$log .= "level Found\n";
					if(preg_match('/^level(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $acls[$match[1]]['level'] = $value; }
					}else{
						if(strcmp('', $value)) { $aclsUnique['level'] = $value; }
					}
					break;
			}
}
$log .= '$_POST Scan done'."\n";
//Hardwork done, add the floater
//one or the other or both
		if(strcmp($aclsUnique['userid'], '')) { array_push($acls, $aclsUnique); }
//Phase II
		$log .= "TRUNCATE TABLE bot_acls_".$magickey."\n";
		$db->exec("TRUNCATE TABLE bot_acls_".$magickey);
		foreach ($acls as $key){
		if(strcmp($key['userid'], '')) { 
				//This should be alot more nimble then this, but wtf?
				//strcmp seemes to be inversed to what I would expect
			// preg_replace('"', '\\"', $key['userid']);
			$log .= "INSERT INTO bot_acls_".$magickey." SET userid=".$db->quote($key['userid']).", access_level=".$db->quote($key['level']).", comment=".$db->quote($key['comment'])."\n"; 
			$db->exec("INSERT INTO bot_acls_".$magickey." SET userid=".$db->quote($key['userid']).", access_level=".$db->quote($key['level']).", comment=".$db->quote($key['comment']));  
			}
		}
}

function update_events() {
	global $db, $botid, $magickey, $command, $events, $eventsUnique, $log;
	$log .= "\nInside update_events\n";
		foreach($_POST as $key => $value){
			switch(true){
				case preg_match('/^event/', $key):
					$log .= "event Found\n";
					if(preg_match('/^event(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $events[$match[1]]['event'] = $value; }
					}else{
						if(strcmp('', $value)) { $eventsUnique['event'] = $value; }
					}
					break;
				case preg_match('/^delivery_method/', $key):
					$log .= "delivery_method Found\n";
					if(preg_match('/^delivery_method(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $events[$match[1]]['delivery_method'] = $value; }
					}else{
						if(strcmp('', $value)) { $eventsUnique['delivery_method'] = $value; }
					}
					break;
				case preg_match('/^pre_text/', $key):
					$log .= "pre_text Found\n";
					if(preg_match('/^pre_text(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $events[$match[1]]['pre_text'] = $value; }
					}else{
						if(strcmp('', $value)) { $eventsUnique['pre_text'] = $value; }
					}
					break;
				case preg_match('/^post_text/', $key):
					$log .= "post_text Found\n";
					if(preg_match('/^post_text(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $events[$match[1]]['post_text'] = $value; }
					}else{
						if(strcmp('', $value)) { $eventsUnique['post_text'] = $value; }
					}
					break;
				case preg_match('/^include_name/', $key):
					$log .= "include_name Found\n";
					if(preg_match('/^include_name(\d+)/', $key, $match)){
						if(strcmp('', $value)) { $log .= "Number Found: ".$match[1]."\n"; $events[$match[1]]['include_name'] = $value; }
					}else{
						if(strcmp('', $value)) { $eventsUnique['include_name'] = $value; }
					}
					break;
			}
}
$log .= '$_POST Scan done'."\n";
//Hardwork done, add the floater
//one or the other or both
		if ((strcmp($eventsUnique['pre_text'],'') || strcmp($eventsUnique['post_text'],'')) || (strcmp($eventsUnique['pre_text'],'') && strcmp($eventsUnique['post_text'],''))) { array_push($events, $eventsUnique); }
//Phase II
		$log .= "TRUNCATE TABLE bot_events_".$magickey."\n";
		$db->exec("TRUNCATE TABLE bot_events_".$magickey);
		foreach ($events as $key){
		// one or the other or both have something.....
		if ((strcmp($key['pre_text'],'') || strcmp($key['post_text'],''))||(strcmp($key['pre_text'],'') && strcmp($key['post_text'],''))) { 
				//This should be alot more nimble then this, but wtf?
				//strcmp seemes to be inversed to what I would expect
			if(!isset($key['include_name'])) { $key['include_name'] = 0; }
			preg_replace('"', '\"', $key['pre_text']);
			preg_replace('"', '\"', $key['post_text']);
			$log .= "INSERT INTO bot_events_".$magickey." SET event=".$db->quote($key['event']).", delivery_method=".$db->quote($key['delivery_method']).", pre_text=".$db->quote($key['pre_text']).", post_text=".$db->quote($key['post_text']).", include_name=".$db->quote($key['include_name'])."\n"; 
			$db->exec("INSERT INTO bot_events_".$magickey." SET event=".$db->quote($key['event']).", delivery_method=".$db->quote($key['delivery_method']).", pre_text=".$db->quote($key['pre_text']).", post_text=".$db->quote($key['post_text']).", include_name=".$db->quote($key['include_name']));  
			}
		}
}


function update_basics() {
	global $db, $botid, $magickey, $command, $ads, $triggers, $triggerUnique, $adsUniqueMsg, $adsUniqueDelay, $log;

$trigger = $db->quote($_POST['trigger']);
$bot_roomid = $db->quote($_POST['bot_roomid']);
$bot_authid = $db->quote($_POST['bot_authid']);
$bot_userid = $db->quote($_POST['bot_userid']);
$bot_ownerid = $db->quote($_POST['bot_ownerid']);
// UPDATE table_name SET column1=value, column2=value2,... WHERE some_column=some_value 
	$log .= "\nInside update_basics";
	$log .= "\nSQL:\n".'UPDATE users set bot_userid='.$bot_userid.', bot_authid='.$bot_authid.', bot_roomid='.$bot_roomid.', owner_userid='.$bot_ownerid.', command_trigger='.$trigger.' WHERE magic_key="'.$magickey.'"'."\n";

	$db->exec('UPDATE users set bot_userid='.$bot_userid.', bot_authid='.$bot_authid.', bot_roomid='.$bot_roomid.', owner_userid='.$bot_ownerid.', command_trigger='.$trigger.' WHERE magic_key="'.$magickey.'"');

}

function update_botstats() {
	global $db, $botid, $magickey, $command, $ads, $triggers, $triggerUnique, $adsUniqueMsg, $adsUniqueDelay, $log, $botstats;
	
    $botstats['bg_color'] = $db->quote($_POST['roomDescrBgColor']);
	$botstats['theme']=$db->quote($_POST['statsTheme']);
	$botstats['room_descr']=$db->quote(htmlentities(strip_tags($_POST['roomDescrText'],'<font><br><p><a><img><ul><li>')));
	$db->exec('UPDATE users set room_description='.$botstats['room_descr'].', stats_bg_color='.$botstats['bg_color'].', stats_color_theme='.$botstats['theme'].' WHERE magic_key="'.$magickey.'"');

}

function update_misc() {
	global $db, $botid, $magickey, $command, $ads, $triggers, $triggerUnique, $adsUniqueMsg, $adsUniqueDelay, $log, $botstats;
	$ss = "'0'";
	$sq = "'0'";
	$md = "'0'";
	$st = "'0'";
	$sadj = "'0'";
	$alonedj = "'0'";
	$arequeue = "'0'";

	if(array_key_exists('startSlide', $_POST)){ 		if($_POST['startSlide'] == 1) { $ss = "'1'"; }}
	if(array_key_exists('startQueue', $_POST)){ 		if($_POST['startQueue'] == 1) { $sq = "'1'"; }}
	if(array_key_exists('modsToLvl1', $_POST)){ 		if($_POST['modsToLvl1'] == 1) { $md = "'1'"; }}
	if(array_key_exists('startStats', $_POST)){ 		if($_POST['startStats'] == 1) { $st = "'1'"; }}
	if(array_key_exists('startAutoDj', $_POST)){ 		if($_POST['startAutoDj'] == 1) { $sadj = "'1'"; }}
	if(array_key_exists('switchAloneDj', $_POST)){ 		if($_POST['switchAloneDj'] == 1) { $alonedj = "'1'"; }}
	if(array_key_exists('switchAutoRequeue', $_POST)){ 	if($_POST['switchAutoRequeue'] == 1) { $arequeue = "'1'"; }}
	
	#$db->exec("UPDATE users set start_slide=$db->quote($ss), start_queue=$db->quote($sq), mods_to_lvl1=$db->quote($md), start_stats=$db->quote($st), start_autodj=$db->quote($sadj) where magic_key=$db->quote($magickey)");
	$db->exec('UPDATE users set start_slide='.$ss.', start_queue='.$sq.', mods_to_lvl1='.$md.', start_stats='.$st.', start_autodj='.$sadj.', switch_alonedj='.$alonedj.', switch_autorequeue='.$arequeue.' WHERE magic_key="'.$magickey.'"');

}

function update_pkgb() {
	global $db, $botid, $magickey, $command, $ads, $triggers, $triggerUnique, $adsUniqueMsg, $adsUniqueDelay, $log, $botstats;
	$last_fm_e = "'0'";
	$wikipedia_e = "'0'";
	$weather_e = "'0'";
	$user_lookup_e = "'0'";
	$dice_6_e = "'0'";
	$dice_20_e = "'0'";
	$_8ball_e = "'0'";
	$anti_idle = "'0'";
	$horoscope_e = "'0'";

	if(array_key_exists('last_fm_e', $_POST)){ 		if($_POST['last_fm_e'] == 1) { $last_fm_e = "'1'"; }}
	if(array_key_exists('wikipedia_e', $_POST)){ 	if($_POST['wikipedia_e'] == 1) { $wikipedia_e = "'1'"; }}
	if(array_key_exists('weather_e', $_POST)){ 		if($_POST['weather_e'] == 1) { $weather_e = "'1'"; }}
	if(array_key_exists('user_lookup_e', $_POST)){ 	if($_POST['user_lookup_e'] == 1) { $user_lookup_e = "'1'"; }}
	if(array_key_exists('dice_6_e', $_POST)){ 		if($_POST['dice_6_e'] == 1) { $dice_6_e = "'1'"; }}
	if(array_key_exists('dice_20_e', $_POST)){ 		if($_POST['dice_20_e'] == 1) { $dice_20_e = "'1'"; }}
	if(array_key_exists('8ball_e', $_POST)){ 		if($_POST['8ball_e'] == 1) { $_8ball_e = "'1'"; }}
	if(array_key_exists('anti_idle', $_POST)){ 		if($_POST['anti_idle'] == 1) { $anti_idle = "'1'"; }}
	if(array_key_exists('horoscope_e', $_POST)){ 	if($_POST['horoscope_e'] == 1) { $horoscope_e = "'1'"; }}

	$last_fm_t 		= $db->quote($_POST['last_fm_t']);
	$wikipedia_t 	= $db->quote($_POST['wikipedia_t']);
	$weather_t		= $db->quote($_POST['weather_t']);
	$user_lookup_t	= $db->quote($_POST['user_lookup_t']);
	$dice_6_t		= $db->quote($_POST['dice_6_t']);
	$dice_20_t		= $db->quote($_POST['dice_20_t']);
	$_8ball_t		= $db->quote($_POST['8ball_t']);
	$ai_w_msg		= $db->quote($_POST['ai_w_msg']);
	$ai_msg 		= $db->quote($_POST['ai_msg']);
	$ai_w_msg_t		= $db->quote($_POST['ai_w_msg_t']);
	$ai_msg_t 		= $db->quote($_POST['ai_msg_t']);

	$_8ball_1		= $db->quote($_POST['8ball_1']);
	$_8ball_2		= $db->quote($_POST['8ball_2']);
	$_8ball_3		= $db->quote($_POST['8ball_3']);
	$_8ball_4		= $db->quote($_POST['8ball_4']);
	$_8ball_5		= $db->quote($_POST['8ball_5']);
	$_8ball_6		= $db->quote($_POST['8ball_6']);
	$_8ball_7		= $db->quote($_POST['8ball_7']);
	$_8ball_8		= $db->quote($_POST['8ball_8']);
	$_8ball_9		= $db->quote($_POST['8ball_9']);
	$_8ball_10		= $db->quote($_POST['8ball_10']);
	$_8ball_11		= $db->quote($_POST['8ball_11']);
	$_8ball_12		= $db->quote($_POST['8ball_12']);
	$_8ball_13		= $db->quote($_POST['8ball_13']);
	$_8ball_14		= $db->quote($_POST['8ball_14']);
	$_8ball_15		= $db->quote($_POST['8ball_15']);
	$_8ball_16		= $db->quote($_POST['8ball_16']);
	$_8ball_17		= $db->quote($_POST['8ball_17']);
	$_8ball_18		= $db->quote($_POST['8ball_18']);
	$_8ball_19		= $db->quote($_POST['8ball_19']);
	$_8ball_20		= $db->quote($_POST['8ball_20']);

	$magickey 		= $db->quote($magickey);

	$db->exec("UPDATE pkg_b_data SET last_fm_e = $last_fm_e, last_fm_t = $last_fm_t, wikipedia_e = $wikipedia_e, wikipedia_t = $wikipedia_t, weather_e = $weather_e, weather_t = $weather_t, user_lookup_e = $user_lookup_e, user_lookup_t = $user_lookup_t, dice_6_e = $dice_6_e, dice_6_t = $dice_6_t, dice_20_e = $dice_20_e, dice_20_t = $dice_20_t, 8ball_t= $_8ball_t, 8ball_e = $_8ball_e, anti_idle = $anti_idle, ai_w_msg = $ai_w_msg, ai_msg = $ai_msg, ai_w_msg_t = $ai_w_msg_t, ai_msg_t = $ai_msg_t, horoscope_e = $horoscope_e, 8ball_1 = $_8ball_1, 8ball_2 = $_8ball_2, 8ball_3 = $_8ball_3, 8ball_4 = $_8ball_4, 8ball_5 = $_8ball_5, 8ball_6 = $_8ball_6, 8ball_7 = $_8ball_7, 8ball_8 = $_8ball_8, 8ball_9 = $_8ball_9, 8ball_10 = $_8ball_10, 8ball_11 = $_8ball_11, 8ball_12 = $_8ball_12, 8ball_13 = $_8ball_13, 8ball_14 = $_8ball_14, 8ball_15 = $_8ball_15, 8ball_16 = $_8ball_16, 8ball_17 = $_8ball_17, 8ball_18 = $_8ball_18, 8ball_19 = $_8ball_19, 8ball_20 = $_8ball_20 WHERE magic_key = $magickey");
}

switch ($command) {
	case 'update_ads':
			$log .= "\nupdate_ads running\n";
			update_ads();
			break;
	case 'update_basics':
			$log .= "\nupdate_basics running\n";
			update_basics();
			update_misc();
			break;
	case 'update_triggers':
			$log .= "\nupdate_triggers running\n";
			update_triggers();
			break;
	case 'update_blacklist':
			$log .= "\nupdate_blacklist running\n";
			update_blacklist();
			break;
	case 'update_events':
			$log .= "\nupdate_events running\n";
			update_events();
			break;
	case 'update_acl':
			$log .= "\nupdate_acl running\n";
			update_acl();
			break;
	case 'update_botstats':
			$log .= "\update_botstats running\n";
			update_botstats();
			break;
	case 'update_pkgb':
			$log .= "\update_pkgb running\n";
			update_pkgb();
			break;
	case 'update_misc':
			update_misc();
			break;
	default:
		# code...
		break;
}

array_values($triggers);
array_values($ads);
array_values($blacklist);
file_put_contents('log', $log ."\n_POST\n".var_export($_POST, $return = true)."\nAds\n".var_export($ads, $return = true) ."\nTriggers\n".var_export($triggers, $return = true)."\nBlacklist\n".var_export($blacklist, $return = true)."\nEvents\n".var_export($events, $return = true)."\nACLS\n".var_export($acls, $return = true));

?>
