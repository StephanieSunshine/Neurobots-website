<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>neuroBots</title>
<meta name="keywords" content="turntable, turntable.fm, bots, turntable bots, turntable.fm bots, " />
<meta name="description" content="Neurobots is a platform to make using bots with turntable easy!" />
<link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css' />
	<link href="/public.css" type="text/css" rel="stylesheet" /> 
	<script type="text/javascript" src="/js/jquery.min.js"></script> 
	<script type="text/javascript" src="/js/jquery.scrollTo-min.js"></script> 
	<script type="text/javascript" src="/js/jquery.localscroll-min.js"></script> 
	<script type="text/javascript" src="/js/init.js"></script> 
    

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
    
    <link rel="stylesheet" href="/css/slimbox2.css" type="text/css" media="screen" /> 
    <script type="text/JavaScript" src="/js/slimbox2.js"></script> 
    <script type="text/JavaScript" src="/js/verimail.js"></script>
    <script type="text/JavaScript">
/*
 * A JavaScript implementation of the RSA Data Security, Inc. MD5 Message
 * Digest Algorithm, as defined in RFC 1321.
 * Copyright (C) Paul Johnston 1999 - 2000.
 * Updated by Greg Holt 2000 - 2001.
 * See http://pajhome.org.uk/site/legal.html for details.
 */
 
/*
 * Convert a 32-bit number to a hex string with ls-byte first
 */

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


    </script>
</head> 
<body> 
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
                  echo file_get_contents('config/motd.conf'); 
                } else { 
                  echo '<h2>Welcome</h2>
                  <p>Welcome to neuroBots.  Here room owners can obtain a robot for the 
                  <a href="http://turntable.fm/">Turntable.fm</a> service with ease to help in the moderation of their room.</p>
                  <p>Before you begin, please create a new user with the turntable.fm service for your robot.  Then if you don\'t have it already, go 
                  <a href="http://alaingilbert.github.com/Turntable-API/bookmarklet.html">here</a> and use the bookmarklet to obtain the userid, the authid, 
                  and the roomid of your bot and room on turntable.fm.</p><p>Once you have those, creating a robot takes no time at all.  When you are ready, 
                  click <a href="#Create">here</a> to begin creating your robot.</p>'; 
                } 
                ?>
                </div>
            
            <div id="home_gallery" class="box no_mr">
                <a href="" rel=""><img src="http://i.imgur.com/0oLWxcv.jpg" alt="image 1" /></a>
                <a href="" rel=""><img src="http://i.imgur.com/EwiAUdM.jpg" alt="image 2" /></a>
                <a href="" rel="" class="no_mr"><img src="http://i.imgur.com/5KasUz7.jpg" alt="image 3" /></a>
                <a href="" rel=""><img src="http://i.imgur.com/uhIxOdJ.jpg" alt="image 4" /></a>
                <a href="" rel=""><img src="http://i.imgur.com/9XPT95e.jpg" alt="image 5" /></a>
                <a href="" rel="" class="no_mr"><img src="http://i.imgur.com/e3K3nCo.jpg" alt="image 6" /></a>
            </div>
            <?php
            if($auth) {
            echo '<a href="/console/"><div class="box home_box1 color1">
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <Center><h3>Console</h3></center>
            </div></a>
            <a href="http://forum.neurobots.net/"><div class="box home_box1 color2">
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <Center><h3>Forum</h3></center>
            </div></a>';            
            }else{
            echo '<a href="#Create"><div class="box home_box1 color1">
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <Center><h3>Create</h3></center>
            </div></a>
            <a href="#Login"><div class="box home_box1 color2">
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <Center><h3>Login</h3></center>
            </div></a>';
            }
            ?>
            <a href="http://wiki.neurobots.net/"><div class="box home_box1 color3">
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <Center><h3>Wiki</h3></center>
            </div></a>

            <a href="#Contact"><div class="box home_box1 color4">
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <Center><h3>Contact</h3></center>
            </div></a>

            <a href="#Payment"><div class="box home_box1 color1 no_mr">
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <div class="clear h20"></div>
              <Center><h3>Payment</h3></center>
            </div></a>
               
        </div> <!-- END of home -->
        
        <div class="section section_with_padding" id="Create"> 
            <h2>Create a bot</h2>
            <div class="half left"> 
               <p>Please come find us in <a href="http://tt.fm/express_yourself9">Express Yourself</a> to setup a new bot.</p> 
            </div>
            
            <div class="half right">

                <div class="clear h20">
                <center><h3 style="color: red" id="form_error_message"></h3></center>
                </div>
                

                
            </div> 
            <a href="#home" class="slider_nav_btn home_btn">home</a> 

        </div> 
        <div class="section section_with_padding" id="Login"> 
            <h2>Login</h2> 
            <div class="half left">
                <div id="contact_form" style="width: 400px">
                    <form method="post" name="login" action="login.php">
                        <div class="left">
                            <label for="email">Email:</label> <input name="email" type="text" class="input_field" id="login" maxlength="64" style="width: 180px"/>
                        </div>
                        <div class="right">                           
                            <label for="password">Password:</label> <input name="password" type="password" class="input_field" id="password" maxlength="64" style="width: 180px"/>
                        </div>
                        <div class="clear"></div>
                        <input type="submit" class="submit_btn float_l" name="submit" id="submit" value="Login" onclick="" />
                    </form>
                </div>
            </div>
            
            <div class="half right">

                <div class="clear h20"></div>
                <center><h3 style="color: red" id="form_error_message"><?php if(isset($_SESSION['invalidlogin'])) echo "Invalid Credentials."; ?></h3></center>
            </div>                    
            <a href="#home" class="slider_nav_btn home_btn">home</a> 

        </div> 

        <div class="section section_with_padding" id="Payment"> 
            <h2>Payment</h2> 
            <div class="half left">
            <div class="clear h20"></div>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="G8QBG6LHVEV3E">
