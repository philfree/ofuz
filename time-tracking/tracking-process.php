<?php
/*
 * An intermediate file, used by an ajax call, to interact with Class
 *
 * @see github_time_tracking.php
 * @see OfuzGitHubAPI.class.php
 */
include_once("config.php");
include_once("OfuzGitHubAPI.class.php");
include_once("wekan/Wekan.class.php");

$month = $_POST['month'];
$year = $_POST['year'];
$weeks = $_POST['weeks'];
$feature = $_POST['feature'];
$data = array();

if($feature == "week-range") {
  $do_github = new OfuzGitHubAPI($conn);
  $data = $do_github->eventGetWeeksRangeDropdown($year,$month,$weeks);
} else if($feature == "timesheet-all") {
  $do_github = new OfuzGitHubAPI($conn);
  $do_github->eventGetTimesheetReport($year,$month,$weeks);

  $data = json_encode($do_github->report);
} else {
}

echo $data;
exit();
?>
