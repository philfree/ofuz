<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  /**
   * Main event Controler
   * This is an instance of the Event controler that will be managing the execution of the events and set the next url
   * @see EventControler
   * @package RadriaSiteTemplate
   * @author Philippe Lewicki  <phil@sqlfusion.com>
   * @copyright  SQLFusion LLC 2001-2007
   * @version 4.0
   */

   include_once("config.php") ;
   include_once('includes/ofuz_check_access.script.inc.php');
  //  "start event control" ;
  $eventControler = new EventControler($conx) ;
  $eventControler->setMyDbPath($cfg_local_mydbdir) ;
  $eventControler->addparam("dbc", $conx) ;
  $eventControler->addparam("doSave", "yes") ;
  $eventControler->setMessagePage("message.php");
//  If you want to secure your site to only authorize request with local referer
//  comment the line bellow.
//  Notes: doesn't work with ssl or some IE version and Ajax 
  $eventControler->setCheckReferer(false);
  $eventControler->addallvars(); 
  $eventControler->listenEvents($_REQUEST['mydb_events']) ;

  //$eventControler->doForward() ;
  echo $eventControler->doOutput();


?>