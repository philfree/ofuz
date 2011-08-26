<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

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