<?php
/*
 * A class which parses the Issues Object and stores the data in the Ofuz Database
 *
 * Node Type: Issue, Pull Request
 *
 * @see class/GitHubAPI.class.php
 */

include_once('GitHubAPI.class.php');

class OfuzGitHubAPI {

	public $table = "github_time_tracking";
	protected $primary_key = "idgithub_time_tracking";
	public $report_month = "";
	public $report_year = "";
	public $week_range = "";
	public $report = array();
	public $repositories = array();
	public $org = "";
	public $repo = "";
	private $issues_cursor = "";
	private $pull_request_cursor = "";
	private $conn;

	function __construct($conn) {
		// mysqli connection
		$this->conn = $conn;
	}


	/*
	 * This method gets the repository object from GitHub API and 
	   parses all the issues and it's comments.
	   Looks for the time format T{} in the Issues' comments, extracts the exact time, 
	   stores all the info with time in the ofuz Database.
	   Time format: T{1:30} which is 1 hour 30 minutes.
	   Since the GitHub API has limit 100 on fetching the issues/comments, this method uses 
	   recursive function to read all the Issues from the repository.

	 * @see class/GitHubAPI.class.php
	 */
	public function trackTimeFromIssues() {
		$github_api = new GitHubAPI();
		$github_api->organization = $this->org;
		$github_api->repository = $this->repo;
		$result = $github_api->getAllIssues($this->issues_cursor);
		$result = $github_api->jsonDecode($result);
		$org = $this->org;
		$repo = $this->repo;
		$first_day_this_month_ts = strtotime('first day of this month');
    $last_day_this_month_ts = strtotime('last day of this month'); 

		/*$first_day_this_month_ts = strtotime('first day of July 2020');
    $last_day_this_month_ts = strtotime('last day of July 2020');*/

		foreach($result as $data) {
			foreach($data as $repository) {
				$idrepository = $repository->id;

				if($repository->issues->totalCount) {
					foreach($repository->issues->nodes as $issue) {
						//echo $issue->title."<br />";
						if($issue->comments->totalCount) {
							foreach($issue->comments->nodes as $comment) {
								$time_on_comment = preg_match('#\T{(.*?)\}#', $comment->bodyText, $matches) ? $matches[1] : "";
								if(!empty($time_on_comment)) {
									$arr_comment_created_at = explode("T", $comment->createdAt);
									$comment_created_at = $arr_comment_created_at[0];
									$arr_time_taken = explode(":", $time_on_comment);
									if(isset($arr_time_taken[1])) {
										$time_taken = $arr_time_taken[0].".".$arr_time_taken[1];
									} else {
										$time_taken = $arr_time_taken[0];
									}

									$comment_created_at_ts = strtotime($comment_created_at);

									if(($comment_created_at_ts >= $first_day_this_month_ts) && ($comment_created_at_ts <= $last_day_this_month_ts)) {
									$idgithub_time_tracking = $this->checkIfIssueCommentTimeAlreadyRecorded($org,$repo,$issue->id,$comment->id);
									if($idgithub_time_tracking) {
										$query = "UPDATE ".$this->table." SET title = '".mysqli_real_escape_string($this->conn, $issue->title)."', time_taken = '".$time_taken."' WHERE ".$this->primary_key." = ".$idgithub_time_tracking;
									} else {
				$query = "INSERT INTO ".$this->table." (`organization`,`idrepository`,`repository`,`idissue`,`title`,`url`,`id_comment`,`comment_created_at`,`comment_author`,`time_taken`,`node_type`) VALUES('".mysqli_real_escape_string($this->conn, $org)."','".mysqli_real_escape_string($this->conn, $idrepository)."','".mysqli_real_escape_string($this->conn, $repo)."','".mysqli_real_escape_string($this->conn, $issue->id)."','".mysqli_real_escape_string($this->conn, $issue->title)."','".mysqli_real_escape_string($this->conn, $issue->url)."','".mysqli_real_escape_string($this->conn, $comment->id)."','".mysqli_real_escape_string($this->conn, $comment_created_at)."','".mysqli_real_escape_string($this->conn, $comment->author->login)."','".mysqli_real_escape_string($this->conn, $time_taken)."','Issue')";
									}
										mysqli_query($this->conn, $query);
									}


								}
							}
						}
					}

					if($repository->issues->pageInfo->hasNextPage) {
						$this->issues_cursor = $repository->issues->pageInfo->endCursor;
						$this->trackTimeFromIssues();
					}
				}
			}
		}
	}

