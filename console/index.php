<?php session_start(); ?>
<!DOCTYPE html>
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

	<link rel="stylesheet" href="style.css" type="text/css" media="screen, projection" />

	<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
  <script src="http://code.jquery.com/jquery-migrate-1.1.1.min.js"></script>
  <script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js"></script>
	<script type="text/javascript" src="/js/jquery.websocket-0.0.1.js"></script>
	<script type="text/JavaScript" src="/js/json_parse.js"></script>
	<script type="text/JavaScript" src="/js/jquery.form.js"></script>
	<script type="text/JavaScript" src="/js/jquery.popupWindow.js"></script>
  <script type="text/JavaScript" src="/js/base64_decode.js"></script>
  <script type="text/JavaScript" src='/js/spectrum.js'></script>

  <link rel='stylesheet' href='/css/spectrum.css' />
	<link rel="stylesheet" href="/juithemes/dark-hive/jquery-ui.css" />
	<link rel="stylesheet" href="/juithemes/dark-hive/jquery-ui.min.css" />
	<link rel="stylesheet" href="/juithemes/dark-hive/jquery.ui.theme.css" />
	<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css' />

  <style>

  .ui-tabs-vertical { width: 100%; }
  .ui-tabs-vertical .ui-tabs-nav { padding: .2em .1em .2em .2em; float: left; width: 12em; }
  .ui-tabs-vertical .ui-tabs-nav li { clear: left; width: 100%; border-bottom-width: 1px !important; border-right-width: 0 !important; margin: 0 -1px .2em 0; }
  .ui-tabs-vertical .ui-tabs-nav li a { display:block; width: 10em }
  .ui-tabs-vertical .ui-tabs-nav li.ui-tabs-active { padding-bottom: 0; padding-right: .1em; border-right-width: 1px; border-right-width: 1px; }
  .ui-tabs-vertical .ui-tabs-panel { padding: 1em; float: right; width: calc(100% - 15em) }
  
  .full-spectrum .sp-palette { max-width: 200px; }

textarea {
    resize: none;
}

  </style>

<script type="text/JavaScript"> 

var isControllerConnected = false;
var isBotRunning = false;
var isBotConnected = false;
var socketController;
var botPort = -1;

<?php 

$bot_userid = "";
$flags = array();
require_once('../config/db.conf'); 
echo "var http_host = ".$db->quote($_SERVER['HTTP_HOST']).";\n";
$flags = array();
foreach ($db->query("SELECT bot_userid, magic_key, controller, bot_authid, command_trigger, owner_userid, bot_roomid, id, flags FROM users WHERE login_hash = ".$db->quote($_COOKIE['PHPSESSID'])." LIMIT 1") as $row)  {
$flags = preg_split('//', $row['flags'], -1, PREG_SPLIT_NO_EMPTY);
  echo "var flags = ".json_encode(preg_split('//', $row['flags'], -1, PREG_SPLIT_NO_EMPTY)).";\n";
    echo "var botid = ".$db->quote($row['bot_userid']).";\n";
    $bot_userid = $row['bot_userid'];
    echo "var magicKey = ".$db->quote($row['magic_key']).";\n";
    if (($row['controller'] == "" )||( $row['controller'] == "0")){
      if(preg_match('/dev/',$_SERVER['HTTP_HOST'])){
        echo 'var controller = "a";'."\n";
      }else{
        echo 'var controller = "'.chop(`cat ~/app-root/data/conf/controller_number`).'";'."\n";
        }
    }else{
      echo 'var controller = "'.$row['controller'].'";'."\n";
      }
    echo "\n";
    echo "function getAuthId() { return (".$db->quote($row['bot_authid'])."); }\n";
    echo "function getTrigger() { return (".$db->quote($row['command_trigger'])."); }\n";
    echo "function getOwnerId() { return (".$db->quote($row['owner_userid'])."); }\n";
    echo "function getRoomId() { return (".$db->quote($row['bot_roomid'])."); }\n";
    echo "function getId() { return (".$db->quote($row['id'])."); }\n";
    echo "function getBotId() { return (".$db->quote($row['bot_userid'])."); }\n";
    echo "function getMagicKey() { return (".$db->quote($row['magic_key'])."); }\n";
    } 
?>

var accordianOpts = {
  collapsible: true,
  active: false
};

function unlockBasics() { 
  $('#basics_botuserid').prop('readonly', false); 
  $('#basics_botauthid').prop('readonly', false); 
  $('#basics_botroomid').prop('readonly', false); 
  $('#basics_botownerid').prop('readonly', false); 
  $('#basics_commandtrigger').prop('readonly', false); 

  $(function() { 
    $( "#unlock-basics" ).dialog({
      modal: true,
      buttons: {
        Continue: function() {
          $( this ).dialog( "close" );
          }
        }
    });
});

}

function logout() {
  sessid = document.cookie.match(/PHPSESSID=[^;]+/);
  var httpRequest;
  httpRequest = new XMLHttpRequest();
  httpRequest.open('GET', 'http://'+http_host+'/websockets/logout.php?'+sessid, false);
  httpRequest.send(null);
  window.location.href = "http://"+http_host+"/";
}

