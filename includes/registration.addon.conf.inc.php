<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

       /** 
        *  Registration package
        *  PageBuilder AddOn classes
        *  To display the tool bar we register the
        *  AddOn's in an Array that has the following structure
        *  Array (
        *     'group name' = Array ( 'Addon1 name',
        *                            'Addon2 name',
        *                            ...)
        *         );
        * @package registration
        */
   
  //  if (!empty($_SESSION['openedpage'])) {

//        include_once("class/addon/registration/RegistrationAddOnBase.class.php");
//        include_once("class/addon/registration/RegistrationFormAddOn.class.php");
//        include_once("class/addon/registration/LoginFormAddOn.class.php");
//        include_once("class/addon/registration/ForgotPasswordFormAddOn.class.php");
//        include_once("class/addon/registration/RegistrationFormOpenIdAddOn.class.php");
//        include_once("class/addon/registration/LoginFormOpenIdAddOn.class.php");
//        include_once("class/addon/registration/LogoutAddOn.class.php");
//  
       if(!isset($cfg_pagebuilder_tools)) {
         $cfg_pagebuilder_tools = Array();
         $GLOBALS['cfg_pagebuilder_tools'] = $cfg_pagebuilder_tools;
       }
     
       $GLOBALS['cfg_pagebuilder_tools']['Registration'][] = "RegistrationFormAddOn";
       $GLOBALS['cfg_pagebuilder_tools']['Registration'][] = "LoginFormAddOn";
       $GLOBALS['cfg_pagebuilder_tools']['Registration'][] = "ForgotPasswordFormAddOn";
       $GLOBALS['cfg_pagebuilder_tools']['Registration'][] = "RegistrationFormOpenIdAddOn";
       $GLOBALS['cfg_pagebuilder_tools']['Registration'][] = "LoginFormOpenIdAddOn";
       $GLOBALS['cfg_pagebuilder_tools']['Registration'][] = "LogoutAddOn";
       
      
   // }
     
?>
