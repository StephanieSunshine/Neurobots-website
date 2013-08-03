<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>neuroBots</title>
<meta name="keywords" content="turntable, turntable.fm, bots, turntable bots, turntable.fm bots, " />
<meta name="description" content="Neurobots is a platform to make using bots with turntable easy!" />

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-38719169-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>


	<link href="/private.css" type="text/css" rel="stylesheet" /> 
	<script type="text/javascript" src="/js/jquery.min.js"></script> 
	<script type="text/javascript" src="/js/jquery.scrollTo-min.js"></script> 
	<script type="text/javascript" src="/js/jquery.localscroll-min.js"></script> 
	<script type="text/javascript" src="/js/init.js"></script> 
  <script type="text/javascript" src="/js/jquery.websocket-0.0.1.js"></script>
    <link rel="stylesheet" href="/css/slimbox2.css" type="text/css" media="screen" /> 
    <script type="text/JavaScript" src="/js/slimbox2.js"></script> 
    <script type="text/JavaScript" src="/js/verimail.js"></script>
    <script type="text/JavaScript" src="/js/base64_decode.js"></script>
    <script type="text/JavaScript" src="/js/progress.js"></script>
    <script type="text/JavaScript" src="/js/json_parse.js"></script>
    <script type="text/JavaScript" src="/js/jquery.form.js"></script>
     <script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
      <script type="text/JavaScript" src="/js/jquery.popupWindow.js"></script>
      <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css' />
<link rel="stylesheet" href="/juithemes/dark-hive/jquery-ui.css" />
<link rel="stylesheet" href="/juithemes/dark-hive/jquery-ui.min.css" />
<link rel="stylesheet" href="/juithemes/dark-hive/jquery.ui.theme.css" />

<style>
textarea {
    resize: none;
}
</style>
    <script type="text/JavaScript"> 

function popupDialog() {

$(function() {

$( "#dialog-message" ).dialog({
modal: true,
buttons: {
Continue: function() {
$( this ).dialog( "close" );
}
}
});
});

var button = $(".ui-dialog-buttonpane button:contains('Continue')");
    $(button).button("disable");
}

        // wait for the DOM to be loaded 
        $(document).ready(function() { 
	
	var ajaxOptions = {
		beforeSubmit: function() { popupDialog(); },
		success: function() { refreshAJAX(); }
		};
		
		refreshAJAX();
            // bind 'myForm' and provide a simple callback function 
            $('#ads').ajaxForm(ajaxOptions);
            $('#triggers').ajaxForm(ajaxOptions); 
            $('#basics').ajaxForm(ajaxOptions); 
            $('#blacklist').ajaxForm(ajaxOptions);
            $('#events').ajaxForm(ajaxOptions);
            $('#acl').ajaxForm(ajaxOptions);  
            $('#botstats').ajaxForm(ajaxOptions);
	    $('#misc').ajaxForm(ajaxOptions);
	    $('#sayingsButton').popupWindow({
		windowURL: 'http://'+http_host+'/console/update_sayings.html?magic_key='+magicKey,
		height: 600,
		width: 600    });
	}); 


    </script> 

    <script type="text/JavaScript">

<?php require_once('../config/db.conf'); ?>

var http_host = <?php echo '"'.$_SERVER['HTTP_HOST'].'";'; ?>

<?php foreach ($db->query('SELECT bot_userid FROM users WHERE login_hash = "'.addslashes($_COOKIE['PHPSESSID']).'" LIMIT 1') as $row) echo 'var botid = "'.$row['bot_userid'].'";'."\n"; ?>

<?php foreach ($db->query('SELECT magic_key FROM users WHERE login_hash = "'.addslashes($_COOKIE['PHPSESSID']).'" LIMIT 1') as $row) echo 'var magicKey = "'.$row['magic_key'].'";'."\n"; ?>

<?php 
foreach ($db->query('SELECT controller FROM users WHERE login_hash = "'.addslashes($_COOKIE['PHPSESSID']).'" LIMIT 1') as $row) 
	if (($row['controller'] == "" )||( $row['controller'] == "0")){
		if(preg_match('/dev/',$_SERVER['HTTP_HOST'])){
			echo 'var controller = "a";'."\n";}
			else{
			echo 'var controller = "'.chop(`cat ~/app-root/data/conf/controller_number`).'";'."\n";
			}
		}else{
		echo 'var controller = "'.$row['controller'].'";'."\n";
		
		} 
?>


var accordianOpts = {
collapsible: true,
active: false
};

var isBotRunningVar = false;
var isControllerConnectedVar = false;
var isBotConnectedVar = false;
var socketController;
var socketBot;
var botPort = -1;
var botKeepAlive;
var dataAJAX;

