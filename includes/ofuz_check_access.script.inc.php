<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

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
    
    /* This is for api_upgrade_invoice.php */
     if ($_SESSION['upgrade'] == true) {
		$tt = "api_upgrade_invoice.php";
		echo "<script language=\"javascript\">
        window.location.href='$tt';
		</script>";
	 }
    
  }
    
?>
