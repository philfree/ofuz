<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * ReportUserUsage class
     * Using the DataObject
     */

class ReportUserUsage extends dataObject {

    public $table = "report_user_usage";
    protected $primary_key = "iduser";

	function addUpdateReportData($iduser) {

		$do_contact = new Contact();
		$total_contacts = $do_contact->getTotalNumContactsForUser($iduser);
		
		$do_contact_notes = new ContactNotes();
		$total_notes = $do_contact_notes->getTotalNumContactNotesForUser($iduser);
		
		$do_contact_projects = new Project();
		$total_projects = $do_contact_projects->getTotalNumProjectsForUser($iduser);
		
		$do_task = new Task();
		$total_tasks = $do_task->getTotalNumTasksForUser($iduser);
		
		$do_proj_discussion = new ProjectDiscuss();
		$total_proj_discussions = $do_proj_discussion->getTotalNumProjectDiscussionsForUser($iduser);
		
		$do_invoice = new Invoice();
		$total_invoices = $do_invoice->getTotalNumInvoicesForUser($iduser);

  //total email sent today
  $msg_con = new sqlQuery($this->getDbCon());
  $sql_msg_check = "SELECT SUM(`num_msg_sent`) AS num_msg_sent  FROM `message_usage` WHERE `iduser` = ".$iduser;                
  $msg_con->query($sql_msg_check);
  $total_email_sent= 0;
  if($msg_con->getNumRows()) {
    $msg_con->fetch();
    $total_email_sent = $msg_con->getData("num_msg_sent");
  }

		$this->getId($iduser);

		if($this->hasData()) {
			$this->total_contacts = $total_contacts;
			$this->total_notes = $total_notes;
			$this->total_projects = $total_projects;
			$this->total_tasks = $total_tasks;
			$this->total_discussion = $total_proj_discussions;
			$this->total_invoices = $total_invoices;
			$this->current_date = date("Y-m-d");
   $this->total_email_sent = $total_email_sent;
			$this->update();
		} else {
			$this->total_contacts = $total_contacts;
			$this->total_notes = $total_notes;
			$this->total_projects = $total_projects;
			$this->total_tasks = $total_tasks;
			$this->total_discussion = $total_proj_discussions;
			$this->total_invoices = $total_invoices;
			$this->current_date = date("Y-m-d");
			$this->iduser = $iduser;
   $this->total_email_sent = $total_email_sent;
			$this->add();
		}

	}

    /**
      * original report
      */
	function getReport() {
		$curr_date = date("Y-m-d");
		$previous_day = date("Y-m-d", strtotime("-1 day"));

		$sql = "SELECT ruu.*,u.firstname,u.lastname,u.middlename,login_audit.last_login
				FROM report_user_usage AS ruu
				LEFT JOIN user AS u ON ruu.iduser = u.iduser
                LEFT JOIN login_audit on login_audit.iduser = ruu.iduser
				WHERE current_date = curdate()
				ORDER BY u.iduser
			   ";
		$this->query($sql);
	}

    /**
      * Report of total active users
      */
	function getActiveUsersReport() {
        $sql = "SELECT ruu.*,u.firstname,u.lastname,u.middlename,login_audit.last_login
                  FROM report_user_usage AS ruu
            INNER JOIN user AS u
                    ON ruu.iduser = u.iduser
            INNER JOIN login_audit
                    ON login_audit.iduser = ruu.iduser
                 WHERE DATEDIFF(CURDATE(), u.regdate) > 30
                   AND DATEDIFF(CURDATE(), login_audit.last_login) <= 7
              ORDER BY u.iduser";
		$this->query($sql);
	}
}
?>