function getBotId() {
    return botid;
}

function getMagicKey() {
    return magicKey;
}

function logout() {
sessid = document.cookie.match(/PHPSESSID=[^;]+/);
var httpRequest;
httpRequest = new XMLHttpRequest();
httpRequest.open('GET', 'http://'+http_host+'/websockets/logout.php?'+sessid, false);
httpRequest.send(null);
window.location.href = "http://"+http_host+"/";
}

function getAuthId() {
    return (<?php echo '"'; foreach ($db->query('SELECT bot_authid FROM users WHERE login_hash = "'.addslashes($_COOKIE['PHPSESSID']).'" LIMIT 1') as $row) { echo $row['bot_authid']; } echo '"'; ?>);
}

function getTrigger() {
    return (<?php echo '"'; foreach ($db->query('SELECT command_trigger FROM users WHERE login_hash = "'.addslashes($_COOKIE['PHPSESSID']).'" LIMIT 1') as $row) { echo $row['command_trigger']; } echo '"'; ?>);
}

function getOwnerId() {
    return (<?php echo '"'; foreach ($db->query('SELECT owner_userid FROM users WHERE login_hash = "'.addslashes($_COOKIE['PHPSESSID']).'" LIMIT 1') as $row) { echo $row['owner_userid']; } echo '"'; ?>);
}

function getRoomId() {
    return (<?php echo '"'; foreach ($db->query('SELECT bot_roomid FROM users WHERE login_hash = "'.addslashes($_COOKIE['PHPSESSID']).'" LIMIT 1') as $row) { echo $row['bot_roomid']; } echo '"'; ?>);
}

function getId() {
    return (<?php echo '"'; foreach ($db->query('SELECT id FROM users WHERE login_hash = "'.addslashes($_COOKIE['PHPSESSID']).'" LIMIT 1') as $row) { echo $row['id']; } echo '"'; ?>);
}

function unlockBasics() { 
  $('#basics_botuserid').prop('readonly', false); 
  $('#basics_botauthid').prop('readonly', false); 
  $('#basics_botroomid').prop('readonly', false); 
  $('#basics_botownerid').prop('readonly', false); 
  $('#basics_commandtrigger').prop('readonly', false); 
  }

function isBotConnected(flag) {
      if (typeof flag === 'undefined') {
      return isBotConnectedVar;
      }else{
      isBotConnectedVar = flag;
      }  
      if (isBotConnectedVar == true){ 
		$('#progress_bar .ui-progress').animateProgress(100); 
		// Let the databse know which controller were on
		$.get('/websockets/updatecontroller.php', { magic_key: magicKey, controller: controller } );
		}
}

function isBotRunning(flag) {
      if (typeof flag === 'undefined') {
      return isBotRunningVar;
      }else{
      isBotRunningVar = flag;
      }
      if (isBotRunningVar == true){ $('#progress_bar .ui-progress').animateProgress(66); }

}

function isControllerConnected(flag) {
      if (typeof flag === 'undefined') {
      return isControllerConnectedVar; 
      }else{
      isControllerConnectedVar = flag;
      }
      if (isControllerConnectedVar == true && isBotRunning() == false){ 
		$('#progress_bar .ui-progress').animateProgress(33); 
		$.get('/websockets/updatecontroller.php', { magic_key: magicKey, controller: "0" } );
	}

}

function connectSocketBot(port){
socketBot  = WebSocket("ws://www."+controller+".neurobots.net:8000/bot"+port);
socketBot.onopen = function () {
  isBotConnected(true);
}
socketBot.onclose = function () {
  isBotConnected(false);
  $('#progress_bar .ui-progress').animateProgress(50);
}

  socketBot.onmessage = function(msg) { 
    if( msg.data.substring(0,4) === 'Log|') { 
        $( "div.logwindow" ).append('<p style="word-wrap: break-word;">'+ base64_decode(msg.data.substring(4)) + '</p>');
        var objDiv = document.getElementById("logwindow");
          objDiv.scrollTop = objDiv.scrollHeight;
      }else if(msg.data.substring(0,7) === 'Uptime|'){
         $( "p.uptime" ).html("Uptime: "+String(((parseInt(msg.data.substring(7))/60)/60).toFixed(1))+" hours");
      }
}

}

