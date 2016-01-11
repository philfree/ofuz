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

/*
//echo file_get_contents('https://www.google.com/accounts/o8/id');
$openid_identifier = 'https://www.google.com/accounts/o8/id';
$status = "";
$consumer = new Zend_OpenId_Consumer();
 if (!$consumer->login($openid_identifier, 'google_openid_return.php', null , $sreg)) {
   //echo $consumer->getError();
   $status = "OpenID login failed.";
 }
*/
$status = "";
if (isset($_POST['openid_action']) &&
    $_POST['openid_action'] == "login" &&
    !empty($_POST['openid_identifier'])) {
    $consumer = new Ak33m_OpenId_Consumer();
    if (!$consumer->login($_POST['openid_identifier'], 'google_federated_login.php', null, $sreg)) {
        $status = "OpenID login failed.";
    }
} else if (isset($_GET['openid_mode'])) {
    if ($_GET['openid_mode'] == "id_res") {
        $consumer = new Ak33m_OpenId_Consumer();
        if ($consumer->verify($_GET, $id, $sreg)) {
            $status = "VALID " . htmlspecialchars($id);
            $data = $sreg->getProperties();

            print_r($data);
        } else {
            $status = "INVALID " . htmlspecialchars($id);
        }
    } else if ($_GET['openid_mode'] == "cancel") {
        $status = "CANCELLED";
    }
}
?>
<html><body>
<?php echo "$status<br>" ?>
<form method="post">
<fieldset>
<legend>OpenID Login</legend>
<input type="text" name="openid_identifier" value=""/>
<input type="submit" name="openid_action" value="login"/>
</fieldset>
</form>
</body></html>