function popupDialog() {

	$(function() {

		$( "#sending-updates" ).dialog({
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

function connectSocketController() {
  if (isControllerConnected == false) {
    socketController = new WebSocket("ws://www.neurobots.net/controller");

    socketController.onopen = function () {
      socketController.send(botid+"|"+magicKey);
      isControllerConnected = true;
      $("#connection_status").attr("src", "/images/on.png");
    } 

    socketController.onclose = function () {
      isControllerConnected = false;
      if(isBotConnected == false){ $("#connection_status").attr("src", "/images/off.png"); }
    }

    socketController.onmessage = function(msg) {  
    switch(msg.data.substring(0,1)){
      case '@': 
        isBotRunning = true; 
        botPort = msg.data.slice(3); 
        connectSocketBot(botPort); 
        $("#bot_status").attr("src", "/images/on.png");
        break;
      case '#': 
        isBotRunning = false; 
        botPort = -1; 
        break;
      }
     }
    }
  }

function connectSocketBot(port){
socketBot  = new WebSocket("ws://www.neurobots.net/bot"+port);
socketBot.onopen = function () {
  isBotConnected = true;
}
socketBot.onclose = function () {
  isBotConnected = false;
  $("#bot_status").attr("src", "/images/off.png");
}

  socketBot.onmessage = function(msg) { 
    if( msg.data.substring(0,4) === 'Log|') { 
        $( "div.logwindow" ).append('<p style="word-wrap: break-word;">'+ base64_decode(msg.data.substring(4)) + '</p>');
        var objDiv = document.getElementById("logwindow");
          objDiv.scrollTop = objDiv.scrollHeight;
      }else if(msg.data.substring(0,7) === 'Uptime|'){
         $( "#uptime" ).html("Uptime: "+String(((parseInt(msg.data.substring(7))/60)/60).toFixed(1))+" hours");
      }
}

}

function startBot() {
  if(isControllerConnected == false) { connectSocketController(); }
  socketController.send("~~1$$"+botid+"$$"+magicKey+"$$");
}

function stopBot() {
  if(isControllerConnected == false) { connectSocketController(); }
  socketController.send("~~2$$"+botid+"$$"+magicKey+"$$"); 
}

//Seperate out here

function appendToBlacklist_loop(element, index, array){
$('#blacklistDisplay').append('<h3>'+element['reason']+'</h3>');
$('#blacklistDisplay').append('<div><table><tr><td><input type="text" name="userid'+index+'" size="25" value="'+element['userid']+'"></td><td><input type="text" name="reason'+index+'" size="60" value="'+element['reason']+'"></td></tr><tr><td>Userid</td><td>Reason</td></tr></table></div>');
//alert('test1');
}

function appendToAds_loop(element, index, array){
$('#adsDisplay').append('<h3>Ad '+index+'</h3>');

$('#adsDisplay').append('<div><table><tr><td><input size="75" type="text" name="admsg'+index+'" value="'+element['message']+'"></td><td><input type="text" name="addelay'+index+'" size="10" value="'+element['delay']+'"></td></tr><tr><td>Message</td><td>Delay</td></tr></table></div>');
}

function appendToTriggers_loop(element, index, array){

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
var a_say = '';
var a_status = '';
var a_actionpm = '';
var a_queue_move = '';

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
if(element['action'] == "*actionpm") { a_actionpm = 'selected'; }
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
if(element['action'] == "*say") { a_say = 'selected'; }
if(element['action'] == "*status") { a_status = 'selected'; }
if(element['action'] == "*queue") { a_queue = 'selected'; }
if(element['action'] == "*queue_list") { a_queue_list = 'selected'; }
if(element['action'] == "*queue_add") { a_queue_add = 'selected'; }
if(element['action'] == "*queue_remove") { a_queue_remove = 'selected'; }
if(element['action'] == "*queue_move") { a_queue_move = 'selected'; }

if(element['access_level'] == "0") { acl_0 = 'selected' }
if(element['access_level'] == "1") { acl_1 = 'selected' }
if(element['access_level'] == "2") { acl_2 = 'selected' }
if(element['access_level'] == "3") { acl_3 = 'selected' }
if(element['access_level'] == "4") { acl_4 = 'selected' }

$('#triggersDisplay').append('<h3>'+element['trigger_phrase']+'</h3>');
$('#triggersDisplay').append('<div><p><center>Use Command Prefix&nbsp;<input type="checkbox" name="useTrigger'+index+'" value="1" '+triggerSwitch+'>&nbsp;&nbsp;Use Strict&nbsp;<input type="checkbox" name="useStrict'+index+'" value="1" '+strictSwitch+'>&nbsp;&nbsp;Use Name&nbsp;<input type="checkbox" name="useName'+index+'" value="1" '+nameSwitch+'>&nbsp;&nbsp;Random Saying&nbsp;<input type="checkbox" name="useSaying'+index+'" value="1" '+sayingSwitch+'></center></p><p><center>Action&nbsp;<select name="action'+index+'">\
<option value="*action" '+a_action+'>Action</option>\
<option value="*actionpm" '+a_actionpm+'>Action PM</option>\
<option value="*autodj" '+a_auto_dj+'>Auto DJ</option>\
<option value="*ban" '+a_ban+'>Ban</option>\
<option value="*fan" '+a_fan+'>Fan</option>\
<option value="*forget" '+a_forget+'>Forget</option>\
<option value="*hopdown" '+a_hopdown+'>Hop Down</option>\
<option value="*hopup" '+a_hopup+'>Hop Up</option>\
<option value="*kick" '+a_kick+'>Kick</option>\
<option value="*nextup" '+a_nextup+'>Next Up</option>\
<option value="*queue" '+a_queue+'>Queue</option>\
<option value="*queue_add" '+a_queue_add+'>Queue Add</option>\
<option value="*queue_list" '+a_queue_list+'>Queue List</option>\
<option value="*queue_move" '+a_queue_move+'>Queue Move</option>\
<option value="*queue_remove" '+a_queue_remove+'>Queue Remove</option>\
<option value="*restart" '+a_restart+'>Restart</option>\
<option value="*rehash" '+a_rehash+'>Rehash</option>\
<option value="*removedj" '+a_removedj+'>Remove Dj</option>\
<option value="*say" '+a_say+'>Say</option>\
<option value="*skip" '+a_skip+'>Skip</option>\
<option value="*slide" '+a_slide+'>Slide</option>\
<option value="*snag" '+a_snag+'>Snag</option>\
<option value="*stats" '+a_stats+'>Stats</option>\
<option value="*status" '+a_status+'>Status</option>\
<option value="*theme" '+a_theme+'>Theme</option>\
<option value="*themeset" '+a_theme_set+'>Theme Set</option>\
<option value="*userids" '+a_userids+'>Userids</option>\
<option value="*votedown" '+a_votedown+'>Vote Down</option>\
<option value="*voteup" '+a_voteup+'>Vote Up</option>\
</select>&nbsp;&nbsp;Access Level&nbsp;<select name="access_level'+index+'"><option value="0" '+acl_0+'>Everyone</option><option value="1" '+acl_1+'>At least level 1</option><option value="2" '+acl_2+'>At least level 2</option><option value="3" '+acl_3+'>At least level 3</option><option value="4" '+acl_4+'>Owner</option></select>&nbsp;&nbsp;Trigger Phrase&nbsp;<input type="text" name="triggerPhrase'+index+'" size="15%" value="'+element['trigger_phrase']+'"></center></p><p><center>Pre Name Response&nbsp;<input type="text" name="preNameResponse'+index+'" id="triggerPreNameResponse" size="20%" value="'+element['pre_name_response']+'">&nbsp;&nbsp;Post Name Response&nbsp;<input type="text" name="postNameResponse'+index+'" id="triggerPostNameResponse" size="20%" value="'+element['post_name_response']+'"></center></p><p><center>Pre Command Fail Response&nbsp;<input type="text" name="preCommandFail'+index+'" size="30%" value="'+element['pre_command_fail']+'">&nbsp;&nbsp;Post Command Fail Response&nbsp;<input type="text" name="postCommandFail'+index+'" size="30%" value="'+element['post_command_fail']+'"></center></p><p><center>Command Description</center></p><p><center><textarea name="commandDescription'+index+'" cols="80" rows="5">'+element['command_description']+'</textarea><div style="font-size: 75%">&lt;br&gt;&lt;font&gt;&lt;a&gt;&lt;img&gt;&lt;p&gt; tags allowed</div></center></p></div>');

}

function appendToEvents_loop(element, index, array){
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
var e_lamed = '';

if(element['event'] == "#dj_added") { e_dj_added = 'selected'; }
if(element['event'] == "#dj_removed") { e_dj_removed = 'selected'; }
if(element['event'] == "#user_entered") { e_user_entered = 'selected'; }
if(element['event'] == "#room_updated") { e_room_updated = 'selected'; }
if(element['event'] == "#user_booted") { e_user_booted = 'selected'; }
if(element['event'] == "#song_snagged") { e_song_snagged = 'selected'; }
if(element['event'] == "#user_left") { e_user_left = 'selected'; }
if(element['event'] == "#song_lamed") { e_lamed = 'selected'; }

if(element['delivery_method'] == "0") { e_private_message = 'selected'; }
if(element['delivery_method'] == "1") { e_chat_room = 'selected'; }

if(element['include_name'] == "1") { e_checked = 'checked'; }
if(element['use_saying'] == "1") { e_saying = 'checked'; }

$('#eventsDisplay').append('<h3>'+element['event'].replace(/#/,'').replace(/_/,' ')+'</h3>');
$('#eventsDisplay').append('<div><table><tr><td>Use Name&nbsp;&nbsp;<input name="include_name'+index+'" type="checkbox" value="1" '+e_checked+'/></td><td>Random Saying&nbsp;&nbsp;<input name="useSaying'+index+'" type="checkbox" value="1" '+e_saying+'/></td></tr><tr><td><select name="event'+index+'">\
<option value="#dj_added" '+e_dj_added+'>Dj Added</option>\
<option value="#dj_removed" '+e_dj_removed+'>Dj Removed</option>\
<option value="#user_entered" '+e_user_entered+'>User Entered</option>\
<option value="#room_updated" '+e_room_updated+'>Room Updated</option>\
<option value="#user_booted" '+e_user_booted+'>User Booted</option>\
<option value="#song_snagged" '+e_song_snagged+'>Song Snagged</option>\
<option value="#user_left" '+e_user_left+'>User Left</option>\
<option value="#song_lamed" '+e_lamed+'>Song Lamed</option>\
</select></td><td><select name="delivery_method'+index+'"><option value="0" '+e_private_message+'>Private Message</option><option value="1" '+e_chat_room+'>Chat Room</option></select></td><td><input name="pre_text'+index+'" type="text" size="25" value="'+element['pre_text']+'"/></td><td><input name="post_text'+index+'" type="text" size="25" value="'+element['post_text']+'"/></td></tr><tr><td>Event</td><td>Delivery</td><td>Pre Name Text</td><td>Post Name Text</td></tr></table></div>');
}

function appendToACL_loop(element, index, array){
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

function a_refresh_ajax(){

  if(dataAJAX['flags'].indexOf('B')) { 

    if(dataAJAX['pkg_b_data'][0]['anti_idle'] == 1)     { $('#anti_idle').attr("checked", true);     }else{ $('#anti_idle').attr("checked", false);     }
    if(dataAJAX['pkg_b_data'][0]['last_fm_e'] == 1)     { $('#last_fm_e').attr("checked", true);     }else{ $('#last_fm_e').attr("checked", false);     }
    if(dataAJAX['pkg_b_data'][0]['wikipedia_e'] == 1)   { $('#wikipedia_e').attr("checked", true);   }else{ $('#wikipedia_e').attr("checked", false);   }
    if(dataAJAX['pkg_b_data'][0]['weather_e'] == 1)     { $('#weather_e').attr("checked", true);     }else{ $('#weather_e').attr("checked", false);     }
    if(dataAJAX['pkg_b_data'][0]['user_lookup_e'] == 1) { $('#user_lookup_e').attr("checked", true); }else{ $('#user_lookup_e').attr("checked", false); }
    if(dataAJAX['pkg_b_data'][0]['dice_6_e'] == 1)      { $('#dice_6_e').attr("checked", true);      }else{ $('#dice_6_e').attr("checked", false);      }
    if(dataAJAX['pkg_b_data'][0]['dice_20_e'] == 1)     { $('#dice_20_e').attr("checked", true);     }else{ $('#dice_20_e').attr("checked", false);     }
    if(dataAJAX['pkg_b_data'][0]['8ball_e'] == 1)       { $('#8ball_e').attr("checked", true);       }else{ $('#8ball_e').attr("checked", false);       }
    if(dataAJAX['pkg_b_data'][0]['horoscope_e'] == 1)   { $('#horoscope_e').attr("checked", true);   }else{ $('#horoscope_e').attr("checked", false);   }

    //$('').val(dataAJAX['pkg_b_data'][0]['']);

    $('#last_fm_t').val(dataAJAX['pkg_b_data'][0]['last_fm_t']);
    $('#wikipedia_t').val(dataAJAX['pkg_b_data'][0]['wikipedia_t']);
    $('#weather_t').val(dataAJAX['pkg_b_data'][0]['weather_t']);
    $('#user_lookup_t').val(dataAJAX['pkg_b_data'][0]['user_lookup_t']);
    $('#dice_6_t').val(dataAJAX['pkg_b_data'][0]['dice_6_t']);
    $('#dice_20_t').val(dataAJAX['pkg_b_data'][0]['dice_20_t']);
    $('#ai_w_msg').val(dataAJAX['pkg_b_data'][0]['ai_w_msg']);
    $('#ai_msg').val(dataAJAX['pkg_b_data'][0]['ai_msg']);
    $('#ai_w_msg_t').val(dataAJAX['pkg_b_data'][0]['ai_w_msg_t']);
    $('#ai_msg_t').val(dataAJAX['pkg_b_data'][0]['ai_msg_t']);

    $('#8ball_t').val(dataAJAX['pkg_b_data'][0]['8ball_t']);
    $('#8ball_1').val(dataAJAX['pkg_b_data'][0]['8ball_1']);
    $('#8ball_2').val(dataAJAX['pkg_b_data'][0]['8ball_2']);
    $('#8ball_3').val(dataAJAX['pkg_b_data'][0]['8ball_3']);
    $('#8ball_4').val(dataAJAX['pkg_b_data'][0]['8ball_4']);
    $('#8ball_5').val(dataAJAX['pkg_b_data'][0]['8ball_5']);
    $('#8ball_6').val(dataAJAX['pkg_b_data'][0]['8ball_6']);
    $('#8ball_7').val(dataAJAX['pkg_b_data'][0]['8ball_7']);
    $('#8ball_8').val(dataAJAX['pkg_b_data'][0]['8ball_8']);
    $('#8ball_9').val(dataAJAX['pkg_b_data'][0]['8ball_9']);
    $('#8ball_10').val(dataAJAX['pkg_b_data'][0]['8ball_10']);
    $('#8ball_11').val(dataAJAX['pkg_b_data'][0]['8ball_11']);
    $('#8ball_12').val(dataAJAX['pkg_b_data'][0]['8ball_12']);
    $('#8ball_13').val(dataAJAX['pkg_b_data'][0]['8ball_13']);
    $('#8ball_14').val(dataAJAX['pkg_b_data'][0]['8ball_14']);
    $('#8ball_15').val(dataAJAX['pkg_b_data'][0]['8ball_15']);
    $('#8ball_16').val(dataAJAX['pkg_b_data'][0]['8ball_16']);
    $('#8ball_17').val(dataAJAX['pkg_b_data'][0]['8ball_17']);
    $('#8ball_18').val(dataAJAX['pkg_b_data'][0]['8ball_18']);
    $('#8ball_19').val(dataAJAX['pkg_b_data'][0]['8ball_19']);
    $('#8ball_20').val(dataAJAX['pkg_b_data'][0]['8ball_20']);    

    //alert(dataAJAX['pkg_b_data'][0]['anti_idle']); 
    }



       $("#color-picker").spectrum("set",dataAJAX['stats_bg_color']);
     
  if(dataAJAX['room_description'] != null) {
       $('#roomDescrText').val(dataAJAX['room_description'].replace(/\&lt\;/g,"<").replace(/\&gt\;/g,">").replace(/\&quot\;/g,"\""));
        }
       setup_bot_stats_theme(dataAJAX['stats_color_theme']);

  if (dataAJAX['start_slide'] == 1) { $('#slideSwitch').attr("checked", true); }
  if (dataAJAX['start_queue'] == 1) { $('#queueSwitch').attr("checked", true); }
  if (dataAJAX['mods_to_lvl1'] == 1) { $('#modsToLvl1').attr("checked", true); }
  if (dataAJAX['start_stats'] == 1) { $('#statsSwitch').attr("checked", true); }
  if (dataAJAX['start_autodj'] == 1) { $('#autoDjSwitch').attr("checked", true); }
  if (dataAJAX['switch_alonedj'] == 1) { $('#aloneDjSwitch').attr("checked", true); }
  if (dataAJAX['switch_autorequeue'] == 1) { $('#autoReQueueSwitch').attr("checked", true); }

       //Fix jQuery
  //Append New Command to triggers
$('#triggersDisplay').append('<h3>New Trigger</h3>');
$('#triggersDisplay').append('<div><p><center>Use Command Prefix&nbsp;<input type="checkbox" name="useTrigger" value="1">&nbsp;&nbsp;Use Strict&nbsp;<input type="checkbox" name="useStrict" value="1">&nbsp;&nbsp;Use Name&nbsp;<input type="checkbox" name="useName" value="1">&nbsp;&nbsp;Random Saying&nbsp;<input type="checkbox" name="useSaying" class="triggerUseSaying" value="1"></center></p><p><center>Action&nbsp;<select name="action">\
<option value="*action">Action</option>\
<option value="*actionpm">Action PM</option>\
<option value="*autodj">Auto DJ</option>\
<option value="*ban">Ban</option>\
<option value="*fan">Fan</option>\
<option value="*forget">Forget</option>\
<option value="*hopdown">Hop Down</option>\
<option value="*hopup">Hop Up</option>\
<option value="*kick">Kick</option>\
<option value="*nextup">Next Up</option>\
<option value="*queue">Queue</option>\
<option value="*queue_add">Queue Add</option>\
<option value="*queue_list">Queue List</option>\
<option value="*queue_move">Queue Move</option>\
<option value="*queue_remove">Queue Remove</option>\
<option value="*restart">Restart</option>\
<option value="*rehash">Rehash</option>\
<option value="*removedj">Remove Dj</option>\
<option value="*say">Say</option>\
<option value="*skip">Skip</option>\
<option value="*slide">Slide</option>\
<option value="*snag">Snag</option>\
<option value="*stats">Stats</option>\
<option value="*status">Status</option>\
<option value="*theme">Theme</option>\
<option value="*themeset">Theme Set</option>\
<option value="*userids">Userids</option>\
<option value="*votedown">Vote Down</option>\
<option value="*voteup">Vote Up</option>\
</select>&nbsp;&nbsp;Access Level&nbsp;<select id="access_level" name="access_level"><option value="0">Everyone</option><option value="1">At least level 1</option><option value="2">At least level 2</option><option value="3">At least level 3</option><option value="4">Owner</option></select>&nbsp;&nbsp;Trigger Phrase&nbsp;<input type="text" name="triggerPhrase" size="15%"></center></p><p><center>Pre Name Response&nbsp;<input type="text" name="preNameResponse" id="triggerPreNameResponse" size="20%">&nbsp;&nbsp;Post Name Response&nbsp;<input type="text" name="postNameResponse" id="triggerPostNameResponse" size="20%"></center></p><p><center>Pre Command Fail Response&nbsp;<input type="text" name="preCommandFail" size="30%">&nbsp;&nbsp;Post Command Fail Response&nbsp;<input type="text" name="postCommandFail" size="30%"></center></p><p><center>Command Description</center></p><p><center><textarea name="commandDescription" cols="80" rows="5"></textarea><div style="font-size: 75%">&lt;br&gt;&lt;font&gt;&lt;a&gt;&lt;img&gt;&lt;p&gt; tags allowed</div></center></p></div>');

$('#adsDisplay').append('<h3>New Ad</h3>');
$('#adsDisplay').append('<div><table><tr><td><input size="75" type="text" name="admsg"></td><td><input type="text" name="addelay" size="10"></td></tr><tr><td>Message</td><td>Delay</td></tr></table></div>');

$('#blacklistDisplay').append('<h3>New Blacklist Entry</h3>');
$('#blacklistDisplay').append('<div><table><tr><td><input type="text" name="userid" size="25"></td><td><input type="text" name="reason" size="60"></td></tr><tr><td>Userid</td><td>Reason</td></tr></table></div>');

$('#eventsDisplay').append('<h3>New Event</h3>');
$('#eventsDisplay').append('<div><table><tr><td>Use Name&nbsp;&nbsp;<input name="include_name" type="checkbox" value="1" /></td><td>Random Saying&nbsp;&nbsp;<input name="useSaying" type="checkbox" value="1" /></td></tr><tr><td><select name="event">\
<option value="#dj_added">Dj Added</option>\
<option value="#dj_removed">Dj Removed</option>\
<option value="#user_entered">User Entered</option>\
<option value="#room_updated">Room Updated</option>\
<option value="#user_booted">User Booted</option>\
<option value="#song_snagged">Song Snagged</option>\
<option value="#user_left">User Left</option>\
<option value="#song_lamed">Song Lamed</option>\
</select></td><td><select name="delivery_method"><option value="0" >Private Message</option><option value="1" selected="true">Chat Room</option></select></td><td><input class="input_field" name="pre_text" type="text" size="25" /></td><td><input class="input_field" name="post_text" type="text" size="25" /></td></tr><tr><td>Event</td><td>Delivery</td><td>Pre Name Text</td><td>Post Name Text</td></tr></table></div>');

$('#aclsDisplay').append('<h3>New Access Control</h3>');
$('#aclsDisplay').append('<div><table><tr><td>Access Level&nbsp;&nbsp;<select name="access_level"><option>1</option><option>2</option><option>3</option></td></tr><tr><td><input class="input_field" type="text" name="userid" size="25%"></td><td><input class="input_field" type="text" name="comment" size="55%"></td></tr><tr><td>Userid</td><td>Name / Comment</td></tr></table></div>');


      dataAJAX['adverts'].pop();
       dataAJAX['triggers'].pop();
       dataAJAX['blacklist'].pop();
       dataAJAX['events'].pop();
       dataAJAX['acl'].pop();
       dataAJAX['adverts'].forEach(appendToAds_loop);
       dataAJAX['triggers'].forEach(appendToTriggers_loop);
       dataAJAX['blacklist'].forEach(appendToBlacklist_loop);
       dataAJAX['events'].forEach(appendToEvents_loop);
       dataAJAX['acl'].forEach(appendToACL_loop);

       $( "#adsDisplay" ).accordion({collapsible: true, active: false, heightStyle: "content" });
       $( "#triggersDisplay" ).accordion({collapsible: true, active: false, heightStyle: "content" });
       $( "#blacklistDisplay" ).accordion({collapsible: true, active: false, heightStyle: "content" });
       $( "#eventsDisplay" ).accordion({collapsible: true, active: false, heightStyle: "content" });
       $( "#aclsDisplay" ).accordion({collapsible: true, active: false, heightStyle: "content" });



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



}

function pullAJAX() {
  $.get(
    "/websockets/pull.php",
    {bot_userid : botid, magic_key : magicKey},
    function(data) {
       dataAJAX = json_parse(data);
        a_refresh_ajax();

  //alert($("#triggersDisplayWrapper").html()); 
       
       var button = $(".ui-dialog-buttonpane button:contains('Continue')");
       setTimeout(function(){$(button).button("enable")}, 500); // One Extra Second :)
    }
);
}

function a_pre_pull_ajax() {
$('#triggersDisplayWrapper').html('<div id="triggersDisplay" style="margin-left: auto; margin-right: auto"></div>');
$('#adsDisplayWrapper').html('<div id="adsDisplay" style="margin-left: auto; margin-right: auto"></div>');
$('#blacklistDisplayWrapper').html('<div id="blacklistDisplay" style="margin-left: auto; margin-right: auto"></div>');
$('#eventsDisplayWrapper').html('<div id="eventsDisplay" style="margin-left: auto; margin-right: auto"></div>');
$('#aclsDisplayWrapper').html('<div id="aclsDisplay" style="margin-left: auto; margin-right: auto"></div>');  
}

function refreshAJAX(){

a_pre_pull_ajax();

pullAJAX();
}

function updateServer(){
  switch($( "#tabs" ).tabs( "option", "active" )) {
    case 2:
          $("#basics").submit();
          break;
    case 3:
          $("#triggers").submit();
          break;
    case 4:
          $("#events").submit();
          break;
    case 5:
          $("#ads").submit();
          break;
    case 6:
          $("#acl").submit();
          break;
    case 7:
          $("#blacklist").submit();
          break;
    case 8:
          $("#botstats").submit();
          break;
<?php
    if(in_array("B",$flags)){echo'case 9: $("#pkgb").submit(); break;'; }
    if(in_array("C",$flags)){echo'case 10: $("#pkgc").submit(); break;'; }
    if(in_array("D",$flags)){echo'case 11: $("#pkgd").submit(); break;'; }
    if(in_array("E",$flags)){echo'case 12: $("#pkge").submit(); break;'; }
    if(in_array("F",$flags)){echo'case 13: $("#pkgf").submit(); break;'; }
?>
  }
}

//End Seperate

// DOM Ready
$(function() {
    //Setup Jquery Vert Tabs
    $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
    $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
    $( "#tabs" ).tabs({ active: 2});
    $( "input[type=submit], button" ).button().click(function( event ) {event.preventDefault();});
    //Turn on Tooltips
    $( document ).tooltip();
    $("tr:odd").css("background-color", "#222222");
    
$("#color-picker").spectrum({
    color: "#000000",
    showInput: true,
    className: "full-spectrum",
    showInitial: true,
    showPalette: true,
    showSelectionPalette: true,
    maxPaletteSize: 10,
    preferredFormat: "hex6",
    localStorageKey: "spectrum.demo",
    move: function (color) {
        
    },
    show: function () {
    
    },
    beforeShow: function () {
    
    },
    hide: function () {
    
    },
    change: function() {
        
    },
    palette: [
        ["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)",
        "rgb(204, 204, 204)", "rgb(217, 217, 217)","rgb(255, 255, 255)"],
        ["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
        "rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"], 
        ["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)", 
        "rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)", 
        "rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)", 
        "rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)", 
        "rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)", 
        "rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
        "rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
        "rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
        "rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)", 
        "rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)"]
    ]
});


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
    <?php
    if(in_array("B",$flags)){echo "$('#pkgb').ajaxForm(ajaxOptions);"; }
    if(in_array("C",$flags)){echo "$('#pkgc').ajaxForm(ajaxOptions);"; }
    if(in_array("D",$flags)){echo "$('#pkgd').ajaxForm(ajaxOptions);"; }
    if(in_array("E",$flags)){echo "$('#pkge').ajaxForm(ajaxOptions);"; }
    if(in_array("F",$flags)){echo "$('#pkgf').ajaxForm(ajaxOptions);"; }
    ?>
      $('#misc').ajaxForm(ajaxOptions);

      $('#sayingsButton').popupWindow({
    windowURL: 'http://'+http_host+'/console/update_sayings.html?magic_key='+magicKey,
    height: 600,
    width: 600    });
 
      $('#changeEmailButton').popupWindow({
    windowURL: 'http://'+http_host+'/console/update_email.html?magic_key='+magicKey,
    height: 600,
    width: 600    });

      $('#changePasswordButton').popupWindow({
    windowURL: 'http://'+http_host+'/console/update_password.html?magic_key='+magicKey,
    height: 600,
    width: 600    });

      $('#resetStatsButton').popupWindow({
    windowURL: 'http://'+http_host+'/console/reset_stats.html?magic_key='+magicKey,
    height: 600,
    width: 600    });

 $('#basics_botuserid').val(getBotId());
 $('#basics_botauthid').val(getAuthId());
 $('#basics_botroomid').val(getRoomId());
 $('#basics_botownerid').val(getOwnerId());
 $('#basics_commandtrigger').val(getTrigger());

$("#logwindow").css("height", $(window).height() - 200);
$( "#storeMenu" ).accordion({active: false, heightStyle: "content"});
$( "#pkgb_accordion" ).accordion({active: false, heightStyle: "content"});
    //Connect WebSocket Last
    connectSocketController();
  });

</script>
</head>

<body>
<div id="sending-updates" title="Sending Updates" style="display: none;">
<p>Sending Updates</p>
</div>

<div id="unlock-basics" title="Unlocking Basics" style="display: none;">
<p>Unlocking Basics.  Be careful and make sure to click update and refresh when done.</p>
</div>
<div id="wrapper">

	<header id="header">
	<br />
	<br />
	<font style="font-size:400%; font-weight: 700; letter-spacing: 10px">neuroBots</font>
	</header><!-- #header-->

	<div id="content">

<div style="padding: 5px 0px;" align="right"><button onClick="JavaScript:updateServer();">Update</button>&nbsp;<button onClick="JavaScript:startBot();">Start</button>&nbsp;<button onClick="JavaScript:stopBot();">Stop</button>&nbsp;<button onClick="JavaScript:logout();">Logout</button>&nbsp;&nbsp;<font id="email" style="font-size: 18px; vertical-align:bottom; "><?php include("test.php"); ?></font>&nbsp;&nbsp;<img src="/images/off.png" style="vertical-align:bottom;" id="bot_status" title="Bot Status">&nbsp;<img src="/images/off.png" style="vertical-align:bottom;" id="connection_status" title="Connection Status">&nbsp;&nbsp;<font id="uptime" style="font-size: 18px; vertical-align:bottom; ">Uptime: Unknown</font></div>

<div id="tabs">
  <ul>
    <li><a href="#000">Store</a></li>
    <li><a href="#001">Log</a></li>
    <li><a href="#002">General Settings</a></li>
    <li><a href="#003">Triggers</a></li>
    <li><a href="#004">Events</a></li>
    <li><a href="#005">Advertisements</a></li>
    <li><a href="#006">Access Controls</a></li>
    <li><a href="#007">Blacklist</a></li>
    <li><a href="#008">Room Description</a></li>
    <?php 
    if(in_array("B",$flags)){echo'<li><a href="#009">Addon Package B</a></li>';}
    if(in_array("C",$flags)){echo'<li><a href="#010">Addon Package C</a></li>';}
    if(in_array("D",$flags)){echo'<li><a href="#011">Addon Package D</a></li>';}
    if(in_array("E",$flags)){echo'<li><a href="#012">Addon Package E</a></li>';}
    if(in_array("F",$flags)){echo'<li><a href="#013">Addon Package F</a></li>';}
    ?>
 </ul>
   <div id="000">
    <div id="storeMenu">
  <h3>Package A</h3>
  <div>
    <p>Everything you need to run a basic bot.</p>
    <img src="/images/ok.png" style="float:right" />
    <h3>Cost: Free!</h3>
  </div>
  <h3>Package B</h3>
  <div>
    <ul>
      <li>Anti-idle</li>
      <li>User Search</li>
      <li>Album Lookup</li>
      <li>Random 8-ball Responses</li>
      <li>Horoscopes</li>
      <li>Weather</li>
      <li>Dice Roll</li>
      <li>New Raspberry Theme for Stats</li>
    </ul>
    <?php
    if(in_array("B",$flags)){
      echo '<img src="/images/ok.png" style="float:right" />';
    }else{
      echo'<div style="float: right">
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="9SXPAH9VFCJJA">
        <table>
        <tr><td><input type="hidden" name="on0" value="botid"></td></tr><tr><td><input type="hidden" name="os0" maxlength="200" value="'.$bot_userid.'"></td></tr>
        </table>
        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribe_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
        </div>';
    } 
?>
    <h3>Cost: $5 month</h3>
  </div>
  <h3>Package C</h3>
    <div>
    <ul>
      <li>Song Stuck For Others</li>
      <li>Iron Curtain</li>
      <li>Pull Tabs</li>
      <li>More Stats ( Last 3 hours, Last 24 hours, Last 7 Days, Last Month )</li>
      <li>New (insert name) Theme for Stats</li>
    </ul>
        <?php
    if(in_array("C",$flags)){
      echo '<img src="/images/ok.png" style="float:right" />';
    }else{
      echo '<div style="float: right">
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
        <input type="hidden" name="cmd" value="_s-xclick">
        <input type="hidden" name="hosted_button_id" value="LTGSQ45PJEANS">
        <table>
        <tr><td><input type="hidden" name="on0" value="botid"></td></tr><tr><td><input type="hidden" name="os0" maxlength="200" value="'.$bot_userid.'"></td></tr>
        </table>
        <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribe_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
        <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
        </form>
        </div>';
    }
?>
    <h3>Cost: $5 month</h3>
  </div>
  <h3>Package D</h3>
  <div>
    <p>Coming Soon!</p>
    <?php
    if(in_array("D",$flags)){
      echo '<img src="/images/ok.png" style="float:right" />';
    }else{
    echo '<div style="float: right">
          <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
          <input type="hidden" name="cmd" value="_s-xclick">
          <input type="hidden" name="hosted_button_id" value="25RKA56A3QHWJ">
          <table>
          <tr><td><input type="hidden" name="on0" value="botid"></td></tr><tr><td><input type="hidden" name="os0" maxlength="200" value="'.$bot_userid.'"></td></tr>
          </table>
          <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribe_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
          <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
          </form>
          </div>';
    }
    ?>

    <h3>Cost: $10 month</h3>
  </div>
</div>
 </div>
  <div id="001">
    <div class="logwindow" id="logwindow" style="height: 450px; overflow-y: scroll;"></div>
 </div>
  <div id="002">
    <form id="basics" action="/websockets/push.php" method="post">
      
      <script type="text/javascript">
        document.write('<input type="hidden" name="bot_userid" value="'+getBotId()+'">');
        document.write('<input type="hidden" name="magic_key" value="'+getMagicKey()+'">');
      </script>
      <input type="hidden" name="command" value="update_basics">

    <br /><br />
    <div id="002_left" style="width: 50%; float: left" align="center">

                    <table style="basics">
                    <tr><td style="text-align:right">Bot Userid&nbsp;&nbsp;</td><td><input class="input_field" id="basics_botuserid" type="text" name="bot_userid" size="25" readonly="readonly"></td></tr>
                    <tr><td style="text-align:right">Bot Authid&nbsp;&nbsp;</td><td><input class="input_field" id="basics_botauthid" type="text" name="bot_authid" size="25" readonly="readonly"></td></tr>
                    <tr><td style="text-align:right">Bot Roomid&nbsp;&nbsp;</td><td><input class="input_field" id="basics_botroomid" type="text" name="bot_roomid" size="25" readonly="readonly"></td></tr>
                    <tr><td style="text-align:right">Owner Userid&nbsp;&nbsp;</td><td><input class="input_field" id="basics_botownerid" type="text" name="bot_ownerid" size="25" readonly="readonly"></td></tr>
                    <tr><td style="text-align:right">Command Prefix&nbsp;&nbsp;</td><td><input class="input_field" id="basics_commandtrigger" type="text" name="trigger" size="25" readonly="readonly"></td></tr>
                    </table>
    </div>
    <div id="002_right" style="width: 50%; float: left" align="center">
      <table>
      <tr><td>&nbsp;&nbsp;Give Moderators Level 1 Access&nbsp;&nbsp;</td><td><input type="checkbox" id='modsToLvl1' name="modsToLvl1" value="1" /></td></tr>
      <tr><td>&nbsp;&nbsp;Start With Slide On</td><td><input type="checkbox" id='slideSwitch' name="startSlide" value="1" /></td></tr>
      <tr><td>&nbsp;&nbsp;Start With Queue On</td><td><input type="checkbox" id='queueSwitch' name="startQueue" value="1" /></td></tr>
      <tr><td>&nbsp;&nbsp;Start With Stats On</td><td><input type="checkbox" id='statsSwitch' name="startStats" value="1" /></td></tr>
      <tr><td>&nbsp;&nbsp;Start With AutoDJ On</td><td><input type="checkbox" id='autoDjSwitch' name="startAutoDj" value="1" /></td></tr>
      <tr><td>&nbsp;&nbsp;Dj Alone</td><td><input type="checkbox" id='aloneDjSwitch' name="switchAloneDj" value="1" /></td></tr>
      <tr><td>&nbsp;&nbsp;Auto Re-Queue If Queue Isn't Empty&nbsp;&nbsp;</td><td><input type="checkbox" id='autoReQueueSwitch' name="switchAutoRequeue" value="1" /></td></tr>
      </table>
    </div>
  </form>
  <p>&nbsp;</p>
  <p><center><script type="text/javascript">document.write('<h3><a target="_blank" href="http://stats.neurobots.net/modstats.php?id='+getId()+'">Link To Your Public Mod Stats</a></h3>');</script></center></p>
  <p><center><script type="text/javascript">document.write('<h3><a target="_blank" href="http://stats.neurobots.net/botstats.php?id='+getId()+'">Link To Your Public Bot Stats</a></h3>');</script></center></p>
   <center><button onClick="JavaScript:unlockBasics();">Unlock Fields</button>&nbsp;<button id="sayingsButton">Upload Sayings</button>&nbsp;<button id="changeEmailButton">Change E-mail Address</button>&nbsp;<button id="changePasswordButton">Change Password</button>&nbsp;<button id="resetStatsButton">Reset Stats</button></center>
</div>
  <div id="003">
    <form id="triggers" action="/websockets/push.php" method="post">
      <script type="text/javascript">
        document.write('<input type="hidden" name="bot_userid" value="'+getBotId()+'">');
        document.write('<input type="hidden" name="magic_key" value="'+getMagicKey()+'">');
      </script>
      <input type="hidden" name="command" value="update_triggers">
        <div id="triggersDisplayWrapper">
          <div id="triggersDisplay" style="margin-left: auto; margin-right: auto">
          </div>
        </div>
    </form>
</div>
  <div id="004">
    <form id="events" action="/websockets/push.php" method="post">
      <script type="text/javascript">
        document.write('<input type="hidden" name="bot_userid" value="'+getBotId()+'">');
        document.write('<input type="hidden" name="magic_key" value="'+getMagicKey()+'">');
      </script>
      <input type="hidden" name="command" value="update_events">
        <div id="eventsDisplayWrapper">
          <div id="eventsDisplay" style="margin-left: auto; margin-right: auto">
          </div>
        </div>           
    </form>
</div>
  <div id="005">
    <form id="ads" action="/websockets/push.php" method="post">
      <script type="text/javascript">
        document.write('<input type="hidden" name="bot_userid" value="'+getBotId()+'">');
        document.write('<input type="hidden" name="magic_key" value="'+getMagicKey()+'">');
      </script>
      <input type="hidden" name="command" value="update_ads">
        <div id="adsDisplayWrapper">
          <div id="adsDisplay" style="margin-left: auto; margin-right: auto">
          </div>
        </div>
    </form>
</div>
  <div id="006">
    <form id="acl" action="/websockets/push.php" method="post">
      <script type="text/javascript">
        document.write('<input type="hidden" name="bot_userid" value="'+getBotId()+'">');
        document.write('<input type="hidden" name="magic_key" value="'+getMagicKey()+'">');
      </script>
      <input type="hidden" name="command" value="update_acl">
        <div id="aclsDisplayWrapper">
          <div id="aclsDisplay" style="width: 90%; margin-left: auto; margin-right: auto">
          </div>
        </div>
    </form>
</div>
  <div id="007">
    <form id="blacklist" action="/websockets/push.php" method="post">
      <script type="text/javascript">
        document.write('<input type="hidden" name="bot_userid" value="'+getBotId()+'">');
        document.write('<input type="hidden" name="magic_key" value="'+getMagicKey()+'">');
      </script>
      <input type="hidden" name="command" value="update_blacklist">
        <div id="blacklistDisplayWrapper">
          <div id="blacklistDisplay" style="margin-left: auto; margin-right: auto">
          </div>
        </div>
      </form>
</div>
  <div id="008">
    <form id="botstats" action="/websockets/push.php" method="post">
      <script type="text/javascript">
        document.write('<input type="hidden" name="bot_userid" value="'+getBotId()+'">');
        document.write('<input type="hidden" name="magic_key" value="'+getMagicKey()+'">');
      </script>
      <input type="hidden" name="command" value="update_botstats">
      <center><h3>Theme Settings</h3></center>
        <center>
        Background Color&nbsp;<input type="text" id="color-picker" name="roomDescrBgColor">&nbsp;&nbsp;Color Theme&nbsp;
          <select name="statsTheme" id="statsTheme">
            <option>dark-hive</option>
            <option>cupertino</option>
            <option>mint-choc</option>
            <option>south-street</option>
            <option>blitzer</option>
            <option>smoothness</option>
            <option>le-frog</option>
            <option>flick</option>
           <option>humanity</option>
          </select>&nbsp;&nbsp;
        <button onClick="window.open('/images/thumb.jpg','Theme Preview','');">Theme Examples</button>
      </center>
      <br />
    <center><h3>Room Description</h3></center>
    <center><textarea id='roomDescrText' name='roomDescrText' cols="100" rows="20"></textarea></center> 
    <center><div style="font-size: 75%">&lt;br&gt;&lt;font&gt;&lt;a&gt;&lt;img&gt;&lt;p&gt; tags allowed</div></center>
  </form>
</div>
<?php
  if(in_array("B",$flags)){ echo '<div id="009"><form id="pkgb" action="/websockets/push.php" method="post">
      <script type="text/javascript">
        document.write(\'<input type="hidden" name="bot_userid" value="\'+getBotId()+\'">\');
        document.write(\'<input type="hidden" name="magic_key" value="\'+getMagicKey()+\'">\');
      </script>
      <input type="hidden" name="command" value="update_pkgb">
  <div id="pkgb_accordion">
  <h3>Triggers</h3>
  <div>
    Enable Last.fm lookup: <input type = "checkbox" id = "last_fm_e" name = "last_fm_e" value = "1" />&nbsp;&nbsp;Trigger Phrase: <input type="text" size="20" name="last_fm_t" id="last_fm_t" /><br />
    Enable Wikipedia lookup: <input type = "checkbox" id = "wikipedia_e" name = "wikipedia_e" value = "1" />&nbsp;&nbsp;Trigger Phrase: <input type="text" size="20" name="wikipedia_t" id="wikipedia_t" /><br />
    Enable Weather lookup: <input type = "checkbox" id = "weather_e" name = "weather_e" value = "1" />&nbsp;&nbsp;Trigger Phrase: <input type="text" size="20" name="weather_t" id="weather_t" /><br />
    Enable User lookup:  <input type = "checkbox" id = "user_lookup_e" name =" user_lookup_e" value = "1" />&nbsp;&nbsp;Trigger Phrase: <input type="text" size="20" name="user_lookup_t" id="user_lookup_t" /><br />
    Enable Dice (d6) trigger: <input type = "checkbox" id = "dice_6_e" name = "dice_6_e" value = "1" />&nbsp;&nbsp;Trigger Phrase: <input type="text" size="20" name="dice_6_t" id="dice_6_t" /><br />
    Enable Dice (d20) trigger: <input type = "checkbox" id = "dice_20_e" name = "dice_20_e" value = "1" />&nbsp;&nbsp;Trigger Phrase: <input type="text" size="20" name="dice_20_t" id="dice_20_t" /><br />
    Enable 8-Ball trigger: <input type = "checkbox" id = "8ball_e" name = "8ball_e" value = "1" />&nbsp;&nbsp;Trigger Phrase: <input type="text" size="20" name="8ball_t" id="8ball_t" /><br />

  </div>
  <h3>Anti Idle</h3>
  <div>
    Enable: <input type = "checkbox" id = "anti_idle" name = "anti_idle" value = "1" /><br />
    Warning Message: <input type="text" size="54" name="ai_w_msg" id="ai_w_msg" />&nbsp;&nbsp;Timeout: <input type="text" size="5" name="ai_w_msg_t" id="ai_w_msg_t" />&nbsp;&nbsp;&nbsp;&nbsp;
    Removal Message: <input type="text" size="54" name="ai_msg" id="ai_msg" />&nbsp;&nbsp;Timeout: <input type="text" size="5" name="ai_msg_t" id="ai_msg_t" />  
  </div>
  <h3>8 Ball Sayings</h3>
  <div>
    <input type="text" size="54" name="8ball_1" id="8ball_1" />&nbsp;&nbsp;<input type="text" size="54" name="8ball_2" id="8ball_2" /><br />
    <input type="text" size="54" name="8ball_3" id="8ball_3" />&nbsp;&nbsp;<input type="text" size="54" name="8ball_4" id="8ball_4" /><br />
    <input type="text" size="54" name="8ball_5" id="8ball_5" />&nbsp;&nbsp;<input type="text" size="54" name="8ball_6" id="8ball_6" /><br />
    <input type="text" size="54" name="8ball_7" id="8ball_7" />&nbsp;&nbsp;<input type="text" size="54" name="8ball_8" id="8ball_8" /><br />
    <input type="text" size="54" name="8ball_9" id="8ball_9" />&nbsp;&nbsp;<input type="text" size="54" name="8ball_10" id="8ball_10" /><br />
    <input type="text" size="54" name="8ball_11" id="8ball_11" />&nbsp;&nbsp;<input type="text" size="54" name="8ball_12" id="8ball_12" /><br />
    <input type="text" size="54" name="8ball_13" id="8ball_13" />&nbsp;&nbsp;<input type="text" size="54" name="8ball_14" id="8ball_14" /><br />
    <input type="text" size="54" name="8ball_15" id="8ball_15" />&nbsp;&nbsp;<input type="text" size="54" name="8ball_16" id="8ball_16" /><br />
    <input type="text" size="54" name="8ball_17" id="8ball_17" />&nbsp;&nbsp;<input type="text" size="54" name="8ball_18" id="8ball_18" /><br />
    <input type="text" size="54" name="8ball_19" id="8ball_19" />&nbsp;&nbsp;<input type="text" size="54" name="8ball_20" id="8ball_20" /><br />
  </div>
  <h3>Horoscopes</h3>
  <div>
  Enable: <input type = "checkbox" id = "horoscope_e" name = "horoscope_e" value = "1" /><br /><br />
    <script>document.write( getTrigger()+"aries<br />" );</script>
    <script>document.write( getTrigger()+"taurus<br />" );</script>
    <script>document.write( getTrigger()+"gemini<br />" );</script>
    <script>document.write( getTrigger()+"cancer<br />" );</script>
    <script>document.write( getTrigger()+"leo<br />" );</script>
    <script>document.write( getTrigger()+"virgo<br />" );</script>
    <script>document.write( getTrigger()+"libra<br />" );</script>
    <script>document.write( getTrigger()+"scorpio<br />" );</script>
    <script>document.write( getTrigger()+"sagittarius<br />" );</script>
    <script>document.write( getTrigger()+"capricorn<br />" );</script>
    <script>document.write( getTrigger()+"aquarius<br />" );</script>
    <script>document.write( getTrigger()+"pisces<br />" );</script>
    <p></p>
  </div>
</div>


    </form></div>'; }
  if(in_array("C",$flags)){ echo '<div id="010"><form id="pkgc" action="/websockets/push.php" method="post"></form></div>'; }
  if(in_array("D",$flags)){ echo '<div id="011"><p>All advertisements are blocked.  Thank you for your support!</p></div>'; }
  if(in_array("E",$flags)){ echo '<div id="012"><form id="pkge" action="/websockets/push.php" method="post"></form></div>'; }
  if(in_array("F",$flags)){ echo '<div id="013"><form id="pkgf" action="/websockets/push.php" method="post"></form></div>'; }
  ?>
</div><!-- #tabs -->
	</div><!-- #content-->

</div><!-- #wrapper -->

<footer id="footer">
	<div style="text-align: right; padding: 0 100px 0 0">© 2013 <a href="http://www.neurobots.net/">neuroBots.net</a></div>
</footer><!-- #footer -->

</body>
</html>