function connectSocketController() {
if (isControllerConnected() == false) {
socketController = WebSocket("ws://www."+controller+".neurobots.net:8000/controller");

  socketController.onopen = function () {
    socketController.send(botid+"|"+magicKey);
    isControllerConnected(true)
  } 

  socketController.onclose = function () {
    isControllerConnected(false);
    if(isBotConnected() == false){$('#progress_bar .ui-progress').animateProgress(10);}
  }

  socketController.onmessage = function(msg) {  
                 //message('<p class="message">Received: '+msg.data);  
  switch(msg.data.substring(0,1)){
              case '@': 
                isBotRunning(true); 
                botPort = msg.data.slice(3); 
                connectSocketBot(botPort); 
                break;
              case '#': isBotRunning(false); botPort = -1; break;
              }
              
            }
          }
   
}

function sendBotLogOn() {
  if (isBotConnected() == false){
    connectSocketBot(botPort);
    socketBot.send("log=on");
  }
  socketBot.send("log=on");
}

function sendBotLogOff() {
  if (isBotConnected() == false){
    connectSocketBot(botPort);
    socketBot.send("log=off");
  }
  socketBot.send("log=off");
}

function startBot() {
  socketController.send("~~1$$"+botid+"$$"+magicKey+"$$");
}

function stopBot() {
  socketController.send("~~2$$"+botid+"$$"+magicKey+"$$"); 
}

function appendToBlacklist(element, index, array){
$('#blacklistDisplay').append('<h3>Blacklist Entry '+index+'</h3>');
$('#blacklistDisplay').append('<div><table><tr><td><input type="text" name="userid'+index+'" size="25" value="'+element['userid']+'"></td><td><input type="text" name="reason'+index+'" size="60" value="'+element['reason']+'"></td></tr><tr><td>Userid</td><td>Reason</td></tr></table></div>');
//alert('test1');
}

function appendToAds(element, index, array){
$('#adsDisplay').append('<h3>Ad '+index+'</h3>');

$('#adsDisplay').append('<div><table><tr><td><input size="75" type="text" name="admsg'+index+'" value="'+element['message']+'"></td><td><input type="text" name="addelay'+index+'" size="10" value="'+element['delay']+'"></td></tr><tr><td>Message</td><td>Delay</td></tr></table></div>');
}

