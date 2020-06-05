<?php
/*
 * An intermediate file, used by an ajax call, to interact with Class
 *
 * @see github_time_tracking.php
 * @see OfuzGitHubAPI.class.php
 */
include_once("config.php");
include_once("OfuzGitHubAPI.class.php");

$month = $_POST['month'];
$year = $_POST['year'];
$weeks = $_POST['weeks'];
$method = $_POST['method'];
$do_github = new OfuzGitHubAPI($conn);
$data = $do_github->$method($year,$month,$weeks);
echo $data;
exit();
?>