	/*
	 * This method gets the repository object from GitHub API and 
	   parses all the Pull Requests and it's comments.
	   Looks for the time format T{} in the comments, extracts the exact time, 
	   stores all the info with time in the ofuz Database.
	   Time format: T{1:30} which is 1 hour 30 minutes.
	   Since the GitHub API has limit 100 on fetching the Pull Requests, this method uses 
	   recursive function to read all the Pull Requests from the repository.

	 * @see class/GitHubAPI.class.php
	 */
	
	public function trackTimeFromPullRequests() {
		$github_api = new GitHubAPI();
		$github_api->organization = $this->org;
		$github_api->repository = $this->repo;
		$result = $github_api->getAllPullRequests($this->pull_request_cursor);
		$result = $github_api->jsonDecode($result);
		$first_day_this_month_ts = strtotime('first day of this month');
    $last_day_this_month_ts = strtotime('last day of this month');

		/*$first_day_this_month_ts = strtotime('first day of July 2020');
    $last_day_this_month_ts = strtotime('last day of July 2020');*/

		foreach($result as $data) {
			foreach($data as $repository) {
				$idrepository = $repository->id;

				if($repository->pullRequests->totalCount) {
					foreach($repository->pullRequests->nodes as $pull_request) {
						//echo $pull_request->title."<br />";
						if($pull_request->comments->totalCount) {
							foreach($pull_request->comments->nodes as $comment) {
								$time_on_comment = preg_match('#\T{(.*?)\}#', $comment->bodyText, $matches) ? $matches[1] : "";
								if(!empty($time_on_comment)) {
									//echo $pull_request->title." : ".$pull_request->url."<br />";
									$arr_comment_created_at = explode("T", $comment->createdAt);
									$comment_created_at = $arr_comment_created_at[0];
									$arr_time_taken = explode(":", $time_on_comment);
									if(isset($arr_time_taken[1])) {
										$time_taken = $arr_time_taken[0].".".$arr_time_taken[1];
									} else {
										$time_taken = $arr_time_taken[0];
									}

									$comment_created_at_ts = strtotime($comment_created_at);

									if(($comment_created_at_ts >= $first_day_this_month_ts) && ($comment_created_at_ts <= $last_day_this_month_ts)) {

									$idgithub_time_tracking = $this->checkIfTimeAlreadyTrackedForPullRequest($this->org,$this->repo,$pull_request->id,$comment->id);
									if($idgithub_time_tracking) {
										$query = "UPDATE ".$this->table." SET title = '".mysqli_real_escape_string($this->conn, $pull_request->title)."', time_taken = '".$time_taken."' WHERE ".$this->primary_key." = ".$idgithub_time_tracking;
									} else {
				$query = "INSERT INTO ".$this->table." (`organization`,`idrepository`,`repository`,`idpull_request`,`title`,`url`,`id_comment`,`comment_created_at`,`comment_author`,`time_taken`,`node_type`) VALUES('".mysqli_real_escape_string($this->conn, $this->org)."','".mysqli_real_escape_string($this->conn, $idrepository)."','".mysqli_real_escape_string($this->conn, $this->repo)."','".mysqli_real_escape_string($this->conn, $pull_request->id)."','".mysqli_real_escape_string($this->conn, $pull_request->title)."','".mysqli_real_escape_string($this->conn, $pull_request->url)."','".mysqli_real_escape_string($this->conn, $comment->id)."','".mysqli_real_escape_string($this->conn, $comment_created_at)."','".mysqli_real_escape_string($this->conn, $comment->author->login)."','".mysqli_real_escape_string($this->conn, $time_taken)."','Pull Request')";
									}
										mysqli_query($this->conn, $query);
									}
								}
							}
						}
					}

					if($repository->pullRequests->pageInfo->hasNextPage) {
						$this->pull_request_cursor = $repository->pullRequests->pageInfo->endCursor;
						$this->trackTimeFromPullRequests();
					}
				}
			}
		}
	}
	 