function appendToTriggers(element, index, array){

var triggerSwitch = '';
var strictSwitch = '';
var nameSwitch = '';
var sayingSwitch = '';


var a_voteup = '';
var a_votedown = '';
var a_action = '';
var a_restart = '';
var a_rehash = '';
var a_snag = '';
var a_nextup = '';
var a_skip = '';
var a_forget = '';
var a_hopup = '';
var a_hopdown = '';
var a_userids = '';
var a_kick = '';
var a_ban = '';
var a_removedj = '';
var a_fan = '';
var a_slide = '';
var a_queue = '';
var a_queue_list = '';
var a_queue_add = '';
var a_queue_remove = '';
var a_theme = '';
var a_theme_set = '';
var a_auto_dj = '';
var a_stats = '';

var acl_0 = '';
var acl_1 = '';
var acl_2 = '';
var acl_3 = '';
var acl_4 = '';


if(element['use_trigger_switch'] == '1') { triggerSwitch = 'checked'; }
if(element['use_strict_matching'] == '1') { strictSwitch = 'checked'; }
if(element['use_name_switch'] == '1') { nameSwitch = 'checked'; }
if(element['use_saying_switch'] == '1') { sayingSwitch = 'checked'; }

if(element['action'] == "*stats") { a_stats = 'selected'; }
if(element['action'] == "*theme") { a_theme = 'selected'; }
if(element['action'] == "*themeset") { a_theme_set = 'selected'; }
if(element['action'] == "*autodj") { a_auto_dj = 'selected'; }
if(element['action'] == "*voteup") { a_voteup = 'selected'; }
if(element['action'] == "*votedown") { a_votedown = 'selected'; }
if(element['action'] == "*action") { a_action = 'selected'; }
if(element['action'] == "*restart") { a_restart = 'selected'; }
if(element['action'] == "*rehash") { a_rehash = 'selected'; }
if(element['action'] == "*snag") { a_snag = 'selected'; }
if(element['action'] == "*nextup") { a_nextup = 'selected'; }
if(element['action'] == "*skip") { a_skip = 'selected'; }
if(element['action'] == "*forget") { a_forget = 'selected'; }
if(element['action'] == "*hopup") { a_hopup = 'selected'; }
if(element['action'] == "*hopdown") { a_hopdown = 'selected'; }
if(element['action'] == "*userids") { a_userids = 'selected'; }
if(element['action'] == "*kick") { a_kick = 'selected'; }
if(element['action'] == "*ban") { a_ban = 'selected'; }
if(element['action'] == "*removedj") { a_removedj = 'selected'; }
if(element['action'] == "*fan") { a_fan = 'selected'; }
if(element['action'] == "*slide") { a_slide = 'selected'; }
if(element['action'] == "*queue") { a_queue = 'selected'; }
if(element['action'] == "*queue_list") { a_queue_list = 'selected'; }
if(element['action'] == "*queue_add") { a_queue_add = 'selected'; }
if(element['action'] == "*queue_remove") { a_queue_remove = 'selected'; }

if(element['access_level'] == "0") { acl_0 = 'selected' }
if(element['access_level'] == "1") { acl_1 = 'selected' }
if(element['access_level'] == "2") { acl_2 = 'selected' }
if(element['access_level'] == "3") { acl_3 = 'selected' }
if(element['access_level'] == "4") { acl_4 = 'selected' }

$('#triggersDisplay').append('<h3>'+element['trigger_phrase']+'</h3>');
$('#triggersDisplay').append('<div><p><center>Use Command Prefix&nbsp;<input type="checkbox" name="useTrigger'+index+'" value="1" '+triggerSwitch+'>&nbsp;&nbsp;Use Strict&nbsp;<input type="checkbox" name="useStrict'+index+'" value="1" '+strictSwitch+'>&nbsp;&nbsp;Use Name&nbsp;<input type="checkbox" name="useName'+index+'" value="1" '+nameSwitch+'>&nbsp;&nbsp;Random Saying&nbsp;<input type="checkbox" name="useSaying'+index+'" value="1" '+sayingSwitch+'></center></p><p><center>Action&nbsp;<select name="action'+index+'">\
<option value="*voteup" '+a_voteup+'>Vote Up</option>\
<option value="*votedown" '+a_votedown+'>Vote Down</option>\
<option value="*action" '+a_action+'>Action</option>\
<option value="*restart" '+a_restart+'>Restart</option>\
<option value="*rehash" '+a_rehash+'>Rehash</option>\
<option value="*snag" '+a_snag+'>Snag</option>\
<option value="*nextup" '+a_nextup+'>Next Up</option>\
<option value="*skip" '+a_skip+'>Skip</option>\
<option value="*forget" '+a_forget+'>Forget</option>\
<option value="*hopup" '+a_hopup+'>Hop Up</option>\
<option value="*hopdown" '+a_hopdown+'>Hop Down</option>\
<option value="*userids" '+a_userids+'>Userids</option>\
<option value="*kick" '+a_kick+'>Kick</option>\
<option value="*ban" '+a_ban+'>Ban</option>\
<option value="*removedj" '+a_removedj+'>Remove Dj</option>\
<option value="*fan" '+a_fan+'>Fan</option>\
<option value="*slide" '+a_slide+'>Slide</option>\
<option value="*queue" '+a_queue+'>Queue</option>\
<option value="*queue_list" '+a_queue_list+'>Queue List</option>\
<option value="*queue_add" '+a_queue_add+'>Queue Add</option>\
<option value="*queue_remove" '+a_queue_remove+'>Queue Remove</option>\
<option value="*theme" '+a_theme+'>Theme</option>\
<option value="*themeset" '+a_theme_set+'>Theme Set</option>\
<option value="*stats" '+a_stats+'>Stats</option>\
<option value="*autodj" '+a_auto_dj+'>Auto DJ</option>\
</select>&nbsp;&nbsp;Access Level&nbsp;<select name="access_level'+index+'"><option value="0" '+acl_0+'>Everyone</option><option value="1" '+acl_1+'>At least level 1</option><option value="2" '+acl_2+'>At least level 2</option><option value="3" '+acl_3+'>At least level 3</option><option value="4" '+acl_4+'>Owner</option></select>&nbsp;&nbsp;Trigger Phrase&nbsp;<input type="text" name="triggerPhrase'+index+'" size="15%" value="'+element['trigger_phrase']+'"></center></p><p><center>Pre Name Response&nbsp;<input type="text" name="preNameResponse'+index+'" id="triggerPreNameResponse" size="20%" value="'+element['pre_name_response']+'">&nbsp;&nbsp;Post Name Response&nbsp;<input type="text" name="postNameResponse'+index+'" id="triggerPostNameResponse" size="20%" value="'+element['post_name_response']+'"></center></p><p><center>Command Fail Response&nbsp;<input type="text" name="commandFail'+index+'" size="30%" value="'+element['command_fail_response']+'"></center></p><p>Command Description</p><p><center><textarea name="commandDescription'+index+'" cols="80" rows="5">'+element['command_description']+'</textarea><div style="font-size: 75%">&lt;br&gt;&lt;font&gt;&lt;a&gt;&lt;img&gt;&lt;p&gt; tags allowed</div></center></p></div>');

}

