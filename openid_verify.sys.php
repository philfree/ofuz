<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  /**
   * OpenID landing page and verification
   *
   * @package registration
   * @author Jay Link
   * @version 1.3
   */
//session_start();
require_once('config.php') ;
require_once('Zend/OpenId/Consumer.php');

$eventControler = new EventControler($GLOBALS['conx']) ;
$eventControler->setLogRun(true);
$eventControler->setMyDbPath($cfg_local_mydbdir) ;
$eventControler->setMessagePage("message.php");
$eventControler->setCheckReferer(false);
//echo $_SESSION['openid_userclass'];
if (!isset($_SESSION['openid_userclass'])) {
    $eventControler->setUrlNext("index.php");
    $eventControler->doForward();
    exit();
} else {
    $cur_userclass = $_SESSION['openid_userclass'];
}

$eventControler->setLog("\n--- OpenId Call back --");
$eventControler->setLog("\n userclass:".$_SESSION['openid_userclass']);
$eventControler->setLog("\n openid_mode:".$_GET['openid_mode']);

$events = Array();
$events[50] = $cur_userclass."->eventCheckOpenIdCallBack";
$events[40] = "mydb.gotoPage";

if (isset($_GET['openid_mode'])) {
 
  $eventControler->addParam("goto", "sess_test2.php");
  $eventControler->listenEvents($events) ;
  $eventControler->doForward() ;
  //echo $eventControler->getUrlNext();

}

//unset($_SESSION['openid_userclass']);

?>
