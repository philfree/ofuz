<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

//$GLOBALS['cfg_full_path'] = '/server/vhtdocs/ofuz.net/';

include_once('config.php');
$do_contactwebsite = new ContactWebsite($GLOBALS['conx']);
$do_contactwebsite->insertNoteForAutoFetchOn();

?>
