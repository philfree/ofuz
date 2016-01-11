<?php

if (isset($_GET['openid_mode'])) {
  require_once("config.php");
  require_once(dirname(__FILE__) . '/Zend/OpenId/Consumer.php');
  require_once(dirname(__FILE__) . '/Zend/OpenId/Extension/Sreg.php');

  $sreg = new Zend_OpenId_Extension_Sreg(array(
  'nickname'=>false,
  'email'=>false,
  'fullname'=>false), null, 1.1);

  $consumer = new Zend_OpenId_Consumer();

  if ($_GET['openid_mode'] == "id_res") {
    if ($consumer->verify($_GET, $id, $sreg)) {
      //$status = "Status : VALID <br /> Identifier : " . htmlspecialchars($id)."<br>\n";
      $array = preg_split("/[\s]*[=][\s]*/", $id);
      $google_openid_identity = $array[1];
      
      $do_user_openid = new User();
      $do_user_openid->setRegistry(false);
      $do_user_openid->googleOpenIdIdentityExists($google_openid_identity);
      if($do_user_openid->getNumRows()){
        $do_user_openid->setSessionVariable();
        header('Location: index.php');
        exit();
      } else {
        $_SESSION["google_openid_identity"] = $google_openid_identity;
        header('Location: user_glogin.php?message=Welcome to Ofuz Google login. You do not seem to have an Ofuz account linked to your google login.');
        exit();
      }
      //print_r($array);
      /*$data = $sreg->getProperties();
      if (isset($data['nickname'])) {
        echo "nickname: " . htmlspecialchars($data['nickname']) . "<br>\n";
      }
      if (isset($data['email'])) {
        echo "email: " . htmlspecialchars($data['email']) . "<br>\n";
      }
      if (isset($data['fullname'])) {
        echo "fullname: " . htmlspecialchars($data['fullname']) . "<br>\n";
      }*/
    } else {
      $status = "Status : INVALID " . htmlspecialchars($id);
    }
  } else if ($_GET['openid_mode'] == "cancel") {
      $status = "Status : CANCELLED";
  }
}
?>