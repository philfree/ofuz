<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/


    /**
     * Contact class
     * Using the DataObject
     * The note Model is:
		+----------------+--------------+------+-----+---------+----------------+
		| Field          | Type         | Null | Key | Default | Extra          |
		+----------------+--------------+------+-----+---------+----------------+
		| idcontact_note | int(10)      | NO   | PRI | NULL    | auto_increment |
		| idcontact      | int(15)      | NO   |     | 0       |                |
		| note           | text         | NO   |     |         |                |
		| date_added     | date         | NO   |     |         |                |
		| document       | varchar(254) | NO   |     |         |                |
		| idcompany      | int(15)      | NO   |     | 0       |                |
		| iduser         | varchar(15)  | NO   |     |         |                |
		+----------------+--------------+------+-----+---------+----------------+
     * Copyright 2001 - 2010 All rights reserved SQLFusion LLC, info@sqlfusion.com 
	 

     */

class ContactNotes extends Note {
    
    public $table = "contact_note";
    protected $primary_key = "idcontact_note";
    protected $prefix = "ContactNotes";  // Should be the same as the class name 
    public $sql_view_limit = 50;
	public $sql_qry_start = 0;
	public $sql_qry_end = 50;
	public $notes_count = 0;
	//public $idcontact = "";
 
    function __construct(sqlConnect $conx=NULL, $table_name="") {
       parent::__construct($conx, $table_name);
       $this->setLogRun(RADRIA_LOG_RUN_OFUZ);
    }

    function getContactNotes($idcontact) {
        $this->query("select * from contact_note where idcontact = " . $idcontact ." order by priority desc, date_added desc, idcontact_note desc limit {$this->sql_qry_start},{$this->sql_view_limit}");
		//echo "select * from contact_note where idcontact = " . $idcontact ." order by priority desc, date_added desc, idcontact_note desc limit {$this->sql_qry_start},{$this->sql_view_limit}";
		//$this->notes_count = $this->getNumRows();
    }

	function getContactNotesCount($idcontact) {
        $this->query("select * from contact_note where idcontact = " . $idcontact ." order by priority desc, date_added desc, idcontact_note desc");
		$this->notes_count = $this->getNumRows();
	}

    function getContactNotesDeleted($idcontact) {
        $this->query("select * from contact_note where idcontact = " . $idcontact ." AND is_delete = 1 order by priority desc, date_added desc, idcontact_note desc");
    }

    function eventAjaxGetContactNote(EventControler $event_controler) {
        $Parsedown = new Parsedown();
        $this->getId((int)$event_controler->idnote);
        //$this->query("select note from contact_note where idcontact_note = " . $idnote . " and idcontact = ".$_SESSION['do_cont']->idcontact);
        $event_controler->addOutputValue($Parsedown->text($this->formatNoteDisplayFull()));
    }

