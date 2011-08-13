<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

  /**
   * Template web pages 
   */
  include_once("config.php"); 
   $pageTitle   ="Content Administration - Email templates";
   $helptext    = "Click on the update, delete or add links to manage emails templates.";
   $previousur  = "./index.php";
   $adminhome   = "admin.php";
   $reloadurl   =  "adminemailtemplates.php";
   $helpurl     = "docs/mailingtools/";
   $bpreviousurl  = true ;
   $badminhome    = true;
   $breloadurl    = true;
   $bhelpurl      = true;
  include("includes/adminheader.inc.php");  

 if ($_SESSION['iscontentadmin']) {

  $do_emailtemplate = new DataObject($GLOBALS['conx'], "emailtemplate");
  $fields = new Registry($GLOBALS['conx'], "emailtemplate"); 
  $fields->bodyhtml->hidden = 1;
  $fields->bodytext->hidden = 1;
  $fields->senderemail->hidden = 1;
  $fields->sendername->hidden = 1;
  $fields->internal->hidden = 1;
  $fields->idemailtemplate->label = "id";

  $do_emailtemplate->setRegistry($fields);
  $do_emailtemplate->prepareView(Array("formpage"=>"adminformrecordedit.php"));
  $do_emailtemplate->view();


  // $r_emailtemplates = new Report($conx, "mailingtools.ListEmailTemplates") ;
  // $r_emailtemplates->execute() ;
 
}
 include("includes/adminfooter.inc.php") ; 
 ?>