<?php

$to = 'neurobotsnet@gmail.com';
$headers = 'From: '. htmlspecialchars($_GET['email']) . "\r\n";

mail($to, htmlspecialchars($_GET['author']) . " submitted a web form", htmlspecialchars($_GET['email']) . "\r\n". htmlspecialchars($_GET['text']), $headers); 
header('Location: http://www.neurobots.net/');
?>