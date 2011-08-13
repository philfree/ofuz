<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

   /*****
    * Include script to check if users are logged in before opening the page.
    * 
    */
    
    if (!is_object($_SESSION['do_User'])) {
        $disp = new Display("user_login.php");
        $disp->addParam("message", "Your session has expired, please sign-in again");
        //$disp->addParam("entry", $_SERVER['REQUEST_URI']);
		$_SESSION['entry'] = $_SERVER['REQUEST_URI'];
        header("Location: /".$disp->getUrl());
        exit;
    } 
    if (is_object($_SESSION['do_User'])) {
    try {
      if (!$_SESSION['do_User']->iduser) {
        $disp = new Display("user_login.php");
        $disp->addParam("message", "Error with your user record, please sign-in again");
        //$disp->addParam("entry", $_SERVER['REQUEST_URI']);
		$_SESSION['entry'] = $_SERVER['REQUEST_URI'];
        header("Location: /".$disp->getUrl());
        
       }
     } catch (Exception $e) { 
        $disp = new Display("user_login.php");
        $disp->addParam("message", "Error with your user record, please sign-in again");
        //$disp->addParam("entry", $_SERVER['REQUEST_URI']);
		$_SESSION['entry'] = $_SERVER['REQUEST_URI'];
        header("Location: /".$disp->getUrl());
    }
  }
    
?>
