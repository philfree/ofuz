<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/


   include_once("class/RegisteredUser.class.php");
   include_once("class/User.class.php");

  // Registration package configuration
  // Those bellow should not be needed anymore.
  // $GLOBALS['cgf_reg_user_table'] = "referee";
  // $GLOBALS['cgf_reg_user_primarykey'] = "idreferee";
  // $GLOBALS['cfg_admin_user_table'] = "clubs";
  // $GLOBALS['cfg_admin_user_primarykey'] = "idclub";
  
   define("RADRIA_LOG_RUN_REGISTRATION", true);
  
?>
