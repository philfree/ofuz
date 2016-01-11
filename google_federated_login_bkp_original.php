<?php
require_once(dirname(__FILE__) . '/Zend/OpenId/Consumer.php');
require_once(dirname(__FILE__) . '/Zend/OpenId/Extension/Sreg.php');
$sreg = new Zend_OpenId_Extension_Sreg(array(
 'nickname'=>false,
 'email'=>true,
 'fullname'=>false), null, 1.1);
//echo file_get_contents('https://www.google.com/accounts/o8/id');
$openid_identifier = 'https://www.google.com/accounts/o8/id';
$status = "";
$consumer = new Zend_OpenId_Consumer();
 if (!$consumer->login($openid_identifier, 'google_openid_return.php', null , $sreg)) {
   //echo $consumer->getError();
   $status = "OpenID login failed.";
   //echo $status;exit();
 }

?>