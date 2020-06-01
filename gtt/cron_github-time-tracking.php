<?php
/*
 * A cron script to track the time from GitHub Issues/Pull Requests
 *
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
