<?php
/*
 * Worklog Tracking System
 * A cron script to track the time from GitHub Issues/Pull Requests
 *
 * @author ravi@afternow.io
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
              "repository" => "gishwhes-react-native",
            ],
            [
              "organisation" => "AfterNow",
              "repository" => "AN-Prez",
            ],
            [
              "organisation" => "AfterNow",
              "repository" => "AR-Pres-LB",
            ],
            [
              "organisation" => "AfterNow",
              "repository" => "AN-Prez-Quest",
            ],
            [
              "organisation" => "AfterNow",
              "repository" => "AR-Pres-WebClient",
            ],
            [
              "organisation" => "AfterNow",
              "repository" => "AN-Prez-Asset-Library",
            ],
            [
              "organisation" => "AfterNow",
              "repository" => "Blocker-ios",
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
