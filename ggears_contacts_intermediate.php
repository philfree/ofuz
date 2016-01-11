<?php
include_once('config.php');
include_once("class/OfuzApiClient.class.php");

if($_SESSION['do_User']->api_key == '') {
	$_SESSION['do_User']->autoGenerateAPIKey();
}

$api_key = $_SESSION['do_User']->api_key; //assigning API key

$do_ofuz = new OfuzApiClient();

$iduser = $do_ofuz->setAuth($api_key);

$do_ofuz->format = "json";// json,xml,php

$response = $do_ofuz->get_contacts();
$response = json_decode($response);
$response_status = $response[0];

if($response_status->{'stat'} == 'ok') {
	echo json_encode($response);
} else {
	echo '';
}

?>