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

	 function eventAddContactToTeamCW(Contact $contact) {

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
						$this->idcontact = $contact->idcontact;
						$this->idteam = $do_teams->idteam;
						$this->idcoworker = $do_teams_cw->idco_worker;
						$this->add();
						$this->free();

						//building contact view table
						$do_cv = new ContactView();
						$do_cv->table = "userid".$do_teams_cw->idco_worker."_contact";
						$do_cv->getId($contact->idcontact);
						if(!$do_cv->hasData()) {
						  $do_cv->addNew();
						  $do_cv->idcontact = $contact->idcontact;
						  $do_cv->firstname = $contact->firstname;
						  $do_cv->lastname = $contact->lastname;
						  $do_cv->company = $contact->company;
						  $do_cv->idcompany = $contact->idcompany;
						  $do_cv->position = $contact->position;
						  $do_cv->picture = $contact->picture;
						  $do_cv->last_activity = date('Y-m-d h:i:s');
						  $do_cv->last_update = date('Y-m-d h:i:s');
						  $do_cv->first_created = date('Y-m-d h:i:s');
						  $do_cv->add();
						  $do_cv->free();
						}

						//Conact sharing with Co-Worker
						$do_cs_check = new ContactSharing();
						$contact_shared = $do_cs_check->checkCoWorkerContactRel($contact->idcontact,$do_teams_cw->idco_worker);
						if(!$contact_shared) {
						  $do_cs = new ContactSharing();
						  $do_cs->addNew();
						  $do_cs->iduser = $_SESSION['do_User']->iduser;
						  $do_cs->idcontact = $contact->idcontact;
						  $do_cs->idcoworker = $do_teams_cw->idco_worker;
						  $do_cs->add();
						  $do_cs->free();
						}
						$do_cs_check->free();
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

  /**
   * Contact is shared with selected Team/s and Co-Worker/s
   *
   * @param Object : EventControler
   * 
   */
  function eventShareExistingContactWithTeamCw(EventControler $evtcl) {
    $idteams = $evtcl->getParam("team");
    $idcoworkers = $evtcl->getParam("cwid");
    $contacts = $evtcl->getParam("idcontacts");

    if(is_array($idteams) && is_array($idcoworkers)) {
      foreach($idteams as $idteam) {
	foreach($idcoworkers as $idcoworker) {
	  foreach($contacts as $contact) {
	    $sql = "SELECT count(*) AS count_team
		    FROM {$this->table}
		    WHERE idcontact = {$contact} AND idteam = {$idteam} AND idcoworker = {$idcoworker}
                   ";
	    $this->query($sql);
	    $this->fetch();

	    if(!$this->getData("count_team")) {
	      $this->addNew();
	      $this->idcontact = $contact;
	      $this->idteam = $idteam;
	      $this->idcoworker = $idcoworker;
	      $this->add();
	    }
	  }
	}
      }
    }
  }
	 
}
