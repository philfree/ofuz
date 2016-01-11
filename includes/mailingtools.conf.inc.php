<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

	/**
         * referee assigning configuration file
         */

       include_once("class/Emailer.class.php") ;
       include_once("Zend/Mail.php");
       include_once("class/Radria_Emailer.class.php");
       include_once("class/EmailTemplate.class.php");
       include_once("class/MergeString.class.php");
       $GLOBALS['idmailinglist']= 1;
       $GLOBALS['idlist']= 1;
       $GLOBALS['base_url'] = "http://".$_SERVER['HTTP_HOST'].basename($_SERVER['REQUEST_URI']); 

?>
