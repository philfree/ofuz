<?php
require_once(dirname(__FILE__) . '/Zend/OpenId/Consumer.php');
require_once(dirname(__FILE__) . '/Zend/OpenId/Ak33m_OpenId_Consumer.php');
require_once(dirname(__FILE__) . '/Zend/OpenId/Extension/Sreg.php');
require_once(dirname(__FILE__) . '/Zend/OpenId/Extension/Ax.php');


$sreg = new Ak33m_OpenId_Extension_Ax(array(
    'firstname'=>true,
    'email'=>true,
    'lastname'=>true,
    'dob'=>true,
    'gender'=>true,
    'postcode'=>true,
    'country'=>true,
    'language'=>true,
    'timezone'=>true
    ), null, 1.1);

if (isset($_GET['openid_mode'])) {

    if ($_GET['openid_mode'] == "id_res") {

        require_once("config.php");
        $consumer = new Ak33m_OpenId_Consumer();
        if ($consumer->verify($_GET, $id, $sreg)) {

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

            $data = $sreg->getProperties();
            $_SESSION["google"]["openid_identity"] = $google_openid_identity;
            $_SESSION["google"]["firstname"] = $data["firstname"];
            $_SESSION["google"]["lastname"] = $data["lastname"];
            $_SESSION["google"]["email"] = $data["email"];
            $_SESSION["google"]["lang"] = $data["en"];
            header('Location: user_glogin.php?message=Welcome to Ofuz Google login. You do not seem to have an Ofuz account linked to your google login.');
            exit();

          }

        } else {

            //$status = "INVALID " . htmlspecialchars($id);
            header('Location: user_login.php?message=Sorry! Google has identified your login as INVALID. Please try with VALID login.');
            exit();

        }

    } else if ($_GET['openid_mode'] == "cancel") {

        //$status = "CANCELLED";
        header('Location: user_login.php?message=You have cancelled login with your Google Account.');
        exit();

    }

} else {

    $openid_identifier = 'https://www.google.com/accounts/o8/id';
    $consumer = new Ak33m_OpenId_Consumer();
    if (!$consumer->login($openid_identifier, 'google_federated_login.php', null, $sreg)) {

        //$status = "OpenID login failed.";
        header('Location: user_login.php?message=Sorry! Login with your Google Account has failed. Please try again.');
        exit();

    }

}
?>