	/*
	 * Checks if time already tracked in the Database for specific comment for specific Issue.
	 *
	 * @param string : $org organization
	 * @param string : $repo repository
	 * @param string : $id_issue issue id
	 * @param string : $id_comment comment id
	 *
	 * @return int/bool : if true returns primary key or false
	 */
	public function checkIfIssueCommentTimeAlreadyRecorded($org,$repo,$id_issue,$id_comment){
		$query = "SELECT ".$this->primary_key
				." FROM ".$this->table
				." WHERE `organization` = '".$org."' AND `repository` = '".$repo."' 
				AND `idissue` = '".$id_issue."'  AND `id_comment` = '".$id_comment."' 
				AND node_type = 'Issue'";
		$result = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			return $row['idgithub_time_tracking'];
		} else {
			return false;
		}
	}

	/*
	 * Checks if time already tracked in the Database for specific comment for specific Pull Request.
	 *
	 * @param string : $org organization
	 * @param string : $repo repository
	 * @param string : $idpull_request Pull Request id
	 * @param string : $id_comment comment id
	 *
	 * @return int/bool : if true returns primary key or false
	 */
	public function checkIfTimeAlreadyTrackedForPullRequest($org,$repo,$idpull_request,$id_comment){
		$query = "SELECT ".$this->primary_key
				." FROM ".$this->table
				." WHERE `organization` = '".$org."' AND `repository` = '".$repo."' 
				AND `idpull_request` = '".$idpull_request."'  AND `id_comment` = '".$id_comment."' 
				AND node_type = 'Pull Request'";
		$result = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			return $row['idgithub_time_tracking'];
		} else {
			return false;
		}
	}

	/*
	 * Prepares weeks of the month
	 */
    function getWeekRangeDropDown(){
		$num_of_weeks = $this->getNumberOfWeeks();
		$start_date = $this->report_year.'-'.$this->report_month.'-01';

		$year = $this->report_year;
		$month = $this->report_month;
        
        $html = '';
        $html .= '<select name="weeks" id="weeks">';
        $html .= '<option value="">Select Week</option>';
        for($i=1;$i<=$num_of_weeks;$i++){
			if($i != 1){
				$lastday = date("t", mktime(0, 0, 0, $month, 1, $year));
				$lastdate = "$year"."-"."$month"."-"."$lastday";
				$lastdate = strtotime(date("Y-m-d",strtotime($lastdate)) ."+1 day");
				$start_date = strtotime(date("Y-m-d",strtotime($end_date)) ."+1 day");

				if($start_date != $lastdate){
					$start_date = date("Y-m-d",$start_date);
				}
				else{
					$html .= '</select>';
					return $html;
				}
            }

            //$start_date_splited = explode("-",$start_date);
            $end_date = strtotime(date("Y-m-d", strtotime($start_date)) . "next sunday");
            $end_date = date("Y-m-d",$end_date);
            $end_date_splited = explode("-",$end_date);
            if($this->report_month != $end_date_splited[1]){
                $result = strtotime("{$this->report_year}-{$this->report_month}-01");
                $result = strtotime('-1 second', strtotime('+1 month', $result)); 
                $end_date = date("Y-m-d",$result);
                $end_date_splited = explode("-",$end_date);
            }
            $date_range = $start_date.'/'.$end_date;
            $html .= '<option value="'.$start_date.'/'.$end_date.'" 
                        '.$this->getWeeksFilter($date_range).'>Week '.$i.': '
                         .date("d S",strtotime($start_date)).' to '.date("d S",strtotime($end_date)).'</option>';
        }

        $html .= '</select>';
        return $html;
    }

    function getNumberOfWeeks(){
		if($this->report_month == ""){ $this->report_month = date("m"); }
		if($this->report_year == ""){ $this->report_year = date("Y"); }
		$year = $this->report_year;
		$month = $this->report_month;
        
		$num_of_days = date("t", mktime(0,0,0,$month,1,$year));
		$firstdayname = date("D", mktime(0, 0, 0, $month, 1, $year));
		$firstday = date("w", mktime(0, 0, 0, $month, 1, $year));
		$lastday = date("t", mktime(0, 0, 0, $month, 1, $year));
		$min_week = 0;
		$max_week = 0;
		$counter_track_num_week = 0;    

		for ($day_of_week = 0; $day_of_week <= 6; $day_of_week++){
			$counter_track_num_week++;    
			if ($firstday > $day_of_week) {
				// means we need to jump to the second week to find the first $day_of_week
				$d = (7 - ($firstday - $day_of_week)) + 1;
			} elseif ($firstday < $day_of_week) {
				// correct week, now move forward to specified day
				$d = ($day_of_week - $firstday + 1);
			} else {
			// "reversed-engineered" formula
				if ($lastday==28) // max of 4 occurences each in the month of February with 28 days
				$d = ($firstday + 4);
				elseif ($firstday==4)
				$d = ($firstday - 2);
				elseif ($firstday==5 )
				$d = ($firstday - 3);
				elseif ($firstday==6)
				$d = ($firstday - 4);
				else
				$d = ($firstday - 1);
				if ($lastday==29) // only 1 set of 5 occurences each in the month of February with 29 days
				$d -= 1;
			}

			$d += 28; // jump to the 5th week and see if the day exists

			if ($d > $lastday) {
			  $weeks = 4;
			  $min_week = $weeks;
			} elseif($d == $lastday) {
				$weeks = 5;
				$max_week = $weeks;
			}else{
				$weeks = 6;
				$max_week = $weeks;
			}
		}

		if($max_week != 0){
			return $max_week;
		}else{
			return $min_week;
		}
    }

    /*
     * Method to generate the Months Dropdown for the reporting.
     * @return html
     */
    function getMonthsDropDown(){
        if($this->report_month == ""){ $this->report_month = date("m"); }

        $html = '<select class="report_selects" name="months" id="months">';
        $html .='<option value = "01" '.$this->getMonthFilter("01").'>January</option>';
        $html .='<option value = "02" '.$this->getMonthFilter("02").'>February</option>';
        $html .='<option value = "03" '.$this->getMonthFilter("03").'>March</option>';
        $html .='<option value = "04" '.$this->getMonthFilter("04").'>April</option>';
        $html .='<option value = "05" '.$this->getMonthFilter("05").'>May</option>';
        $html .='<option value = "06" '.$this->getMonthFilter("06").'>June</option>';
        $html .='<option value = "07" '.$this->getMonthFilter("07").'>July</option>';
        $html .='<option value = "08" '.$this->getMonthFilter("08").'>August</option>';
        $html .='<option value = "09" '.$this->getMonthFilter("09").'>September</option>';
        $html .='<option value = "10" '.$this->getMonthFilter("10").'>October</option>';
        $html .='<option value = "11" '.$this->getMonthFilter("11").'>November</option>';
        $html .='<option value = "12" '.$this->getMonthFilter("12").'>December</option>';
        $html .= '</select>';

        return $html;
    }

	/*
	 *
     * Get week filter 
     */
	function getWeeksFilter($selected=""){
		if ($selected == $this->week_range) {
            return " selected";
        } else {
            return "";
        }
    }

    /**
      * Get the month filter
    */
    function getMonthFilter($selected="") {
        if ($selected == $this->report_month) {
            return "selected";
        } else {
            return "";
        }
    }

	/*
	 *
	 */
	function eventGetWeeksRangeDropdown($year, $month, $weeks=""){
		$this->report_year = $year;
		$this->report_month = $month;

		$data = $this->getWeekRangeDropDown();

		return $data;
	}

	/*
	 *
	 */
	function jsonEncode($data) {
		$data = json_encode($data);
		return $data;
	}

	/*
	 *
	 */
	function eventGetTimesheetReport($year, $month, $weeks) {
		$this->report_year = $year;
		$this->report_month = $month;
		$this->week_range = $weeks;

		$this->getTimeSpentOnRepositoriesPerAuthors();
    $this->getTimeTakenOnIssuesPerRepository();
    // wekan board
		$this->getTimeSpentOnBoardsPerUsers();
    $this->getTimeTakenOnCardsPerBoard();
    
    /*
    $data = $this->jsonEncode($this->report);	
    return $data;
    */
	}


	function getTimeSpentOnBoardsPerUsers(){
		$where_clause_date_range = $this->getQueryWhereClauseForDateRange();
		$query = "SELECT `user`, SUM(time_taken) AS time_taken 
					FROM wekan_time_tracking 
					WHERE ".$where_clause_date_range."
					GROUP BY `user`";
    $result = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_object($result)) {
				$authors_time = array();
				$authors_time['commentAuthor'] = $row->user;
				$authors_time['timeTaken'] = $row->time_taken;
				$this->repositories["users"][] = $authors_time;
			}

		}

		$this->report = $this->repositories;
  }

	/*
     *
	 */
	function getTimeSpentOnRepositoriesPerAuthors(){
		$where_clause_date_range = $this->getQueryWhereClauseForDateRange();
		$query = "SELECT comment_author, SUM(time_taken) AS time_taken 
					FROM ".$this->table."
					WHERE ".$where_clause_date_range."
					GROUP BY comment_author";
		$result = mysqli_query($this->conn, $query);
		if(mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_object($result)) {
				$authors_time = array();
				$authors_time['commentAuthor'] = $row->comment_author;
				$authors_time['timeTaken'] = $row->time_taken;
				$this->repositories["authorsTime"][] = $authors_time;
			}

		}

		$this->report = $this->repositories;
	}


	/*
     *
	 */
	function getTimeTakenOnIssuesPerRepository(){

		$where_clause_date_range = $this->getQueryWhereClauseForDateRange();
		$query = "SELECT DISTINCT(idrepository),repository,organization 
					FROM ".$this->table."
					WHERE ".$where_clause_date_range;
		$result = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($result) > 0) {
			while($repo = mysqli_fetch_object($result)) {
				$time_spent_on_repo  = $this->getTimeSpentOnRepository($repo->idrepository);
				$time_per_issues = $this->getTimeSpentPerRepositoryPerIssues($repo->idrepository);
				$time_per_pull_requests = $this->getTimeSpentPerRepositoryPerPullRequests($repo->idrepository);
				$time_per_authors = $this->getTimeSpentPerRepositoryPerAuthors($repo->idrepository);
				$arr_repo = array(
								"organization" => $repo->organization,
								"idrepository" => $repo->idrepository,
								"repository" => $repo->repository,
								"totalTimeSpent" => $time_spent_on_repo,
								"issues" => $time_per_issues,
								"pullRequests" => $time_per_pull_requests,
								"authors" => $time_per_authors
							);
				$this->repositories["repositories"][] = $arr_repo;
			}
		}

		$this->report = $this->repositories;

	}

	/*
	 * Creates a WHERE clause based on the YEAR, MONTH, WEEKS range selection
	 *
	 * @return string : WHERE clause
	 */
	function getQueryWhereClauseForDateRange() {

		$year_month = $this->report_year."-".$this->report_month;
		$where_clause_date_range = "";

		if($this->week_range) {
			$arr_week_range = explode('/', $this->week_range);
			$where_clause_date_range = "`comment_created_at` BETWEEN '".$arr_week_range[0]."' AND '".$arr_week_range[1]."'";
		} else {
			$where_clause_date_range = "`comment_created_at` LIKE '".$year_month."-%'";
		}

		return $where_clause_date_range;

	}

	/*
	 *
	 */
	function getTimeSpentOnRepository($idrepository){

		$time_spent_on_repo = "0.00";
		$where_clause_date_range = $this->getQueryWhereClauseForDateRange();
		$query = "SELECT SUM(time_taken) AS time_taken
					FROM ".$this->table." 
					WHERE idrepository = '".$idrepository."' AND ".$where_clause_date_range;

		$result = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_object($result);	
			$time_spent_on_repo = $row->time_taken;
		}

		return $time_spent_on_repo;

	}

	/*
	 * This gets total time spent on each ISSUE for specific repository 
	   for given DATE range
	 *
	 * @param string : $idrepository
	 * @return array : of Issues with time spent on.
	 */
	function getTimeSpentPerRepositoryPerIssues($idrepository){

		$data = array();
		$where_clause_date_range = $this->getQueryWhereClauseForDateRange();
		/*
		 * following query throws error because of SQL_MODE:
		 * ERROR 1055 (42000): Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 
		   'ofuz.github_time_tracking.title' which is not functionally dependent on columns in GROUP BY clause; this is 
		   incompatible with sql_mode=only_full_group_by
		 *
		$query = "SELECT title, SUM(time_taken) AS time_taken
					FROM ".$this->table." 
					WHERE idrepository = '".$idrepository."' AND node_type = 'Issue' AND ".$where_clause_date_range."
					GROUP BY idissue";
		 */
		$query = "SELECT title, SUM(time_taken) AS time_taken
					FROM ".$this->table." 
					WHERE idrepository = '".$idrepository."' AND node_type = 'Issue' AND ".$where_clause_date_range."
					GROUP BY title";
		$result = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_object($result)) {
				$issue = array(
								'title' => $row->title,
								'time_taken' => $row->time_taken
							);
				$data['issue'][] = $issue;	
			}
		}

		return $data;

	}

	/*
	 * This gets total time spent on each Pull Request for specific repository 
	   for given DATE range
	 *
	 * @param string : $idrepository
	 * @return array : of Pull Requests with time spent on.
	 */
	function getTimeSpentPerRepositoryPerPullRequests($idrepository){

		$data = array();
		$where_clause_date_range = $this->getQueryWhereClauseForDateRange();
		$query = "SELECT title, SUM(time_taken) AS time_taken
					FROM ".$this->table." 
					WHERE idrepository = '".$idrepository."' AND node_type = 'Pull Request' AND ".$where_clause_date_range."
					GROUP BY title";
		$result = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($result)) {
			while($row = mysqli_fetch_object($result)) {
				$pull_request = array(
								'title' => $row->title,
								'time_taken' => $row->time_taken
							);
				$data['pullRequest'][] = $pull_request;
			}
		}

		return $data;

	}

	/*
	 * This gets time spent on specific repository  by 
	   each Author for given DATE range
	 *
	 * @param string : $idrepository
	 * @return array 
	 */
	function getTimeSpentPerRepositoryPerAuthors($idrepository){

		$data = array();
		$where_clause_date_range = $this->getQueryWhereClauseForDateRange();
		$query = "SELECT comment_author, SUM(time_taken) AS time_taken
					FROM ".$this->table." 
					WHERE idrepository = '".$idrepository."' AND ".$where_clause_date_range."
					GROUP BY comment_author";
		$result = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($result)) {
			while($row = mysqli_fetch_object($result)) {
				$time_authors = array(
								'login' => $row->comment_author,
								'time_taken' => $row->time_taken
							);
				$data['author'][] = $time_authors;	
			}
		}

		return $data;

	}

	function getTimeTakenOnCardsPerBoard(){

		$where_clause_date_range = $this->getQueryWhereClauseForDateRange();
		$query = "SELECT DISTINCT(board_id),board
			  FROM wekan_time_tracking 
			  WHERE ".$where_clause_date_range;
		$result = mysqli_query($this->conn, $query);
		$arr = array();	
		if(mysqli_num_rows($result) > 0) {
			while($board = mysqli_fetch_object($result)) {
				$time_spent_on_board  = $this->getTimeSpentOnBoard($board->board_id);
				$time_per_cards = $this->getTimeSpentPerBoardPerCard($board->board_id);
				$time_per_authors = $this->getTimeSpentPerBoardPerAuthors($board->board_id);
				$arr_board= array(
								"organization" => "AfterNow",
								"board_id" => $board->board_id,
								"board" => $board->board,
								"totalTimeSpent" => $time_spent_on_board,
								"cards" => $time_per_cards,
								"users" => $time_per_authors
							);
				$this->repositories["boards"][] = $arr_board;
			}
		}

		$this->report = $this->repositories;

  }

	/*
	 *
	 *
	 */
	function getTimeSpentOnBoard($board_id){

		$time_spent = "0.00";
		$where_clause_date_range = $this->getQueryWhereClauseForDateRange();
		$query = "SELECT SUM(time_taken) AS time_taken
			FROM wekan_time_tracking  
			WHERE `board_id` = '".$board_id."' AND ".$where_clause_date_range;

		$result = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_object($result);	
			$time_spent = $row->time_taken;
		}

		return $time_spent;
	}


	/*
	 * This gets total time spent on each card for specific Board 
	   for given DATE range
	 *
	 * @param string : $board_id
	 * @return array : of cards with time spent on.
	 */
	function getTimeSpentPerBoardPerCard($board_id){

		$data = array();
		$where_clause_date_range = $this->getQueryWhereClauseForDateRange();
		$query = "SELECT `card`, SUM(time_taken) AS time_taken
					FROM wekan_time_tracking 
					WHERE `board_id` = '".$board_id."' AND ".$where_clause_date_range."
					GROUP BY card";
		$result = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($result) > 0) {
			while($row = mysqli_fetch_object($result)) {
				$card = array(
								'title' => $row->card,
								'time_taken' => $row->time_taken
							);
				$data['card'][] = $card;	
			}
		}

		return $data;

  }

	/*
	 * This gets time spent on specific Board by 
	   each Author for given DATE range
	 *
	 * @param string : $board_id
	 * @return array 
	 */
	function getTimeSpentPerBoardPerAuthors($board_id){

		$data = array();
		$where_clause_date_range = $this->getQueryWhereClauseForDateRange();
		$query = "SELECT `user`, SUM(time_taken) AS time_taken
					FROM wekan_time_tracking 
					WHERE `board_id` = '".$board_id."' AND ".$where_clause_date_range."
					GROUP BY `user`";
		$result = mysqli_query($this->conn, $query);

		if(mysqli_num_rows($result)) {
			while($row = mysqli_fetch_object($result)) {
				$time_authors = array(
								'login' => $row->user,
								'time_taken' => $row->time_taken
							);
				$data['author'][] = $time_authors;	
			}
		}

		return $data;
	}

}
?>
