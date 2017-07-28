<?php
/*
 * A cron script
 *
 */
include_once('config.php');

echo "htmlfusion / gishwhes <br />";

$do_github = new OfuzGitHubAPI();
$do_github->org = "htmlfusion";
$do_github->repo = "gishwhes";
$do_github->trackTimeFromCommentsSpentOnIssues();

echo "AfterNow / death2normalcy <br />";

$do_github = new OfuzGitHubAPI();
$do_github->org = "AfterNow";
$do_github->repo = "death2normalcy";
$do_github->trackTimeFromCommentsSpentOnIssues();
?>
