<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

  /****
   * Message display page
   * Display the content of $message
   * - param string $message contains the message to display
   *
   * @package PASSiteTemplate
   * @author Philippe Lewicki  <phil@sqlfusion.com>
   * @copyright  SQLFusion LLC 2001-2007
   * @version 4.0
   */


  include_once("config.php") ;
  $pageTitle = "Message" ;
  include("includes/header.inc.php") ;

  if (!empty($_SESSION['message'])) {
    echo htmlentities(stripslashes($_SESSION['message'])) ; 
    $_SESSION['message'] = null;
  } else {
    echo htmlentities(stripslashes($_REQUEST['message'])) ;
  }
?>