function appendToEvents(element, index, array){
var e_dj_added = '';
var e_dj_removed = '';
var e_user_entered = '';
var e_room_updated = '';
var e_user_booted = '';
var e_song_snagged = '';
var e_user_left = '';
var e_private_message = '';
var e_chat_room = '';
var e_checked = '';
var e_saying = '';

if(element['event'] == "#dj_added") { e_dj_added = 'selected'; }
if(element['event'] == "#dj_removed") { e_dj_removed = 'selected'; }
if(element['event'] == "#user_entered") { e_user_entered = 'selected'; }
if(element['event'] == "#room_updated") { e_room_updated = 'selected'; }
if(element['event'] == "#user_booted") { e_user_booted = 'selected'; }
if(element['event'] == "#song_snagged") { e_song_snagged = 'selected'; }
if(element['event'] == "#user_left") { e_user_left = 'selected'; }

if(element['delivery_method'] == "0") { e_private_message = 'selected'; }
if(element['delivery_method'] == "1") { e_chat_room = 'selected'; }

if(element['include_name'] == "1") { e_checked = 'checked'; }
if(element['use_saying'] == "1") { e_saying = 'checked'; }

$('#eventsDisplay').append('<h3>'+element['event'].replace(/#/,'').replace(/_/,' ')+'</h3>');
$('#eventsDisplay').append('<div><table><tr><td>Use Name&nbsp;&nbsp;<input name="include_name'+index+'" type="checkbox" value="1" '+e_checked+'/></td><td>Random Saying&nbsp;&nbsp;<input name="useSaying'+index+'" type="checkbox" value="1" '+e_saying+'/></td></tr><tr><td><select name="event'+index+'"><option value="#dj_added" '+e_dj_added+'>dj added</option><option value="#dj_removed" '+e_dj_removed+'>dj removed</option><option value="#user_entered" '+e_user_entered+'>user entered</option><option value="#room_updated" '+e_room_updated+'>room updated</option><option value="#user_booted" '+e_user_booted+'>user booted</option><option value="#song_snagged" '+e_song_snagged+'>song snagged</option><option value="#user_left" '+e_user_left+'>user left</option></select></td><td><select name="delivery_method'+index+'"><option value="0" '+e_private_message+'>Private Message</option><option value="1" '+e_chat_room+'>Chat Room</option></select></td><td><input name="pre_text'+index+'" type="text" size="25" value="'+element['pre_text']+'"/></td><td><input name="post_text'+index+'" type="text" size="25" value="'+element['post_text']+'"/></td></tr><tr><td>Event</td><td>Delivery</td><td>Pre Name Text</td><td>Post Name Text</td></tr></table></div>');
}

function appendToACL(element, index, array){
var a_level1 = '';
var a_level2 = '';
var a_level3 = '';
if(element['access_level'] == "1") { a_level1 = 'selected'; }
if(element['access_level'] == "2") { a_level2 = 'selected'; }
if(element['access_level'] == "3") { a_level3 = 'selected'; }

$('#aclsDisplay').append('<h3>'+element['comment']+'</h3>');
$('#aclsDisplay').append('<div><table><tr><td>Access Level&nbsp;&nbsp;<select name="level'+index+'"><option '+a_level1+'>1</option><option '+a_level2+'>2</option><option '+a_level3+'>3</option></td></tr><tr><td><input class="input_field" type="text" name="userid'+index+'" size="25%" value="'+element['userid']+'"></td><td><input type="text" name="comment'+index+'" size="55%" value="'+element['comment']+'"></td></tr><tr><td>Userid</td><td>Name / Comment</td></tr></table></div>');
}

function setup_bot_stats_theme(theme) {
var t_dark_hive;
var t_cupertino;
var t_mint_choc;
var t_south_street;
var t_blitzer;
var t_smoothness;
var t_le_frog;
var t_flick;
var t_humanity;

if (theme == 'dark-hive') { t_dark_hive = 'selected'; }
if (theme == 'cupertino') { t_cupertino = 'selected'; }
if (theme == 'mint-choc') { t_mint_choc = 'selected'; }
if (theme == 'south-street') { t_south_street = 'selected'; }
if (theme == 'blitzer') { t_blitzer = 'selected'; }
if (theme == 'smoothness') { t_smoothness = 'selected'; }
if (theme == 'le-frog') { t_le_frog = 'selected'; }
if (theme == 'flick') { t_flick = 'selected'; }
if (theme == 'humanity') { t_humanity = 'selected'; }

$('#statsTheme').html('<option '+t_dark_hive+'>dark-hive</option><option '+t_cupertino+'>cupertino</option><option '+t_mint_choc+'>mint-choc</option><option '+t_south_street+'>south-street</option><option '+t_blitzer+'>blitzer</option><option '+t_smoothness+'>smoothness</option><option '+t_le_frog+'>le-frog</option><option '+t_flick+'>flick</option><option '+t_humanity+'>humanity</option>');

}

function pullAJAX() {
  $.get(
    "/websockets/pull.php",
    {bot_userid : botid, magic_key : magicKey},
    function(data) {
       dataAJAX = json_parse(data);
       dataAJAX['adverts'].pop();
       dataAJAX['triggers'].pop();
       dataAJAX['blacklist'].pop();
       dataAJAX['events'].pop();
       dataAJAX['acl'].pop();
       dataAJAX['adverts'].forEach(appendToAds);
       dataAJAX['triggers'].forEach(appendToTriggers);
       dataAJAX['blacklist'].forEach(appendToBlacklist);
       dataAJAX['events'].forEach(appendToEvents);
       dataAJAX['acl'].forEach(appendToACL);
       
       $('#roomDescrBgColor').val(dataAJAX['stats_bg_color']);
	if(dataAJAX['room_description'] != null) {
       $('#roomDescrText').val(dataAJAX['room_description'].replace(/\&lt\;/g,"<").replace(/\&gt\;/g,">").replace(/\&quot\;/g,"\""));
        }
       setup_bot_stats_theme(dataAJAX['stats_color_theme']);

	if (dataAJAX['start_slide'] == 1) { $('#slideSwitch').attr("checked", true); }
	if (dataAJAX['start_queue'] == 1) { $('#queueSwitch').attr("checked", true); }
	if (dataAJAX['mods_to_lvl1'] == 1) { $('#modsToLvl1').attr("checked", true); }
	if (dataAJAX['start_stats'] == 1) { $('#statsSwitch').attr("checked", true); }
	if (dataAJAX['start_autodj'] == 1) { $('#autoDjSwitch').attr("checked", true); }


       //Fix jQuery
	//Append New Command to triggers
$('#triggersDisplay').append('<h3>New Trigger</h3>');
$('#triggersDisplay').append('<div><p><center>Use Command Prefix&nbsp;<input type="checkbox" name="useTrigger" value="1">&nbsp;&nbsp;Use Strict&nbsp;<input type="checkbox" name="useStrict" value="1">&nbsp;&nbsp;Use Name&nbsp;<input type="checkbox" name="useName" value="1">&nbsp;&nbsp;Random Saying&nbsp;<input type="checkbox" name="useSaying" class="triggerUseSaying" value="1"></center></p><p><center>Action&nbsp;<select name="action">\
<option value="*voteup">Vote Up</option>\
<option value="*votedown">Vote Down</option>\
<option value="*action">Action</option>\
<option value="*restart">Restart</option>\
<option value="*rehash">Rehash</option>\
<option value="*snag">Snag</option>\
<option value="*nextup">Next Up</option>\
<option value="*skip">Skip</option>\
<option value="*forget">Forget</option>\
<option value="*hopup">Hop Up</option>\
<option value="*hopdown">Hop Down</option>\
<option value="*userids">Userids</option>\
<option value="*kick">Kick</option>\
<option value="*ban">Ban</option>\
<option value="*removedj">Remove Dj</option>\
<option value="*fan">Fan</option>\
<option value="*slide">Slide</option>\
<option value="*queue">Queue</option>\
<option value="*queue_list">Queue List</option>\
<option value="*queue_add">Queue Add</option>\
<option value="*queue_remove">Queue Remove</option>\
<option value="*theme">Theme</option>\
<option value="*themeset">Theme Set</option>\
<option value="*stats">Stats</option>\
<option value="*autodj">Auto DJ</option>\
</select>&nbsp;&nbsp;Access Level&nbsp;<select id="access_level" name="access_level"><option value="0">Everyone</option><option value="1">At least level 1</option><option value="2">At least level 2</option><option value="3">At least level 3</option><option value="4">Owner</option></select>&nbsp;&nbsp;Trigger Phrase&nbsp;<input type="text" name="triggerPhrase" size="15%"></center></p><p><center>Pre Name Response&nbsp;<input type="text" name="preNameResponse" id="triggerPreNameResponse" size="20%">&nbsp;&nbsp;Post Name Response&nbsp;<input type="text" name="postNameResponse" id="triggerPostNameResponse" size="20%"></center></p><p><center>Command Fail Response&nbsp;<input type="text" name="commandFail" size="30%"></center></p><p>Command Description</p><p><center><textarea name="commandDescription" cols="80" rows="5"></textarea><div style="font-size: 75%">&lt;br&gt;&lt;font&gt;&lt;a&gt;&lt;img&gt;&lt;p&gt; tags allowed</div></center></p></div>');

$('#adsDisplay').append('<h3>New Ad</h3>');
$('#adsDisplay').append('<div><table><tr><td><input size="75" type="text" name="admsg"></td><td><input type="text" name="addelay" size="10"></td></tr><tr><td>Message</td><td>Delay</td></tr></table></div>');

$('#blacklistDisplay').append('<h3>New Blacklist Entry</h3>');
$('#blacklistDisplay').append('<div><table><tr><td><input type="text" name="userid" size="25"></td><td><input type="text" name="reason" size="60"></td></tr><tr><td>Userid</td><td>Reason</td></tr></table></div>');

$('#eventsDisplay').append('<h3>New Event</h3>');
$('#eventsDisplay').append('<div><table><tr><td>Use Name&nbsp;&nbsp;<input name="include_name" type="checkbox" value="1" /></td><td>Random Saying&nbsp;&nbsp;<input name="useSaying" type="checkbox" value="1" /></td></tr><tr><td><select name="event"><option value="#dj_added">dj added</option><option value="#dj_removed">dj removed</option><option value="#user_entered">user entered</option><option value="#room_updated">room updated</option><option value="#user_booted">user booted</option><option value="#song_snagged">song snagged</option><option value="#user_left">user left</option></select></td><td><select name="delivery_method"><option value="0" >Private Message</option><option value="1" selected="true">Chat Room</option></select></td><td><input class="input_field" name="pre_text" type="text" size="25" /></td><td><input class="input_field" name="post_text" type="text" size="25" /></td></tr><tr><td>Event</td><td>Delivery</td><td>Pre Name Text</td><td>Post Name Text</td></tr></table></div>');

$('#aclsDisplay').append('<h3>New Access Control</h3>');
$('#aclsDisplay').append('<div><table><tr><td>Access Level&nbsp;&nbsp;<select name="access_level"><option>1</option><option>2</option><option>3</option></td></tr><tr><td><input class="input_field" type="text" name="userid" size="25%"></td><td><input class="input_field" type="text" name="comment" size="55%"></td></tr><tr><td>Userid</td><td>Name / Comment</td></tr></table></div>');

       $( "#adsDisplay" ).accordion({collapsible: true, active: false });
       $( "#triggersDisplay" ).accordion({collapsible: true, active: false });
       $( "#blacklistDisplay" ).accordion({collapsible: true, active: false });
       $( "#eventsDisplay" ).accordion({collapsible: true, active: false });
       $( "#aclsDisplay" ).accordion({collapsible: true, active: false });


//alert('test2');
$( ".triggerUseSaying").on('click', function() {
if($(this).is(':checked') == true) {
$(this).parent().parent().find("#triggerPreNameResponse").attr("disabled", "disabled");
$(this).parent().parent().find("#triggerPostNameResponse").attr("disabled", "disabled");
}else{
$(this).parent().parent().find("#triggerPreNameResponse").removeAttr("disabled");
$(this).parent().parent().find("#triggerPostNameResponse").removeAttr("disabled");
}
});

	//alert($("#triggersDisplayWrapper").html());	
       
       var button = $(".ui-dialog-buttonpane button:contains('Continue')");
       setTimeout(function(){$(button).button("enable")}, 500); // One Extra Second :)
    }
);
}

function refreshAJAX(){
$('#triggersDisplayWrapper').html('<div id="triggersDisplay" style="width: 90%; margin-left: auto; margin-right: auto"></div>');
$('#adsDisplayWrapper').html('<div id="adsDisplay" style="width: 90%; margin-left: auto; margin-right: auto"></div>');
$('#blacklistDisplayWrapper').html('<div id="blacklistDisplay" style="width: 90%; margin-left: auto; margin-right: auto"></div>');
$('#eventsDisplayWrapper').html('<div id="eventsDisplay" style="width: 90%; margin-left: auto; margin-right: auto"></div>');
$('#aclsDisplayWrapper').html('<div id="aclsDisplay" style="width: 90%; margin-left: auto; margin-right: auto"></div>');


pullAJAX();
}



  function redirect() {
        <?php $_SESSION['invalidlogin'] = "true"; ?>
        window.location.href = "http://"+http_host+"/#Login";
  }

  function loadFunction(form){
        window.location.href = "/console/#" + form.function.value;
  }

  function testResults (form) {
document.getElementById("form_error_message").innerHTML = "";
//Test ids
if ( form.ouserid.value.length < 24 ){
     event.preventDefault();
     form.ouserid.style.border="1px solid #F00"; 
     document.getElementById("form_error_message").innerHTML += '<p>Owner userid is too short.  Please correct and try again.</p>';
}

if ( form.userid.value.length < 24 ){
     event.preventDefault();
     form.userid.style.border="1px solid #F00"; 
     document.getElementById("form_error_message").innerHTML += '<p>Bot userid is too short.  Please correct and try again.</p>';
}

if ( form.roomid.value.length < 24 ){
     event.preventDefault();
     form.roomid.style.border="1px solid #F00"; 
     document.getElementById("form_error_message").innerHTML += '<p>Bot roomid is too short.  Please correct and try again.</p>';
}

if ( form.authid.value.length < 24 ){
     event.preventDefault();
     form.authid.style.border="1px solid #F00"; 
     document.getElementById("form_error_message").innerHTML += '<p>Bot authid is too short.  Please correct and try again.</p>';
}


//Test Passwm_rds
if ( form.password1.value != form.password2.value ){
     event.preventDefault();
     form.password1.style.border="1px solid #F00"; 
     form.password2.style.border="1px solid #F00"; 
     document.getElementById("form_error_message").innerHTML += '<p>Password do not match.  Please correct and try again.</p>';
}

if ( form.password1.value.length < 8 ) {
     event.preventDefault();
     form.password1.style.border="1px solid #F00"; 
     form.password2.style.border="1px solid #F00"; 
     document.getElementById("form_error_message").innerHTML += '<p>Password too short.  Please correct and try again.</p>';

}

form.password1.value = calcMD5(form.password1.value);
form.password2.value = calcMD5(form.password2.value);

//Test Email
    var email = form.email.value;
    var verimail = new Comfirm.AlphaMail.Verimail();
     verimail.verify(email, function(status, message, suggestion){
    if(status < 0){
        // Incorrect syntax!
     event.preventDefault();
     form.email.style.border="1px solid #F00";    
     document.getElementById("form_error_message").innerHTML += '<p>E-mail address is invalid.  Please correct and try again.</p>';
     }
});
}

function functions_list(which_function) {
  document.write('<form method="post" name="function" action="#"><label for="function">&nbsp;</label><select name="function" id="function" style="width: 180px" onmouseup="loadFunction(this.form)">\
                <?php
                $counter=1;
                $flags = array ("A");
                /* This is the correct way to loop over the directory. */
                foreach (scandir("functions") as $function) {
                  if(strlen($function) > 4) {
                    $functions_level = substr($function,-1,1);
                    //error_log("Stupid variable $functions_level $flags\n");
                    if(in_array($functions_level, $flags)) { 
                      echo '<option value="'.str_pad($counter,3,"0", STR_PAD_LEFT).'">'.substr($function,4,-2).'</option>';
                      $counter++;
                      }                     
                    }
                    }
                

                ?></select>&nbsp;&nbsp;');
		if(which_function != ''){document.write('<input type="submit" value="Update" form="'+which_function+'">');}
		document.write('</form>');
}
    </script>
</head> 
<body>
<div id="dialog-message" title="Sending Updates" style="display: none;">
<p>Sending Updates</p>
</div>
 
<div id="templatemo_header">
    <div id="site_title"><h1><a href="http://www.neurobots.net/" title="neuroBots">neuroBots</a></h1></div>
</div>
<div class="my_username"><?php include("test.php"); ?></div>

<div id="templatemo_main">
    <div id="content"> 

		<div id="home" class="section">
        	
			<div id="home_about" class="box">
           	  <?php if($auth) { 
                  echo "<h2>Welcome back</h2>";
                  echo file_get_contents('../config/motd.conf'); 
                } else { 
                  //They aren't a valid user
                  echo "<img src='/images/loading.gif' onload='redirect()'>";
                } 
                ?>
                </div>
                <div id="home_gallery" class="box no_mr">

                <script type="text/javascript"> functions_list(); </script>
                </div>

               
        </div> <!-- END of home -->        
<?php
                if ($handle = opendir('functions')) {
                  while (false !== ($entry = readdir($handle))) {
                  echo file_get_contents('./functions/'.$entry);
                  }
                }

?>
   
    </div> 
</div>
<div id="templatemo_footer">
      Copyright © 2013 <a href="#">neuroBots.net</a> | Template designed by <a href="http://www.templatemo.com" target="_parent">Free CSS Templates</a>
</div>

</body> 
</html>
