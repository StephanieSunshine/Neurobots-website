<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Bot Summary</title>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.1/themes/base/jquery-ui.css" />
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.1/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css" />
<link href="http://fonts.googleapis.com/css?family=Droid+Sans" rel="stylesheet" type="text/css" />


<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-38719169-1']);
  _gaq.push(['_setDomainName', 'neurobots.net']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<?php
require_once('config/db.conf');

$magic_key = '';
$bot_userid = '';
$bg_color = '';
$room_description = '';
$command_trigger = '';
$theme = '';
$flags = '';

if(isset($_GET['id'])) {
	if(preg_match('/^\d+$/',$_GET['id'])) {
		# Get the Basic Info
		foreach ($db->query("SELECT * from users WHERE id='".$_GET['id']."' LIMIT 1") as $row) {
			$magic_key = $row['magic_key'];
			$bot_userid = $row['bot_userid'];
			$bg_color = $row['stats_bg_color'];
			$room_description = $row['room_description'];
			$command_trigger = $row['command_trigger'];
			$theme = $row['stats_color_theme'];
			$flags = $row['flags'];
		}
	


echo "\n\n".'<link rel="stylesheet" href="/juithemes/'.$theme.'/jquery-ui.css" />'."\n";
echo '<link rel="stylesheet" href="/juithemes/'.$theme.'/jquery-ui.min.css" />'."\n";
echo '<link rel="stylesheet" href="/juithemes/'.$theme.'/jquery.ui.theme.css" />'."\n";

echo '
<script>
$(function() {
// 5-mins refresh
setTimeout( function () {location.reload()},  300000 ); 

// Set the room description from the imported js

$( "#tabs" ).tabs();

$( "#commands-tab" ).accordion({ 
collapsible: true,
heightStyle: "content",
active: false
});

$( "#mods-commands-tab" ).accordion({ 
collapsible: true,
heightStyle: "content",
active: false
});

$( "#users-tab" ).accordion({ 
collapsible: true,
heightStyle: "content" 
});

$( "#songs-tab" ).accordion({ 
collapsible: true,
heightStyle: "content" 
});

});

</script>
</head>
<body bgcolor="'.$bg_color.'">';

if(!preg_match('/D/', $flags)){
echo '<div align="center">
<script type="text/javascript"><!--
google_ad_client = "ca-pub-2377570921415131";
/* statsheader */
google_ad_slot = "5228677353";
google_ad_width = 728;
google_ad_height = 90;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></div>';
}

echo '<div id="tabs">
<ul>
<li><a href="#tabs-1">Room Description</a></li>
<li><a href="#tabs-2">Mod Commands</a></li>
<li><a href="#tabs-3">Top Users</a></li>
<li><a href="#tabs-4">Top Songs</a></li>
<li><a href="#tabs-5">Room Stats</a></li>

</ul>
<div id="tabs-1">';
echo html_entity_decode($room_description);
echo '</div>
<div id="tabs-2">

<div id="commands-tab">';
foreach ($db->query("SELECT use_trigger_switch, use_strict_matching, trigger_phrase, command_description from bot_triggers_".$magic_key." where access_level>0") as $row) {
	if ($row['use_trigger_switch'] == 1) { echo "<h3>".$command_trigger.$row['trigger_phrase']."</h3>\n"; } else { echo "<h3>".$row['trigger_phrase']."</h3>\n"; }
	echo '<div><p>'.htmlspecialchars_decode($row['command_description']).'</p></div>';
}
echo'</div>

</div> <!-- End of tabs-2 -->
<div id="tabs-3">

<div id="users-tab">
<h3>Most Awesomes</h3>
<div>
<ul>';
foreach ($db->query('select name, songs_awesomed from bot_ustats_'.$magic_key.' ORDER BY songs_awesomed DESC LIMIT 5') as $row) {
	echo "<li>".$row['name']." with ".$row['songs_awesomed']."</li>";
}
echo '</ul>
</div>
<h3>Most Snags</h3>
<div>
<ul>';
foreach ($db->query('select name, songs_snagged from bot_ustats_'.$magic_key.' ORDER BY songs_snagged DESC LIMIT 5') as $row) {
	echo "<li>".$row['name']." with ".$row['songs_snagged']."</li>";
}
echo '</ul>
</div>
<h3>Most Snagged From</h3>
<div>
<ul>';
foreach ($db->query('select name, songs_shared from bot_ustats_'.$magic_key.' ORDER BY songs_shared DESC LIMIT 5') as $row) {
	echo "<li>".$row['name']." with ".$row['songs_shared']."</li>";
}
echo '</ul>
</div>
<h3>Most Plays</h3>
<div>
<ul>';
foreach ($db->query('select name, songs_played from bot_ustats_'.$magic_key.' ORDER BY songs_played DESC LIMIT 5') as $row) {
	echo "<li>".$row['name']." with ".$row['songs_played']."</li>";
}
echo '</ul>
</div>
</div>
</div> <!-- End of tabs-3 -->
<div id="tabs-4">
<div id="songs-tab">
<h3>Most Awesomes</h3>
<div>
<ul>';
foreach ($db->query('select title, artist, times_awesomed from bot_sstats_'.$magic_key.' ORDER BY times_awesomed DESC LIMIT 5') as $row) {
	echo "<li>".$row['title']." by ".$row['artist']." with ".$row['times_awesomed']."</li>";
}
echo '<ul>
</div>
<h3>Most Plays</h3>
<div>
<ul>';
foreach ($db->query('select title, artist, times_played from bot_sstats_'.$magic_key.' ORDER BY times_played DESC LIMIT 5') as $row) {
	echo "<li>".$row['title']." by ".$row['artist']." with ".$row['times_played']."</li>";
}
echo '</ul>
</div>
<h3>Most Snags</h3>
<div>
<ul>';
foreach ($db->query('select title, artist, times_snagged from bot_sstats_'.$magic_key.' ORDER BY times_snagged DESC LIMIT 5') as $row) {
	echo "<li>".$row['title']." by ".$row['artist']." with ".$row['times_snagged']."</li>";
}
echo '</ul>
</div>
<h3>Most Lamed</h3>
<div>
<ul>';
foreach ($db->query('select title, artist, times_lamed from bot_sstats_'.$magic_key.' ORDER BY times_lamed DESC LIMIT 5') as $row) {
	echo "<li>".$row['title']." by ".$row['artist']." with ".$row['times_lamed']."</li>";
}
echo '</ul>
</div>
</div>
</div> <!-- End of tabs-4 -->
<div id="tabs-5">
<h3>Comming Soon</h3>
</div>
</div>
</body>
</html>
';


	
	}else{
		echo "Invalid Bot id\n";
	}
}else{
	echo "Bot id not found\n";
}

?>
