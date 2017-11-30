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
$do_github->trackTimeFromIssues();
$do_github->trackTimeFromPullRequests();


/*
echo "AfterNow / death2normalcy <br />";

$do_github = new OfuzGitHubAPI();
$do_github->org = "AfterNow";
$do_github->repo = "death2normalcy";
$do_github->trackTimeFromIssues();
$do_github->trackTimeFromPullRequests();
*/

echo "AfterNow / AR-Pres-Hololens<br />";

$do_github = new OfuzGitHubAPI();
$do_github->org = "AfterNow";
$do_github->repo = "AR-Pres-Hololens";
$do_github->trackTimeFromIssues();
$do_github->trackTimeFromPullRequests();

echo "AfterNow / Hyperlens2<br />";

$do_github = new OfuzGitHubAPI();
$do_github->org = "AfterNow";
$do_github->repo = "Hyperlens2";
$do_github->trackTimeFromIssues();
$do_github->trackTimeFromPullRequests();

echo "AfterNow / AR-Pres-API<br />";

$do_github = new OfuzGitHubAPI();
$do_github->org = "AfterNow";
$do_github->repo = "AR-Pres-API";
$do_github->trackTimeFromIssues();
$do_github->trackTimeFromPullRequests();

echo "AfterNow / inhance_sensr<br />";

$do_github = new OfuzGitHubAPI();
$do_github->org = "AfterNow";
$do_github->repo = "inhance_sensr";
$do_github->trackTimeFromIssues();
$do_github->trackTimeFromPullRequests();

echo "AfterNow / AR-Pres-WebClient<br />";

$do_github = new OfuzGitHubAPI();
$do_github->org = "AfterNow";
$do_github->repo = "AR-Pres-WebClient";
$do_github->trackTimeFromIssues();
$do_github->trackTimeFromPullRequests();

echo "AfterNow / Sherlock3CG<br />";

$do_github = new OfuzGitHubAPI();
$do_github->org = "AfterNow";
$do_github->repo = "Sherlock3CG";
$do_github->trackTimeFromIssues();
$do_github->trackTimeFromPullRequests();
?>
