<?php 
// Copyright 2001 - 2007 SQLFusion LLC           info@sqlfusion.com
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

  $eventControler->doForward() ;


?>
