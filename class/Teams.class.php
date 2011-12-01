<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Teams class
     * Using the DataObject
     */
   
class Teams extends DataObject {
    
    public $table = "team";
    protected $primary_key = "idteam";
   

    /**
      * Ajax Event Method
      * 
      * @param object $evtcl
      * @return string
    */

    function eventAjaxAddTeam(EventControler $evtcl) {
		$team_name = $evtcl->team_name;
		$auto_share = $evtcl->auto_share;
		
		$sql_check_availability = "SELECT idteam 
								   FROM {$this->table}
								   WHERE iduser = {$_SESSION['do_User']->iduser} AND `team_name` = '{$team_name}'
		                          ";	
		$this->query($sql_check_availability);                          	
		if($this->getNumRows()) {
			echo "exists";
		} else {
			$this->iduser = $_SESSION['do_User']->iduser;
			$this->team_name = $team_name;
			$this->auto_share = $auto_share;
			$this->date_created = date('Y-m-d');
			$this->add();
			
			$lastInsertedId = $this->getPrimaryKeyValue();
			
			$coworkers_list = $this->generateCoWorkersListNotAddedInTeam($lastInsertedId);
			echo $coworkers_list;
		}			
    }
    
    function generateCoWorkersListNotAddedInTeam($lastInsertedId) {
		$str_idco_workers = "";
		$cw_list = "";
						
		$coworkers = $this->getTeamCoWorkersId($lastInsertedId);

		if(count($coworkers)) {
			$str_idco_workers = implode(',', $coworkers);
		}

		$do_cw = new UserRelations();
		$do_cw->getAllCoWorkersNotInTeam($str_idco_workers);

		if($do_cw->getNumRows()) {
			$cw_list .= "<div>You can add Co-Worker/s to this Team.</div>";
			while($do_cw->next()) {
				$cw_list .= '<div><input type="checkbox" name="coworker" value="'.$do_cw->idcoworker.'" />'.$do_cw->firstname.' '.$do_cw->lastname.'</div>';
			}
		}
		
		if($cw_list) {
			$cw_list .= '<div>';
			$cw_list .= '<input type="button" name="btnAddCoWorkers" id="btnAddCoWorkers" value="Add" onclick="addCoWorkerToTeam();" />';
			$cw_list .= '<input type="button" name="btnSkipCoWorkers" id="btnSkipCoWorkers"value="Skip" onclick="skipAddCoWorker();" />';
			$cw_list .= '<input type="hidden" name="idteam_users" id="idteam_users" value="'.$lastInsertedId.'"';
			$cw_list .= '</div>';
		} else {
			$cw_list = 'You do not have a Co-Worker.';
		}
		return $cw_list;		
	}
    
    
    
    function getTeamCoWorkersId($lastInsertedId) {
		$sql = "SELECT idco_worker
		        FROM team_users
		        WHERE idteam = {$lastInsertedId}
		       ";
		$arr_cw = array();
		$this->query($sql);
		while($this->next()) {
			$arr_cw[] = $this->idco_worker;
		}
		return $arr_cw;
	}
    
    /**
     * Fetches all the teams created by a User.
     * 
     * @return Object : Query
     */
     function getTeams() {
		 $sql = "SELECT * 
		         FROM {$this->table} 
		         WHERE iduser = ".$_SESSION['do_User']->iduser." 
		         ORDER BY team_name";
		 $this->query($sql);        
	 }
	 
	 /*
	  * gets user's teams which are marked as auto-shared
	  * 
	  * @return Object : Query 
	  */
     function getUserTeamsAutoShared() {
		 $sql = "SELECT * 
		         FROM {$this->table} 
		         WHERE iduser = ".$_SESSION['do_User']->iduser." AND auto_share = 'Yes'
		         ORDER BY team_name";
		 $this->query($sql);        
	 }	 
	 
	 /*
	  * 
	  */ 
	 function eventAjaxAddCoWorkerToTeam(EventControler $evtcl) {	
		 $coworkers_list = "";
		 	 
		 if($evtcl->idcoworker) { 
			 $arr_idcoworker = explode(",",$evtcl->idcoworker);
			 foreach($arr_idcoworker as $idcoworker) {
				 $this->query("INSERT INTO team_users VALUES(null,{$evtcl->idteam_users},{$idcoworker})");				 				 
			 }
			 
			$coworkers_list = $this->generateCoWorkersListNotAddedInTeam($evtcl->idteam_users);			
		 }
		 echo $coworkers_list;	     
	 } 
	 
	 /*
	  * This gets all the Co-Workers of a particular Team.
	  * 
	  * @param int
	  * @return Object : Query
	  */
	  
	 function getCoWorkersOfTheTeam($idteam) {
		 $sql = "SELECT idco_worker
		         FROM team_users
		         WHERE idteam = {$idteam}
		        ";
		 $this->query($sql);       
	 }
	 
   
}
