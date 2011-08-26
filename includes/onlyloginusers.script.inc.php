<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

   /*****
    * Include script to check if users are logged in before opening the page.
    * onlyloginusers.script.inc.php
    * 
    * @param is_authorized will check if the user is authorized by check if admin is set to 1.
    * @param not_login_message  message to display when users are not registered and logged in.
    */
    
    if (empty($not_login_message)) {
        $not_login_message = "You need to log-in or sign in to access this page. This is a restricted page for registrered and approved users only.";
       
    }
    if (!empty($login_page)) { $not_login_message .= "<br/><a href=\"".$login_page."\">Click here to Sign on</a>"; }

    if (!is_array($_SESSION['userdata'])) {
        echo $not_login_message;
        exit;
    } elseif(isset($check_if_authorized)) {
        $userdata = $_SESSION['userdata']; 
        if(!$userdata['isadmin']) {
           echo $not_login_message;
           exit;
        }    
    }
?>
