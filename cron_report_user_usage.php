<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

/**
 * A cron job script for generating Report on User Usage
 */

include_once('config.php');

$do_user = new User();
$do_user->getAllUsersId();

while($do_user->next()) {
	$do_report = new ReportUserUsage();
	$do_report->addUpdateReportData($do_user->iduser);
	$do_report->free();
}

