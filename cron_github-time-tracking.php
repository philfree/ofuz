<?php
/*
 * A cron script to track the time from GitHub Issues/Pull Requests
 *
 */

include_once('config.php');


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
		"repository" => "death2normalcy",
	],
	[
		"organisation" => "AfterNow",
		"repository" => "AR-Pres-Hololens",
	],
	[
		"organisation" => "AfterNow",
		"repository" => "Hyperlens2",
	],
	[
		"organisation" => "AfterNow",
		"repository" => "AR-Pres-API",
	],
	[
		"organisation" => "AfterNow",
		"repository" => "inhance_sensr",
	],
	[
		"organisation" => "AfterNow",
		"repository" => "AR-Pres-WebClient",
	],
	[
		"organisation" => "AfterNow",
		"repository" => "Sherlock3CG",
	],
	[
		"organisation" => "AfterNow",
		"repository" => "gishwhes-react-native",
	],
];

// loops through each repo and calls methods to track the time from GitHub
foreach($repos as $repo) {
	echo $repo['organisation']." / ". $repo['repository']."<br />";

	$do_github = new OfuzGitHubAPI();
	$do_github->org = $repo['organisation'];
	$do_github->repo = $repo['repository'];
	$do_github->trackTimeFromIssues();
	$do_github->trackTimeFromPullRequests();
}

?>
