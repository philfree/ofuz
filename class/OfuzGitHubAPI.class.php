<?php
/*
 * A class which interacts with GitHub API and Ofuz Database
 */

include_once('GitHubAPI.class.php');

class OfuzGitHubAPI extends DataObject {

    public $table = "github_time_tracking";
    protected $primary_key = "idgithub_time_tracking";
	public $report_month = "";
	public $report_year = "";
    public $week_range = "";
	public $report = array();
	public $repositories = array();
	private $issues_cursor = "";
	public $org = "";
	public $repo = "";

    function __construct(sqlConnect $conx=NULL, $table_name="") {
		parent::__construct($conx, $table_name);
    }

	/*
	 *
	 */
	public function trackTimeFromCommentsSpentOnIssues() {
		$github_api = new GitHubAPI();
		$github_api->organization = $this->org;
		$github_api->repository = $this->repo;
		$result = $github_api->getAllIssues($this->issues_cursor);
		$result = $github_api->jsonDecode($result);
		$org = $this->org;
		$repo = $this->repo;

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
									$comment_created_at = date('Y-m-d', strtotime($comment->createdAt));
									$arr_time_taken = explode(":", $time_on_comment);
									$time_taken = $arr_time_taken[0].".".$arr_time_taken[1];

									$idgithub_time_tracking = $this->checkIfIssueCommentTimeAlreadyRecorded($org,$repo,$issue->id,$comment->id);

									if($idgithub_time_tracking) {
										$this->getId($idgithub_time_tracking);
										$this->issue_title = $issue->title;
										$this->time_taken = $time_taken;
										$this->update();
									} else {
										$this->addNew();
										$this->organization = $org;
										$this->idrepository = $idrepository;
										$this->repository = $repo;
										$this->idissue = $issue->id;
										$this->issue_title = $issue->title;
										$this->issue_url = $issue->url;
										$this->id_issue_comment = $comment->id;
										$this->issue_comment_created_at = $comment_created_at;
										$this->issue_comment_author = $comment->author->login;
										$this->time_taken = $time_taken;
										$this->add();
									}
								}
							}
						}
					}

					if($repository->issues->pageInfo->hasNextPage) {
						$this->issues_cursor = $repository->issues->pageInfo->endCursor;
						$this->trackTimeFromCommentsSpentOnIssues();
					}
				}
			}
		}
	}

	/*
	 *
	 */
	public function checkIfIssueCommentTimeAlreadyRecorded($org,$repo,$id_issue,$id_comment){
		$query = "SELECT ".$this->primary_key
				." FROM ".$this->table
				." WHERE `organization` = '".$this->quote($org)."' AND `repository` = '".$this->quote($repo)."' 
				AND `idissue` = '".$this->quote($id_issue)."'  AND `id_issue_comment` = '".$this->quote($id_comment)."'"
				;
		$this->query($query);

		if($this->getNumRows()) {
			return $this->idgithub_time_tracking;
		} else {
			return false;
		}
	}

	/*
	 *
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
	function eventGetWeeksRangeDropdown(EventControler $evtcl){
		$this->report_month = $evtcl->month;
		$this->report_year = $evtcl->year;

		$data = $this->getWeekRangeDropDown();

		echo $data;
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
	function eventGetTimesheetReport(EventControler $evtcl) {
		$this->report_year = $evtcl->year;
		$this->report_month = $evtcl->month;
		$this->week_range = $evtcl->weeks;

		$this->getTimeSpentOnRepositoriesPerAuthors();
		$this->getTimeTakenOnIssuesPerRepository();

		$data = $this->jsonEncode($this->report);	
		echo $data;
		//print_r($this->report);
	}

	/*
     *
	 */
	function getTimeSpentOnRepositoriesPerAuthors(){

		$where_clause_date_range = $this->getQueryWhereClauseForDateRange();

		$query = "SELECT issue_comment_author AS comment_author, SUM(time_taken) AS time_taken 
					FROM ".$this->table."
					WHERE ".$where_clause_date_range."
					GROUP BY issue_comment_author";
		$this->query($query);

		if($this->getNumRows()) {
			while($this->next()) {
				$authors_time = array();
				$authors_time['commentAuthor'] = $this->comment_author;
				$authors_time['timeTaken'] = $this->time_taken;
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
		$sql = new sqlQuery($this->getDbCon());
		$query = "SELECT DISTINCT(idrepository),repository,organization 
					FROM ".$this->table."
					WHERE ".$where_clause_date_range;

		$sql->query($query);

		if($sql->getNumRows()) {
			while($repo = $sql->fetch()) {
				$time_spent_on_repo  = $this->getTimeSpentOnRepository($repo->idrepository);
				$time_per_issues = $this->getTimeSpentPerRepositoryPerIssues($repo->idrepository);
				$time_per_authors = $this->getTimeSpentPerRepositoryPerAuthors($repo->idrepository);
				$arr_repo = array(
								"organization" => $repo->organization,
								"idrepository" => $repo->idrepository,
								"repository" => $repo->repository,
								"totalTimeSpent" => $time_spent_on_repo,
								"issues" => $time_per_issues,
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
			$where_clause_date_range = "`issue_comment_created_at` BETWEEN '".$arr_week_range[0]."' AND '".$arr_week_range[1]."'";
		} else {
			$where_clause_date_range = "`issue_comment_created_at` LIKE '".$year_month."-%'";
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

		$this->query($query);

		if($this->getNumRows()) {
			$this->fetch();
			$time_spent_on_repo = $this->time_taken;
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
		$query = "SELECT issue_title, SUM(time_taken) AS time_taken
					FROM ".$this->table." 
					WHERE idrepository = '".$idrepository."' AND ".$where_clause_date_range."
					GROUP BY idissue";
		$this->query($query);

		if($this->getNumRows()) {
			while($this->next()) {
				$issue = array(
								'title' => $this->issue_title,
								'time_taken' => $this->time_taken
							);
				$data['issue'][] = $issue;	
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
		$query = "SELECT issue_comment_author, SUM(time_taken) AS time_taken
					FROM ".$this->table." 
					WHERE idrepository = '".$idrepository."' AND ".$where_clause_date_range."
					GROUP BY issue_comment_author";
		$this->query($query);

		if($this->getNumRows()) {
			while($this->next()) {
				$time_authors = array(
								'login' => $this->issue_comment_author,
								'time_taken' => $this->time_taken
							);
				$data['author'][] = $time_authors;	
			}
		}

		return $data;

	}

}
?>
