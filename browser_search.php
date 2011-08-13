<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

include_once('config.php');
include_once('includes/ofuz_check_access.script.inc.php');
//include_once('includes/header.inc.php');
//the browser_search.php will instantiate an EventControler object, set the search param, goto and pass it to the $_SESSION['do_contact']->eventSetSearch($eventcontroler);

$do_Contacts = new Contact();
$do_Contacts->sessionPersistent("do_Contacts", "index.php", 36000);
$ec_search = new EventControler($conx);
$ec_search->addParam("goto", "contacts.php");
$ec_search->addParam("contacts_search", $_GET['s']);
$_SESSION['do_Contacts']->search_keyword = $_GET['s'];
$_SESSION['do_Contacts']->eventSetSearch($ec_search);

$ec_search->doForward();

?>