    function getCompanyNotes($idcompany) {
        $company_owner = $this->getCompanyOwner($idcompany);
        $q = new sqlQuery($this->getDbCon());
        if($company_owner == $_SESSION['do_User']->iduser ){ 
            $q->query("select idcontact from contact where idcompany = ".$idcompany) ;
        }else{
            $q->query("select contact.idcontact from contact 
					  LEFT JOIN 
                      contact_sharing on contact.idcontact = contact_sharing.idcontact
                      where contact_sharing.idcoworker =
                      ".$_SESSION['do_User']->iduser." AND idcompany = ".$idcompany) ;
        }
       if ($q->getNumRows()) { 
          $contact_arr = Array();
          while($q->fetch()){
            $contact_arr[] = $q->getData("idcontact");
          }
        }
        if(count($contact_arr) > 0){
          $comma_separated = implode(",", $contact_arr);
          $this->query("select * from contact_note where  idcompany = " . $idcompany.
                      " OR (idcontact IN (".$comma_separated.") )
                        order by date_added desc"
                    );
        }else{
             $this->query("select * from contact_note where idcompany = " . $idcompany
                    );
        }
    }

    function getConmanyNoteDeleted($idcompany){
           $company_owner = $this->getCompanyOwner($idcompany);
        $q = new sqlQuery($this->getDbCon());
        if($company_owner == $_SESSION['do_User']->iduser ){ 
            $q->query("select idcontact from contact where idcompany = ".$idcompany) ;
        }else{
            $q->query("select contact.idcontact from contact 
                      LEFT JOIN 
                      contact_sharing on contact.idcontact = contact_sharing.idcontact
                      where contact_sharing.idcoworker =
                      ".$_SESSION['do_User']->iduser." AND idcompany = ".$idcompany) ;
        }
       if ($q->getNumRows()) { 
          $contact_arr = Array();
          while($q->fetch()){
            $contact_arr[] = $q->getData("idcontact");
          }
        }
        if(count($contact_arr) > 0){
          $comma_separated = implode(",", $contact_arr);
          $this->query("select * from contact_note where ( idcompany = " . $idcompany.
                      " OR (idcontact IN (".$comma_separated.") ) ) AND is_delete = 1
                        order by date_added desc"
                    );
        }else{
             $this->query("select * from contact_note where idcompany = " . $idcompany." AND is_delete = 1"
                    );
        }
    }

    function eventAjaxGetCompanyNote(EventControler $event_controler) {
        $idnote = $event_controler->idnote;
        $this->query("select note from contact_note where idcontact_note = " . $idnote);
        $event_controler->addOutputValue(nl2br($this->getData('note')));
    }

    function getCompanyOwner($idcompany){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select iduser from contact where idcompany = ".$idcompany);
        while($q->fetch()){
            $iduser = $q->getData("iduser");
          }
        return $iduser;
    }

    /**
     * Overload the add() method to the contact activity
     */

    function add() {
	  if (strlen($this->date_added) == 0) { $this->date_added = date("Y-m-d"); }
      parent::add();    
        // This below was a try that didn't worked as to global. Created the eventFormatNoteInsert eventaction instead.
        // $this->note = $this->quote(htmlentities($this->note));  
        // $this->query("INSERT INTO ".$this->getTable()." (`idcontact`, `note`, `date_added`, `document`, `idcompany`, `iduser`) 
        //                 VALUES ('".$this->idcontact."','".$this->note."','".$this->date_added."','".$this->document."','".$this->company."','".$this->iduser."')");
        // $this->setPrimaryKeyValue($this->getInsertId($this->getTable(), $this->getPrimaryKey()));
      if ($this->getPrimaryKeyValue() > 0) {
         $contact = $this->getParentContact();
         $contact->setActivity();
      }          
    }            

    function eventDelNoteDrafts(EventControler $event_controler){
        $do_note_draft = new NoteDraft();
        if($do_note_draft->isDraftExist($_SESSION['ContactNoteEditSave']->idcontact,'contact_note') !== false){
            $do_note_draft->deleteDraftWithType($_SESSION['ContactNoteEditSave']->idcontact,'contact_note');
        }
    }


    function eventSetFollowUpTaskReminder(EventControler $evtcl){
        $_SESSION['in_page_message'] = 'follow_up_task';
        
    }


    function deleteContactNotes($idnote){
       $this->getId($idnote);
       //get the file name to delete from disk
	   $this->deleteAttachmentFromDisk($this->document);
       $contact = $this->getParentContact();
       $contact->setActivity();
       $this->delete();
    }

    function eventdelContactNoteById(EventControler $event_controler){
      $idnote = $event_controler->getParam("id");
      $this->deleteContactNotes($idnote);
    }

    function getUserRelatedContactNotes(){
     // $this->query("select * from contact_note where iduser = ".$_SESSION['do_User']->iduser ." Order by idcontact_note desc");
      $qry = "select CONCAT('projectdiscuss_',pd.idproject_discuss) as id,pd.priority, pd.date_added, pd.discuss as note, pd.document,CONCAT('projtask_',pd.idproject_task) as ref1,CONCAT('proj_',pd.idproject) as ref2 ,pd.iduser from task 
            inner join project_task as pt on task.idtask = pt.idtask 
            inner join project_discuss as pd on pd.idproject_task = pt.idproject_task 
            where task.iduser = ".$_SESSION['do_User']->iduser."
            union 
            select CONCAT('contactnote_',idcontact_note) as id,priority, date_added, note, document,CONCAT('cont_',idcontact) as ref1, CONCAT('comp_',idcompany) as ref2 ,iduser
            from contact_note 
            where iduser = ".$_SESSION['do_User']->iduser."
            order by priority desc, date_added desc
";
      $this->query($qry);

    }

    function eventAjaxGetEditForm(EventControler $evtcl){ 
        $_SESSION['ContactNoteEditSave']->setApplyRegistry(false, "Form");
        $html = '';
        $curdiv = $evtcl->curdiv;
        $this->getId($evtcl->idnote);
        $vis_user = ($this->note_visibility=='user') ? 'checked':'' ;
        $vis_user_cw = ($this->note_visibility=='user coworker') ? 'checked':'' ;
        $vis_user_cont = ($this->note_visibility=='user contact') ? 'checked':'' ;
        $vis_user_cw_cont = ($this->note_visibility=='user coworker contact') ? 'checked':'' ;

        $do_cont = new Contact();
        $contact_fullname = $do_cont->getContactName($evtcl->idcontact);

        if($this->iduser == $_SESSION['do_User']->iduser ){
            $note_val = $this->note;
            $e_edit = new Event("ContactNoteEditSave->eventUpdate");
            $e_edit->setLevel(110);
            $e_edit->addEventAction("mydb.gotoPage", 111);
            $e_edit->addEventAction("ContactNoteEditSave->eventHTMLCleanUp", 109);
            $e_edit->setGotFile(true);
            $html .= $e_edit->getFormHeader();
            $html .= $e_edit->getFormEvent();
            $_SESSION['ContactNoteEditSave']->setRegistry("ofuz_add_contact_note");
            $_SESSION['ContactNoteEditSave']->setApplyRegistry(true, "Form");

            $html .= '<br />'._('Note :').'<br /><textarea id="note_edit" name = "fields[note]" rows="3" cols="110">'.$note_val.'</textarea><br />';
            $html .= '<div width="100%">';
            $html .= '<div id="edit_note_more" style="position:relative;float:left;text-align:left;width:50%"><a href="#" onclick ="fnEditNoteMoreOpts();return false;">'._('More Options').'</a></div>';
            $html .= '<div style="position:relative;float:left;text-align:left;width:50%"><a href="javascript:;" onclick="showProjectList();">'._('Attached to a project').'</a>';
            $html .= '<div id="cp_prj_list" style="position:relative;display:none;">';

            $do_prjs = new Project();
            $do_prjs->getAllProjects("open");
            $num_prjs = $do_prjs->getNumRows();
            $html .= '<select name="cpy_prjs" id="cpy_prjs" onchange="populateTasks();">';
            if($num_prjs > 0) {
              $html .= '<option value="">'._('Select Project').'</option>';
            } else {
              $html .= '<option value="">'._('You do not have Project.').'</option>';
            }
            while($do_prjs->next()) {
              $html .= '<option value="'.$do_prjs->getData("idproject").'">'.$do_prjs->getData("name").'</option>';
            }
            $html .= '</select>';
            $html .= '</div>';
            $html .= '<div id="cp_prj_tasks"></div>';

            $html .= '</div></div>';

            $html .= '<div class="div_right" id="edit_note_more_opts" style="display: none;"> ';
            $html .= 'File : '.$_SESSION['ContactNoteEditSave']->document.'<br /><br />';
            $html .= '</div>';
            $html .= '<div id="edit_note_more_opts_vis" style="text-align: left; width: 50%;display: none;"> ';
            $html .= _('Who can view this note:').'<br />';
            $html .= '<input type="radio" name="fields[note_visibility]" value="user" '.$vis_user.'> '._('Just me').' <br />';
            $html .= '<input type="radio" name="fields[note_visibility]" value="user coworker" '.$vis_user_cw.'> '._('My Co-Workers and I').'<br />';
            $html .= '<input type="radio" name="fields[note_visibility]" value="user contact" '.$vis_user_cont.'> '.$contact_fullname._(' and I').'<br />';
            $html .= '<input type="radio" name="fields[note_visibility]" value="user coworker contact" '.$vis_user_cw_cont.'> '._('Me, Co-Workers and ').$contact_fullname;

            $html .= '</div>';
            $html .= '<div style="text-align:right">';
            $html .= '<input type="submit" name="Save" value = "'._('Save').'">&nbsp;&nbsp;<a href="#" onclick = "fnCancelEdit(\''.$curdiv.'\','.$evtcl->idnote.');return false;">'._('close').'</a>';
            $html .= '</div>';


            $html .='</form>';
            $evtcl->addOutputValue($html);
        }else{
            $msg = new Message(); 
            $msg->getMessage("unauthorized_note_edit");
            $html .= $msg->content.'<br /><br />';
            $html .= '<a href="#" onclick = "fnCancelEdit(\''.$curdiv.'\','.$evtcl->idnote.');return false;">'._('close').'</a>';
            $evtcl->addOutputValue($html);
        }
    }


    /**
     * eventFormatNoteInsert
     * This event format the note upon insert in the database.
     * Should be run before eventAdd or eventUpdate
     * It will modify the event_controler values.
     */ 
    function eventFormatNoteInsert(EventControler $event_controler) {
          $Parsedown = new Parsedown();
          $do_NoteDraft = new NoteDraft();
          $idnote_draft = $do_NoteDraft->isDraftExist($this->idcontact,'contact_note');
          if($idnote_draft){
            $do_NoteDraft->getId($idnote_draft);
            $do_NoteDraft->delete();  
          }

          $fields = $event_controler->fields;
          $fields['note'] = $Parsedown->text(htmlentities($fields['note']));
          $event_controler->fields = $fields;
    }

    // Deprecate note used anymore
    function createPortalNoteAddForm($idcontact,$portal_code){
      $this->setRegistry("portal_note_form");
      $f_taskForm = $this->prepareSavedForm("portal_note_form");
      $f_taskForm->setFormEvent($this->getObjectName()."->eventAdd", 1005);
      $f_taskForm->addEventAction($this->getObjectName()."->eventSetUserId", 10);
      //$f_taskForm->setGotFile(true);
      $f_taskForm->addParam("idcontact",$idcontact);
      $f_taskForm->setAddRecord();
      $dispError = new Display("contact_portal.php");
      $dispError->addParam("pc", $portal_code) ;
      $f_taskForm->setUrlNext($dispError) ; 
      //$f_taskForm->setUrlNext("tasks.php");
      $f_taskForm->setForm();
      $f_taskForm->execute();
    }

    /**
     * This function is needed here to retrieve the full
     * Name of the note owner/creator as it can be the 
     * the contact it self of a user.
     * @param interger id
     */

    function getNoteOwnerFullName() {
      if(empty($this->iduser)){
          $do_contact = $this->getParentContact();
          $added_by =  $do_contact->firstname.' '.$do_contact->lastname;
      }else{
           $do_user = new User();
           $added_by = $do_user->getFullName($this->iduser);     
      }
      return $added_by;
    }

    function eventSetUserId(EventControler $evtcl) {
      $fields = $evtcl->getParam("fields");
      $idcontact = $evtcl->getParam("idcontact");
      $q = new sqlQuery($this->getDbCon());
      //$iduser = 17;
      $fields["iduser"] = 0;
      $fields["idcontact"] = $idcontact;
      $evtcl->updateParam("fields", $fields) ;
      //print_r($fields); exit;
    }

    /*
     * Receives the idcontact_note for a note and changes the priority of that note in the DB
     */
    function eventPrioritySortNotes(EventControler $event_controler) {
        $this->query('SELECT priority FROM contact_note WHERE idcontact_note = '.$event_controler->idnote);
        $newpriority = ($this->getData('priority') > 0) ? 0 : 1;
        $this->query('UPDATE contact_note SET priority = '.$newpriority.' WHERE idcontact_note = '.$event_controler->idnote);
    }

	function getTotalNumContactNotesForUser($iduser) {
		$q = new sqlQuery($this->getDbCon());
		$sql = "SELECT COUNT(idcontact_note) AS total_contact_notes 
				FROM `{$this->table}` 
				WHERE `iduser` = {$iduser}
			   ";
		$q->query($sql);
		if($q->getNumRows()) {
			$q->fetch();
			return $q->getData("total_contact_notes");
		} else {
			return "0";
		}
	}

	function autoLoadNotesOnScrollDown() {
    $Parsedown = new Parsedown();
		if($_SESSION['ContactNotes']->notes_count >= $_SESSION['ContactNotes']->sql_qry_start) {
			$_SESSION['ContactNotes']->sql_qry_start = $_SESSION['ContactNotes']->sql_qry_start + $_SESSION['ContactNotes']->sql_view_limit;

			echo '<script type="text/javascript">
			$(document).ready(function() {
				$("div[id^=notetext]").hover(function(){$("div[id^=trashcan]",this).show("fast");},function(){$("div[id^=trashcan]",this).hide("fast");});
			});
			</script>';

			$this->getContactNotes($_SESSION['ContactNoteEditSave']->idcontact);

			if ($this->getNumRows()) {
				$note_count = 0;
				while ($this->next()) {
					$file = '';
					$preview_note = '';
					if($this->document != ''){
						$doc_name = $this->document;
						$doc_name = str_replace("  ","%20%20",$this->document);
						$doc_name = str_replace(" ","%20",$doc_name);
						//$file_url = "/files/".$this->document;
						$file_url = "/files/".$doc_name;
						$file = '<br /><a href="'.$file_url.'" target="_blank">'.$this->document.'</a>';
					}
					//$note_text = $this->note;
					$note_text = $this->formatNoteDisplayShort();
					//if (substr_count($note_text, '<br />') > 4) {
					//	$preview_note = preg_replace('/(.*?<br \/>.*?<br \/>.*?<br \/>.*?<br \/>)(.*)/','$1',str_replace("\n",'',$note_text)).' ';
					//} else if (strlen($note_text) > 500) {
					//	$preview_note = substr($note_text, 0, 500).' ';
					//}
					$added_by = $_SESSION['do_User']->getFullName($this->iduser);
					$e_PrioritySort = new Event('ContactNotes->eventPrioritySortNotes');
					$e_PrioritySort->addParam('goto', 'contact.php');
					$e_PrioritySort->addParam('idnote', $this->idcontact_note);
					$star_img_url = '<img src="/images/'.($this->priority > 0?'star_priority.gif':'star_normal.gif').'" class="star_icon" width="14" height="14" alt="'._('Star this note to move it on top').'" />';
					if (is_object($_SESSION["ContactNotes"])) {
						$e_note_del = new Event("ContactNotes->eventTempDelNoteById");
					}
					$test = 'test';
					$e_note_del->addParam("goto", "contact.php");
					$e_note_del->addParam("id", $this->idcontact_note);
					$e_note_del->addParam("context", "ContactNote");
					$del_img_url = _('delete').' <img src="/images/delete.gif" width="14px" height="14px" alt="" />';
					echo '<div id="notetext', $this->idcontact_note, '" class="vpad10">';
					echo '<div style="height:24px;position:relative;"><div class="percent95"><img src="/images/note_icon.gif" class="note_icon" width="16" height="16" alt="" />',$e_PrioritySort->getLink($star_img_url, ' title="'._('Star this note to move it on top').'"');
					echo '<b>'.date('l, F j', strtotime($this->date_added)).'</b>&nbsp;(Added By :&nbsp;'.$this->getNoteOwnerFullName().')</div> 
					<div id="trashcan', $note_count, '" class="deletenote" style="right:0;">'.'<a href="#"  onclick="fnEditNote(\'notetext'.$this->idcontact_note.'\','.$this->idcontact_note.');return false;">'._('edit').'</a>&nbsp;|&nbsp;'.$e_note_del->getLink($del_img_url, ' title="'._('Delete this note').'"').'</div></div>';
					if ($this->is_truncated != '') {
						echo '<div id="notepreview',$this->idcontact_note,'">',$Parsedown->text($note_text),'<br /><a href="#" onclick="showFullNote(',$this->idcontact_note,'); return false;" >'._('more ...').'</a><br /></div>';
					} else {
						echo $Parsedown->text($note_text);
					}
                                        $note_count++;
					echo $this->formatDocumentLink().'</div>
                                        <div id="e'.$this->idcontact_note.'" style="display: none;" class="note_edit_box"></div>
                                        <div id="'.$this->idcontact_note.'" class="message_box"></div>';
				}
				
			}			
		}
	}


	function getUserContactsFromNotesDaily($date_added) {
		$sql = "SELECT cn.idcontact,CONCAT(c.firstname,' ',c.lastname) AS cname
				FROM
				contact_note cn INNER JOIN contact c ON cn.idcontact = c.idcontact
				WHERE
				cn.iduser = {$_SESSION['do_User']->iduser}
				AND cn.date_added = '{$date_added}'
				GROUP BY cn.idcontact
			   ";
		$this->query($sql);
	}

	function getUserContactNotesOnDate($date_added, $idcontact) {
 		$sql = "SELECT *
 				FROM contact_note
 				WHERE
 				idcontact = {$idcontact}
				AND date_added = '{$date_added}'				
 			   ";

		$this->query($sql);
	}

	function getUserContactsFromNotesMonthly($report_year,$month) {
		/*if($_SESSION['adm_project_report_discuss']->week_range != '' && $_SESSION['adm_project_report_discuss']->week_start_date != '' && $_SESSION['adm_project_report_discuss']->week_end_date != ''){
		  $sql = "SELECT cn.idcontact,CONCAT(c.firstname,' ',c.lastname) AS cname
				  FROM
				  contact_note cn INNER JOIN contact c ON cn.idcontact = c.idcontact
				  WHERE
				  cn.iduser = {$_SESSION['do_User']->iduser}
				  AND cn.date_added BETWEEN '{$_SESSION['adm_project_report_discuss']->week_start_date}' AND '{$_SESSION['adm_project_report_discuss']->week_end_date}'
				  GROUP BY cn.idcontact
			    ";
		} else {
		  $sql = "SELECT cn.idcontact,CONCAT(c.firstname,' ',c.lastname) AS cname
				  FROM
				  contact_note cn INNER JOIN contact c ON cn.idcontact = c.idcontact
				  WHERE
				  cn.iduser = {$_SESSION['do_User']->iduser}
				  AND (YEAR(cn.date_added) = '{$report_year}' AND MONTH(cn.date_added) = '{$month}')
				  GROUP BY cn.idcontact
			    ";
		}*/

  if($_SESSION['adm_project_report_discuss']->week_range != '' && $_SESSION['adm_project_report_discuss']->week_start_date != '' && $_SESSION['adm_project_report_discuss']->week_end_date != ''){
      $sql = "SELECT cn.idcontact,SUM(cn.hours_work) as monthly_hours,CONCAT(c.firstname,' ',c.lastname) AS cname
      FROM contact_note cn 
      INNER JOIN contact c ON cn.idcontact = c.idcontact
      WHERE
      cn.iduser = {$_SESSION['do_User']->iduser}
      AND cn.date_added BETWEEN '{$_SESSION['adm_project_report_discuss']->week_start_date}' AND '{$_SESSION['adm_project_report_discuss']->week_end_date}'
      GROUP BY cn.idcontact
      HAVING monthly_hours > 0
      ORDER BY cname
       ";
  } else {
      $sql = "SELECT cn.idcontact,SUM(cn.hours_work) as monthly_hours,CONCAT(c.firstname,' ',c.lastname) AS cname
      FROM
      contact_note cn INNER JOIN contact c ON cn.idcontact = c.idcontact
      WHERE
      cn.iduser = {$_SESSION['do_User']->iduser}
      AND (YEAR(cn.date_added) = '{$report_year}' AND MONTH(cn.date_added) = '{$month}')
      GROUP BY cn.idcontact
      HAVING monthly_hours > 0
      ORDER BY cname
       ";
  }
		$this->query($sql);
	}

	function getUserContactNotesMonthlyHours($report_year, $month, $idcontact) {
		if($_SESSION['adm_project_report_discuss']->week_range != '' && $_SESSION['adm_project_report_discuss']->week_start_date != '' && $_SESSION['adm_project_report_discuss']->week_end_date != ''){
		  $sql = "SELECT SUM(hours_work) AS monthly_hours
				  FROM contact_note
				  WHERE
				  idcontact = {$idcontact}
				  AND date_added BETWEEN '{$_SESSION['adm_project_report_discuss']->week_start_date}' AND '{$_SESSION['adm_project_report_discuss']->week_end_date}'
				  GROUP BY idcontact
			    ";
		} else {
		  $sql = "SELECT SUM(hours_work) AS monthly_hours
				  FROM contact_note
				  WHERE
				  idcontact = {$idcontact}
				  AND YEAR(date_added) = '{$report_year}' AND MONTH(date_added) = '{$month}'
				  GROUP BY idcontact
			    ";
		}
  //echo $sql.'<br />';
		$q = new sqlQuery($this->getDbCon());
		$q->query($sql);
		if($q->getNumRows()) {
			$q->fetch();
			return $q->getData("monthly_hours");
		}
	}

  /*
      Check if the document is related to the contact
  */
  function isDocumentForContact($idcontact,$file){
      $q = new sqlQuery($this->getDbCon()); 
      $q->query("select idcontact_note from ".$this->table." Where idcontact = ".$idcontact." AND document = '".$file."'");
      if($q->getNumRows()){
          return true;   
      }else{ 
          return false;
      }
  }

  function getContactTopNotes($idcontact) {
    $this->query("select * from contact_note where idcontact = " . $idcontact ." and note <> '' order by priority desc, date_added desc, idcontact_note desc limit 4");
  }

}
?>
