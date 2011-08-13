<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

include_once("config.php");


include_once("includes/header.inc.php");

$do_msg = new EmailTemplate();

echo $do_msg->view();

?>



