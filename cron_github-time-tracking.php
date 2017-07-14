<?php
/*
 * A cron script
 *
 */
include_once('config.php');

$do_github = new OfuzGitHubAPI();
$do_github->trackTimeFromCommentsSpentOnIssues();

?>