<table>
<tr><td><input type="hidden" name="on0" value="Turntable Name">Turntable Name</td></tr><tr><td><input type="text" name="os0" maxlength="200"></td></tr>
</table>
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_subscribe_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>


            </div>
            
            <div class="half right">

                <div class="clear h20"></div>
                

                
            </div>                    
            <a href="#home" class="slider_nav_btn home_btn">home</a> 

        </div> 

        <div class="section section_with_padding" id="Contact"> 
            <h2>Contact</h2> 
            <center>
                <h4>Contact Us</h4>
                <p>If you submitted to this form recently and did not receive a reply, please submit again, there was a bug in the code and I didn't get anyones return email address.</p>
                <p>If you would like to beta test a bot, or you have lost your information please send me a private message @ Princess Ponyo on Turntable.</p>
                <p>The quickest way to find us is on turntable in the <a href="http://turntable.fm/express_yourself9">Express Yourself</a> room.  However, if we can't be found feel free to send us a message.</p>
                <div id="contact_form">
                    <form method="get" name="contact" action="/mail.php" enctype="text/plain">
                        <div class="left">
                            <label for="author">Name:</label> <input name="author" type="text" class="input_field" id="author" maxlength="40" />
                        </div>
                        <div class="right">                           
                            <label for="email">Email:</label> <input name="email" type="text" class="input_field" id="email" maxlength="40" />
                        </div>
                        <div class="clear"></div>
                        <label for="text">Message:</label> <textarea id="text" name="text" rows="10" cols="60"></textarea>
                        <input type="submit" class="submit_btn float_l" name="submit" id="submit" value="Send" />
                    </form>
                </div>
            </div>
            

                

                
            </center>
			<a href="#home" class="slider_nav_btn home_btn">home</a> 
             
        </div> 
    </div> 
</div>
</div>
<div id="templatemo_footer">
    Copyright © 2013 <a href="#">neuroBots.net</a> | Template designed by <a href="http://www.templatemo.com" target="_parent">Free CSS Templates</a>
</div>

</body> 
</html>
