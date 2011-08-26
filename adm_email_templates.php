<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

include_once("config.php");


include_once("includes/header.inc.php");

$do_msg = new EmailTemplate();

echo $do_msg->view();

?>



