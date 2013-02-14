#!/usr/bin/php -q
<?php
ob_start();

// read from stdin
$fd = fopen("php://stdin", "r");
$email = "";
while (!feof($fd)) {
	$email .= fread($fd, 1024);
}
fclose($fd);
// handle email


if(!empty($email){
$secrate_string = "Ofuz is Open Source";
$date = date("YMDH");
$s_string = "$secrate_string"."$date";
$secrate_key = MD5($s_string);

//open connection
$ch = curl_init();

$url = 'http://www.ofuz.net/ofuz_catch_new.php';
//set the url, POST data
curl_setopt($ch,CURLOPT_URL,$url);

$data = array(
email=>$email,
key=>$secrate_key
);

curl_setopt($ch,CURLOPT_POSTFIELDS,$data);

//execute post
$result = curl_exec($ch);

//close connection
curl_close($ch);
}
?>
