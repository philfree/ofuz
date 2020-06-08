<?php
/*
 * A cron script to track the time from GitHub Issues/Pull Requests
 *
 */
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 */
include_once('config.php');
include_once('OfuzGitHubAPI.class.php');

// Arrays of Organizations and Repositories
$repos = [
		[
			"organisation" => "htmlfusion",
			"repository" => "gishwhes",
		],
		[
			"organisation" => "htmlfusion",
			"repository" => "gishwhes_admin",
		],
		[
			"organisation" => "htmlfusion",
			"repository" => "gishwhes_api",
		],
		[
			"organisation" => "htmlfusion",
			"repository" => "gish-website",
		],
		[
			"organisation" => "AfterNow",
			"repository" => "AN-Prez",
		],
		                [
                        "organisation" => "AfterNow",
                        "repository" => "AR-Pres-LB",
                ],
];

// loops through each repo and calls methods to track the time from GitHub
foreach($repos as $repo) {
	echo $repo['organisation']." / ". $repo['repository']."<br />";

	$do_github = new OfuzGitHubAPI($conn);
	$do_github->org = $repo['organisation'];
	$do_github->repo = $repo['repository'];
	$do_github->trackTimeFromIssues();
	$do_github->trackTimeFromPullRequests();
}
?>
