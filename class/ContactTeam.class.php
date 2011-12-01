<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * ContactTeam class
     * Using the DataObject
     */
   
class ContactTeam extends DataObject {
    
    public $table = "contact_team";
    protected $primary_key = "idcontact_team";

	 /*
	  * Shares Contact to the Co-Workers with Teams which are marked auto-shared.
	  * 1. Gets the user's Teams which are marked auto-shared
	    2. Gets Co-Workers of these teams 
	    3. Shares This Contact with these Co-Workers
	   
	  * @param Object : EventControler
	  *
	  */
	 function eventAddContactToTeamCW(EventControler $evtcl) {
		 $do_teams = new Teams();
		 //gets user's teams which are marked as auto-shared
		 $do_teams->getUserTeamsAutoShared();
		 
		 if($do_teams->getNumRows()) {
			 while($do_teams->next()) {
				 //gets Co-Workers of the team
				 $do_teams_cw = new Teams();
				 $do_teams_cw->getCoWorkersOfTheTeam($do_teams->idteam);
				 if($do_teams_cw->getNumRows()) {
					 while($do_teams_cw->next()) {						
						$this->addNew();
						$this->idcontact = $_SESSION['ContactEditSave']->idcontact;
						$this->idteam = $do_teams->idteam;
						$this->idcoworker = $do_teams_cw->idco_worker;
						$this->add();
						$this->free();
					 }
				 } else {
						$this->addNew();
						$this->idcontact = $_SESSION['ContactEditSave']->idcontact;
						$this->idteam = $do_teams->idteam;
						$this->add();
						$this->free();					 
				 }
				 $do_teams_cw->free();				 
			}
		}
		$do_teams->free();
	 } 
}
