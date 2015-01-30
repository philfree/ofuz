<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  /**
   * Class ProjectDiscuss
   * This is the Class to manage the the project discuss.
   * Copyright 2001 - 2008 SQLFusion LLC, Author: Philippe Lewicki, Abhik Chakraborty,Jay Link info@sqlfusion.com 
   *
   * 
		+-------------------+--------------+------+-----+---------+----------------+
		| Field             | Type         | Null | Key | Default | Extra          |
		+-------------------+--------------+------+-----+---------+----------------+
		| idproject_discuss | int(10)      | NO   | PRI | NULL    | auto_increment | 
		| idproject_task    | int(10)      | NO   |     |         |                | 
		| idtask            | int(10)      | NO   |     |         |                | 
		| idproject         | int(10)      | NO   |     |         |                | 
		| discuss           | text         | YES  |     | NULL    |                | 
		| date_added        | date         | NO   |     |         |                | 
		| document          | varchar(254) | NO   |     |         |                | 
		| iduser            | int(10)      | NO   |     | 0       |                | 
		| drop_box_sender   | varchar(100) | NO   |     |         |                | 
		| priority          | int(1)       | NO   |     | 0       |                | 
		| hours_work        | float(10,2)  | NO   |     | 0.00    |                | 
		+-------------------+--------------+------+-----+---------+----------------+
   * 
   */

 
class ProjectDiscuss extends Note {

    public $table = 'project_discuss';
    protected $primary_key = 'idproject_discuss';
    public $report_date = '';
    public $time_spent_on_task = false;
    public $set_user_search = false;
    public $for_user ;
    public $report_month =  '';
    public $sql_view_limit = 50;
    public $sql_qry_start = 0;
    public $sql_qry_end = 50;
    public $prj_discussion_count = 0;
    public $report_year = '';
    public $week_start_date = '';
    public $week_end_date = '';
    public $week_range = '';

    function __construct(sqlConnect $conx=NULL, $table_name="") {
       parent::__construct($conx, $table_name);
       $this->setLogRun(RADRIA_LOG_RUN_OFUZ);
    }


    /**
      * Overriding add() method from the parent class
    */
    
    function add() {
        
        $Parsedown = new Parsedown();
        $do_NoteDraft = new NoteDraft();
        $idnote_draft = $do_NoteDraft->isDraftExist($this->idproject_task,'project_discuss');
        if($idnote_draft){
            $do_NoteDraft->getId($idnote_draft);
            $do_NoteDraft->delete();  
        }
	
        if (get_magic_quotes_gpc()) {
            $project_discuss = $this->discuss;
        }
        else {
            $project_discuss = addslashes($this->discuss);
        }

        $this->query("INSERT INTO project_discuss (idproject_task,discuss,date_added,document,hours_work,iduser,discuss_edit_access,type)
                      VALUES 
                      (".$this->idproject_task.",'".$Parsedown->text($project_discuss)."','".$this->date_added."','".$this->document."','".$this->hours_work."','".$this->iduser."','".$this->discuss_edit_access."','Note')");
                      
        $this->setPrimaryKeyValue($this->getInsertId($this->getTable(), $this->getPrimaryKey()));
        //$Parsedown->free();
    }

    /**
       * Method to convert the content to UTF
       * @note Depricated on Ofuz 0.6 and will not be abailable on further version
    */
    function convert_to_utf($content){
        include_once('class/utf8.class.php');
        $do_utf8 = new utf8(); 
        $content = $do_utf8->convert($content,'windows-1252',"UTF-8");
        $content = $do_utf8->convert($content,'US-ASCII',"UTF-8");
        $content = $do_utf8->convert($content,'ISO-8859-1',"UTF-8");
        $content = $do_utf8->convert($content,'ISO-8859-2',"UTF-8");
        $content = $do_utf8->convert($content,'ISO-8859-3',"UTF-8");
        $content = $do_utf8->convert($content,'ISO-8859-4',"UTF-8");
        $content = $do_utf8->convert($content,'ISO-8859-5',"UTF-8");
        $content = $do_utf8->convert($content,'ISO-8859-6',"UTF-8");
        $content = $do_utf8->convert($content,'ISO-8859-7',"UTF-8");
        $content = $do_utf8->convert($content,'ISO-8859-8',"UTF-8"); 
        $content = $do_utf8->convert($content,'ISO-8859-8-i',"UTF-8");
        $content = $do_utf8->convert($content,'ISO-8859-9',"UTF-8");
        $content = $do_utf8->convert($content,'ISO-8859-15',"UTF-8");
        return $content;
    }

    /**
      * Returns true if $string is valid UTF-8 and false otherwise.
    */
    function is_utf8($string) {

        // From http://w3.org/International/questions/qa-forms-utf-8.html
        return preg_match('%^(?:
            [\x09\x0A\x0D\x20-\x7E]            # ASCII
          | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
          |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
          | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
          |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
          |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
          | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
          |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
            )*$%xs', $string);
   
    } // function is_utf8


    /**
      * Get the Task discussion for the task
      * @param idproject_task
    */
    function getProjectTaskDiscussions($idproject_task) {
      echo "select * from project_discuss where idproject_task = ".$idproject_task." order by date_added desc";
        $this->query("select * from project_discuss where idproject_task = ".$idproject_task." order by date_added desc");
    }

    /**
      * Formating the Discussion item before display
      * @param $text -- STRING
      * @return formated text
    */

    function formatDiscussionItemDisplay($text) {
       $ret = $this->formatNoteDisplayShort($text); 
       return ($ret);
    }

    
    /**
      * Ge the discussion owner name
      * @return username added the discussion
    */

    function getDiscussionItemOwnerFullName() {
        if(empty($this->iduser)){
            $do_contact = $this->getParentContact();
            $added_by =  $do_contact->firstname.' '.$do_contact->lastname;
        } else {
             $do_user = new User();
             $added_by = $do_user->getFullName($this->iduser);     
        }
        return $added_by;
    }

    /**
      * Delete a project discussion note
      * @param $idnote -- INT
    */
    function deleteProjectDiscussionNote($idnote){
       $this->getId($idnote);
       //get the file name to delete from disk
       $this->deleteAttachmentFromDisk($this->document);
       $contact = $this->getParentContact();
       $contact->setActivity();
       $this->delete();
    }

     /**
      * Generate the Ajax Edit form
      * @param $evtcl -- Object
    */
     function eventAjaxGetEditForm(EventControler $evtcl){ 
	
        $_SESSION['ProjectDiscussEditSave']->setApplyRegistry(false, "Form");
        $html = '';
        $curdiv = $evtcl->curdiv;
        $this->getId($evtcl->id);
        $user_checked = '';
        $user_coworker_checked = '';
        if($this->discuss_edit_access == ''){ $user_checked  = 'checked' ;}
        if($this->discuss_edit_access == 'user'){ $user_checked  = 'checked' ;}
        if($this->discuss_edit_access  == 'user coworker'){ $user_coworker_checked  = 'checked' ;}

        $note_val = $this->discuss;
        $e_edit = new Event("ProjectDiscussEditSave->eventUpdate");
        $e_edit->setLevel(110);
        $e_edit->addEventAction("mydb.gotoPage", 111);
        $e_edit->addEventAction('ProjectDiscussEditSave->eventHTMLCleanUp', 109);
        $e_edit->setGotFile(true);

        if($this->iduser == $_SESSION['do_User']->iduser ){
            
            $html .= $e_edit->getFormHeader();
            $html .= $e_edit->getFormEvent();
            $_SESSION['ProjectDiscussEditSave']->setRegistry("ofuz_add_project_discuss");
            $_SESSION['ProjectDiscussEditSave']->setApplyRegistry(true, "Form");
            $html .= '<br />'._('Note :').'<br /><textarea id="note_edit" name="fields[discuss]" rows="3" cols="100" class="dojo_textarea">'.$note_val.'</textarea><br />';
            $html .= '<div id="edit_note_more" style="text-align:left;"><a href="#" onclick ="fnEditNoteMoreOpts();return false;">'._('More Options').'</a></div>';
            $html .= '<div id="edit_note_more_opts" style="display: none;"> ';
            $html .= _('Hours Worked : ').$_SESSION['ProjectDiscussEditSave']->hours_work.'<br />';
            $html .= _('File : ').$_SESSION['ProjectDiscussEditSave']->document.'<br />';
            $html .=  _('Who can edit ? ').'<input type="radio" name="fields[discuss_edit_access]" value="user" '.$user_checked.'>'._('Just me').'&nbsp;&nbsp;';
            $html .='<input type="radio" name="fields[discuss_edit_access]" value="user coworker" '.$user_coworker_checked.'>'._('My Co-Workers and I').'&nbsp;&nbsp;';
            $html .= '</div>';
            $html .= '<div style="text-align:right">';
            $html .= '<input type="submit" name="Save" value="'._('Save').'">&nbsp;&nbsp;<a href="javascript:;" onclick="fnCancelEdit(\''.$curdiv.'\','.$evtcl->id.');return false;">'._('close').'</a>';
            $html .= '</div>';
            $html .='</form>';
            $evtcl->addOutputValue($html);
        }else{
	    
	    if($this->discuss_edit_access  == 'user coworker'){
          $html .= $e_edit->getFormHeader();
          $html .= $e_edit->getFormEvent();
          $_SESSION['ProjectDiscussEditSave']->setRegistry("ofuz_add_project_discuss");
          $_SESSION['ProjectDiscussEditSave']->setApplyRegistry(true, "Form");
          $html .= '<br />'._('Note :').'<br /><textarea id="note_edit" name="fields[discuss]" rows="3" cols="100" class="dojo_textarea">'.$note_val.'</textarea><br />';
          $html .= '<div style="text-align:right">';
          $html .= '<input type="submit" name="Save" value="'._('Save').'">&nbsp;&nbsp;<a href="javascript:;" onclick="fnCancelEdit(\''.$curdiv.'\','.$evtcl->id.');return false;">'._('close').'</a>';
          $html .= '</div>';
          $html .='</form>';
          $evtcl->addOutputValue($html);
          }else{
          $msg = new Message(); 
          $msg->getMessage("unauthorized_note_edit");
          $html .= $msg->content.'<br /><br />';
          $html .= '<a href="#" onclick = "fnCancelEdit(\''.$curdiv.'\','.$evtcl->id.');return false;">'._('close').'</a>';
          $evtcl->addOutputValue($html);
          }
        }
    }
    
    /**
      * Set the registry to false so as to get the data from getId is alreay registry is set
    */

    function eventAjaxSetRegFalse(EventControler $evtcl) {
	    $_SESSION['ProjectDiscussEditSave']->setApplyRegistry(false, "Form");
    }

    /**
     * eventFormatDiscussInsert
     * This event format the note upon insert in the database.
     * Should be run before eventAdd or eventUpdate
     * It will modify the event_controler values.
     */ 

    function eventFormatDiscussInsert(EventControler $event_controler) {
        $fields = $event_controler->fields;
        if(!$this->is_utf8($fields['discuss'])){
           $fields['discuss'] = $this->convert_to_utf($fields['discuss']);
        }
        $fields['discuss'] = htmlentities($fields['discuss'],ENT_QUOTES,'UTF-8');
        $event_controler->fields = $fields;
        //print_r ($fields['discuss']);exit;
    }

    /**
      * Event method to delete a project discussion
      * @param $event_controler -- Object
    */

    function eventDelProjectDiscussionNoteById(EventControler $event_controler) {
        $idnote = $event_controler->getParam('id');
        $this->deleteProjectDiscussionNote($idnote);
    }
    
    /**
      * Get the task discussion with the Ajax call
      * @param $event_controler -- Object
    */
    function eventAjaxProjectTaskDiscussion(EventControler $event_controler) {
        $this->getId((int)$event_controler->idnote);
        //$this->query("select discuss from project_discuss where idproject_discuss = ".$idnote." and idproject_task = ". $_SESSION['do_project_task']->idproject_task);
        $event_controler->addOutputValue($this->formatNoteDisplayFull($this->discuss));
    }
    /** 
     * eventSendDiscussMessageByEmail
     * This will send an email to all the project participant 
     * Using Radria_Emailer and an email template stored in the emailtemplate table.
     * This method uses the currently open project persistant object. 
     * @param Eventcontroler
     */
    function eventSendDiscussMessageByEmail(EventControler $event_controler) {
       $Parsedown = new Parsedown();
      $this->setLog("\n eventSendDiscussMessageByEmail: starting (".date("Y/m/d H:i:s"));

      /*$_SESSION['do_project_task']->getId($event_controler->idproject_task);
        $_SESSION['do_project']->getId($event_controler->idproject);*/

        $idproject_task = $event_controler->fields['idproject_task'];  
        if(empty($idproject_task) || $idproject_task==''){
            $idproject_task = $event_controler->ofuz_idprojecttask;
        }
        $_SESSION["do_project"] = $_SESSION['projectsession_'.$idproject_task];         
        $_SESSION["do_project_task"]  = $_SESSION['projecttasksession_'.$idproject_task];
        //echo $idproject_task;exit;

          try {

                 $co_workers = $_SESSION["do_project"]->getProjectCoWorkers();              
               
              if ($co_workers !== false) {
                  $email_template = new EmailTemplate("ofuz_project_discussion");
                  $email_nudge = new EmailTemplate("ofuz_project_discussion_nudge");
                  $project_task_url = $GLOBALS['cfg_ofuz_site_http_base'].'Task/'.$_SESSION['do_project_task']->idproject_task;
                  $project_link =  $GLOBALS['cfg_ofuz_site_http_base'].'Project/'.$_SESSION['do_project']->idproject;

                  if($event_controler->fields['document'] !=''){ // If a file is attached
                      $doc_link = $GLOBALS['cfg_ofuz_site_http_base'].'files/'.$event_controler->fields['document'];
                      $doc_text = '<br />'._('Attachment').': <a href="'.$doc_link.'"> '.$event_controler->fields['document'].'</a>';
                  }else{$doc_text = '';}
                  $message_html = nl2br(stripslashes($Parsedown->text($event_controler->fields['discuss']).$doc_text));
                  $message_txt =  stripslashes($event_controler->fields['discuss'].$doc_text);
                  $email_data = Array('project-name' => $_SESSION['do_project']->name,
		                    'project-link' => $project_link,
                                    'discussion-owner' => $_SESSION['do_User']->getFullName(),
                                    'task_name' => $_SESSION['do_project_task']->task_description,
				    'task_category' => $_SESSION['do_project_task']->task_category,
                                    'message_txt' => $message_txt,
                                    'message_html' => $message_html,
                                    'project-task-link' => $project_task_url
                                    );
                  //print_r($email_data);exit;
                 $do_discussion_email_setting = new DiscussionEmailSetting();
                 if ($_SESSION['do_project_task']->drop_box_code < 10 || count($co_workers) > 19) {
                    foreach ($co_workers as $co_worker) {
                            $global_email_alert = $_SESSION['UserSettings']->getSettingValue('task_discussion_alert',$co_worker['idcoworker']);
                            $global_email_alert_on = true;
                            if(!$global_email_alert){
                                $global_email_alert_on = true; // means user has not set on/off in the settings and on by default
                            }else{
                                if(is_array($global_email_alert)){
                                    if($global_email_alert["setting_value"] == 'Yes'){
                                        $global_email_alert_on = true;
                                    }else{
                                        $global_email_alert_on = false;
                                    }
                                }  
                            }
                        //if($_SESSION['UserSettings']->global_task_discussion_alert  != 'No' && !$do_discussion_email_setting->isDiscussionAlertSet($_SESSION['do_project_task']->idproject,'Project',$co_worker['idcoworker']) ){
                        if($global_email_alert_on && !$do_discussion_email_setting->isDiscussionAlertSet($_SESSION['do_project_task']->idproject_task,'Project',$co_worker['idcoworker']) ){
                            $emailer = new Radria_Emailer('UTF-8');
                            $emailer->setEmailTemplate($email_template);
                            //$emailer->cleanup();
                            //$emailer->setFrom('support@sqlfusion.com' , 'Ofuz.net');
                            $email_data['firstname'] = $co_worker['firstname'];
                            $emailer->mergeArray($email_data);
                            $this->setLog("\n Sending email to:".$co_worker['email']);
                            $emailer->addTo($co_worker['email']);
                            $emailer->send();
                        }                        
                    }
                } else {
			$co_workers_nudged = Array();
			$co_workers_nudged_emails = Array();
                        foreach ($co_workers as $co_worker) {
                                if ( preg_match("/@".$co_worker['firstname']."/i", $event_controler->fields['discuss'])
                                  || preg_match("/@".$co_worker['lastname']."/i", $event_controler->fields['discuss'])) {
                                          //$co_workers_nudged[] = $co_worker;
                                          $co_workers_nudged_emails[] = $co_worker['email'];
                                          $this->setLog("\n Is nudged:".$co_worker['firstname']." ".$co_worker['lastname']." ".$co_worker['email']);
                                  }
                        }
					
                        $emailer = new Radria_Emailer('UTF-8');
                        $email_template->setSenderName($_SESSION['do_User']->getFullName());
                        $email_template->setSenderEmail($_SESSION['do_User']->email);
                        $emailer->setEmailTemplate($email_template);
                        $emailer->addCc($_SESSION['do_project_task']->getDropBoxEmail());
                        $emailer->addHeader("X-ofuz-emailer", $_SESSION['do_User']->iduser);
                        $emailer->mergeArray($email_data);
                        $this->setLog("\n Sending email to:".$co_workers[0]['email']);
                    
                        $someone_note_nudged = false;
                       
                        foreach ($co_workers as $to_worker) {
                            /*
                                Check if the Co-Worker is supposed to get the email
                            */
                            $global_email_alert = $_SESSION['UserSettings']->getSettingValue('task_discussion_alert',$to_worker['idcoworker']);
                            $global_email_alert_on = true;
                            if(!$global_email_alert){
                                $global_email_alert_on = true; //means user has not set on/off in the settings and on by default
                            }else{
                                if(is_array($global_email_alert)){
                                    if($global_email_alert["setting_value"] == 'Yes'){
                                        $global_email_alert_on = true;
                                    }else{
                                        $global_email_alert_on = false;
                                    }
                                }  
                            }

                           /* if($_SESSION['UserSettings']->global_task_discussion_alert  != 'No' 
                                                  && !$do_discussion_email_setting->isDiscussionAlertSet($_SESSION['do_project_task']->idproject_task,$to_worker['idcoworker']) 
                                                  && !in_array($to_worker['email'], $co_workers_nudged_emails)){*/
                                if($global_email_alert_on 
                                                  && !$do_discussion_email_setting->isDiscussionAlertSet($_SESSION['do_project_task']->idproject,'Project',$to_worker['idcoworker']) 
                                                  && !in_array($to_worker['email'], $co_workers_nudged_emails)){
                                $email_to = $to_worker['email'];
                                $emailer->addTo($email_to);
                                $someone_note_nudged = true;
                                break;
                            }
                        }
                    //$emailer->addTo($co_workers[0]['email']);
                        foreach ($co_workers as $cc_worker) {
                            /*
                            Check if the Co-Worker is supposed to get the email
                            */
                            $global_email_alert_on = true;
                            $global_email_alert = $_SESSION['UserSettings']->getSettingValue('task_discussion_alert',$cc_worker['idcoworker']);
                            if(!$global_email_alert){
                                $global_email_alert_on = true; //means user has not set on/off in the settings and on by default
                            }else{
                                if(is_array($global_email_alert)){
                                    if($global_email_alert["setting_value"] == 'Yes'){
                                        $global_email_alert_on = true;
                                    }else{
                                        $global_email_alert_on = false;
                                    }
                                }  
                            } 

                            if ($email_to != $cc_worker['email'] ) {
                              /*  if($_SESSION['UserSettings']->global_task_discussion_alert  != 'No' 
                                                          && !$do_discussion_email_setting->isDiscussionAlertSet($_SESSION['do_project_task']->idproject_task,$cc_worker['idcoworker']) 
                                                          && !in_array($cc_worker['email'], $co_workers_nudged_emails)){*/

                                  if($global_email_alert_on 
                                                          && !$do_discussion_email_setting->isDiscussionAlertSet($_SESSION['do_project_task']->idproject,'Project',$cc_worker['idcoworker']) 
                                                          && !in_array($cc_worker['email'], $co_workers_nudged_emails)){
                                  $emailer->addCc($cc_worker['email']);
                                  $someone_note_nudged = true;
                                }
                            }
                        }
			if ($someone_note_nudged) {
			   $emailer->send();
			}
             				
			if (count($co_workers_nudged_emails) > 0) {
				$email_nudge->setSenderName($_SESSION['do_User']->getFullName());
				$email_nudge->setSenderEmail($_SESSION['do_User']->email);
				
				$emailer_nudge = new Radria_Emailer();
				$emailer_nudge->setEmailTemplate($email_nudge);
				$emailer_nudge->addCc($_SESSION['do_project_task']->getDropBoxEmail());
				$emailer_nudge->addHeader("X-ofuz-emailer", $_SESSION['do_User']->iduser);
				$emailer_nudge->mergeArray($email_data);
					
				$emailer_nudge->addTo($co_workers_nudged_emails[0]);
				$this->setLog("\n Sending Nudge to:".$co_workers_nudged_emails[0]);
				for ($i=1; $i<count($co_workers_nudged_emails); $i++) {
			         	$this->setLog("\n Sending Nudge to:".$co_workers_nudged_emails[$i]);
					$emailer_nudge->addCc($co_workers_nudged_emails[$i]);                                   
				}
				$emailer_nudge->send();			
				$emailer_nudge->cleanup();	
			}
			$emailer->cleanup();				
                }
            }

          } catch (Exception $e) {
              $this->setError('ProjectDiscuss Could not send email: '.  $e->getMessage());
          } 
          $this->setLog("\n eventSendDiscussMessageByEmail: ending (".date("Y/m/d H:i:s"));

    }

    /**
     * Receives the idproject_discuss for a note and changes the priority of that note in the DB
     * @param $event_controler -- Object
     */
    function eventPrioritySortNotes(EventControler $event_controler) {
        $this->query('SELECT priority FROM project_discuss WHERE idproject_discuss = '.$event_controler->idnote);
        $newpriority = ($this->getData('priority') > 0) ? 0 : 1;
        $this->query('UPDATE project_discuss SET priority = '.$newpriority.' WHERE idproject_discuss = '.$event_controler->idnote);
    }

    /**
      * Method to get the task discussion for a project for the date set in report_date.
      * If no date is set then will return for the current date.
      * @param $idproject -- INT
    */

    function getDailyWorkDonePerProjectTask($idproject=0){
		if(empty($idproject)) { $idproject = $this->idproject; }
        if($this->report_date == ''){
            $this->report_date = date("Y-m-d");
            $where = " Where project_discuss.date_added = '".date("Y-m-d")."' AND project_task.idproject = ".$idproject;
        }else{
            $where = " where project_discuss.date_added = '".$this->report_date."' AND project_task.idproject = ".$idproject;
        }
        if($this->time_spent_on_task){ $where .= " AND project_discuss.hours_work <>'0.00' ";}
        if($this->set_user_search) { $where .= " AND project_discuss.iduser = ".$this->for_user ; }
        echo "SELECT project_discuss.discuss,project_discuss.date_added,document,
                  project_discuss.iduser,project_discuss.hours_work,
                  project_task.idproject, project_task.idtask, project_task.idproject_task FROM project_task
                  left JOIN project_discuss ON project_discuss.idproject_task = project_task.idproject_task
                  ".$where." order by project_task.idproject,project_task.idtask 
                  ";
        $qry = "SELECT project_discuss.discuss,project_discuss.date_added,document,
                  project_discuss.iduser,project_discuss.hours_work,
                  project_task.idproject, project_task.idtask, project_task.idproject_task FROM project_task
                  left JOIN project_discuss ON project_discuss.idproject_task = project_task.idproject_task
                  ".$where." order by project_task.idproject,project_task.idtask 
                  "; 
        $this->setSqlQuery($qry);
     }

     /**
      * Method to get the check if any discussion is available for a project on a day set on report_date
      * If no date is set then will return for the current date.
      * @param $idproject -- INT
      * @return boolean
    */
     function isAnyDiscussForProject($idproject){
        if($this->report_date == ''){
            $this->report_date = date("Y-m-d");
            $where = " where project_task.idproject = ".$idproject." AND project_discuss.date_added = '".date("Y-m-d")."'";
        }else{
            $where = " where project_task.idproject = ".$idproject. " AND project_discuss.date_added = '".$this->report_date."'";
        }
        if($this->time_spent_on_task){ $where .= " AND project_discuss.hours_work <>'0.00' ";}
        if($this->set_user_search) { $where .= " AND project_discuss.iduser = ".$this->for_user ; }
        $q = new sqlQuery($this->getDbCon());
        $qry = "SELECT project_discuss.discuss,project_discuss.date_added,document,
                  project_discuss.iduser,project_discuss.hours_work,
                  project_task.idproject, project_task.idtask FROM project_task
                  left JOIN project_discuss ON project_discuss.idproject_task = project_task.idproject_task
                  ".$where." order by project_task.idproject,project_task.idtask";
        //echo $qry;        
        $q->query($qry);
        if($q->getNumRows()){
          return true;
        }else{
          return false;
        }
    }

     /**
      * Method to get the total hrs entered by the user on a particular day
      * @return total hours entered.
     */
    function getTotalHoursEntered(){
        if($this->report_date == ''){
            $this->report_date = date("Y-m-d");
            $where = " where project_discuss.date_added = '".date("Y-m-d")."'";
        }else{
            $where = " where project_discuss.date_added = '".$this->report_date."'";
        }
        if($this->time_spent_on_task){ $where .= " AND project_discuss.hours_work <>'0.00' ";}
        if($this->set_user_search) { $where .= " AND project_discuss.iduser = ".$this->for_user ; }
        $q = new sqlQuery($this->getDbCon());
        
        $qry = "SELECT project_discuss.discuss,project_discuss.date_added,document,
                  project_discuss.iduser,sum( project_discuss.hours_work ) AS total_hrs,
                  project_task.idproject, project_task.idtask FROM project_task
                  left JOIN project_discuss ON project_discuss.idproject_task = project_task.idproject_task
                  ".$where." group by project_discuss.iduser
                  ";

        $q->query($qry);
        $q->fetch();
        return $q->getData("total_hrs");
        
    }

     /**
       * To get the report date. Returns report_date	
     */
     function getReportDate() {
        return $this->report_date;
     }

     /**
       * Event method to set the report date as report_date minus 1 ie 
       * the previous date
       * @param $evtcl -- Object
     */
     function eventSetPreviousDate(EventControler $evtcl){
        $date = date("Y-m-d",strtotime("-1 day",strtotime($this->report_date)));
        $this->report_date = $date;
     }
     
     /**
       * Event method to set the report date as report date plus 1 i.e. the 
       * next date
       * @param $evtcl -- Object
      */
     function eventSetNextDate(EventControler $evtcl){
        $date = date("Y-m-d",strtotime("+1 day",strtotime($this->report_date)));
        $this->report_date = $date;
     }

    /**
      * Event method to set the report_date as today
      * @param $evtcl -- Object
    */
    function eventSetDateToday(EventControler $evtcl){
        $this->report_date = date('Y-m-d');
    }
    
     /**
      * Event method to set the report_date as today
      * @param $evtcl -- Object
    */
    function eventSetDateSelected(EventControler $evtcl){
        $date_sel  = $_COOKIE['dts'];
        $this->report_date = date("Y-m-d",strtotime($date_sel));
        
    }
    /**
      * Public variable time_spent_on_task is set to true
      * @param $evtcl -- Object
    */
    function eventSetHoursWorkedTrue(EventControler $evtcl){
        $this->time_spent_on_task = true;
    }

    /**
      * Public variable time_spent_on_task is set to false
      * @param $evtcl -- Object
    */
    function eventSetHoursWorkedFalse(EventControler $evtcl){
        $this->time_spent_on_task = false;
        $this->set_user_search = false;
    }
    
    /**
      * Event methos to set the public var set_user_search = true in the report
      * also set for_user as the report filtered for iduser
      * @param $evtcl -- Object
    */
    function eventSetUserSearchTrue(EventControler $evtcl){
        $this->set_user_search = true;
        $this->for_user = $evtcl->iduser;
    }
    
    /**
      * Event method to set set_user_search as false
      * @param $evtcl -- Object
    */
    function eventSetUserSearchFalse(EventControler $evtcl){
        $this->set_user_search = false;
    }

    /**
      * Event method to set the report_month as the $evtc->work_for_mon
      * @param $evtcl -- Object
    */
    function eventSetMonth(EventControler $evtcl){
        if($evtcl->work_for_year!= ''){ $this->report_year = $evtcl->work_for_year; }
        $this->report_month = $evtcl->work_for_mon;

        $this->setWeekDates($evtcl->work_for_week);
        
       // echo $this->report_year;exit;
     }

    /**
      * Method to set the start date and end date of the week
      * @param $date_range -- String
    */
    function setWeekDates($date_range){
        if($date_range != ''){
            $this->week_range = $date_range;
            $week_dates = explode("/",$date_range);
            $this->week_start_date = $week_dates[0];
            $this->week_end_date = $week_dates[1];
        }else{
            $this->week_range = '';
            $this->week_start_date = '';
            $this->week_end_date = '';
        }
    }

    /**
      * Checks if there is any dicussion available for a project
      * on a set month
      * @param $idproject
      * @return boolean
    */
    function isAnyDiscussOnProjectMonth($idproject){

        if($this->report_month == ''){ $this->report_month = date("m"); }
        if($this->week_range != '' && $this->week_start_date != '' && $this->week_end_date != ''){
              $where = " where project_task.idproject = ".$idproject." AND 
                          project_discuss.date_added between '".$this->week_start_date."'
                          AND '".$this->week_end_date."'
                          ";
        }else{
              $where = " where project_task.idproject = ".$idproject." AND 
                          project_discuss.date_added like '%".$this->formatSearchMonth($this->report_month)."%'";
        }
        $where .= " AND project_discuss.hours_work <>'0.00' ";
        $q = new sqlQuery($this->getDbCon());
        $qry = "SELECT project_discuss.discuss,project_discuss.date_added,document,
                  project_discuss.iduser,project_discuss.hours_work,
                  project_task.idproject, project_task.idtask FROM project_task
                  left JOIN project_discuss ON project_discuss.idproject_task = project_task.idproject_task
                  ".$where." order by project_task.idproject,project_task.idtask";
       // echo $qry;        
        $q->query($qry);
        if($q->getNumRows()){
          return true;
        }else{
          return false;
        }
    }
        
    /**
      * Get the monthly work done for a project
      * @param $idproject
    */
    function getMonthlyWorkDonePerProjectTask($idproject){
          if($this->report_month == ''){ $this->report_month = date("m"); }

          if($this->week_range != '' && $this->week_start_date != '' && $this->week_end_date != ''){
              $where = " Where project_discuss.date_added between
                         '".$this->week_start_date."' AND '".$this->week_end_date."' 
                            
                            AND project_task.idproject = ".$idproject;
          }else{
              $where = " Where project_discuss.date_added 
                          like '%".$this->formatSearchMonth($this->report_month)."%' AND project_task.idproject = ".$idproject;
          }
       
          $where .= " AND project_discuss.hours_work <>'0.00' ";
        
          $qry = "SELECT distinct project_discuss.iduser,
                  project_discuss.date_added,document,
                  project_discuss.hours_work,
                  project_task.idproject,
                  project_task.idtask 
                  FROM project_task
                  left JOIN project_discuss ON project_discuss.idproject_task = project_task.idproject_task
                  ".$where." group by project_discuss.iduser order by project_task.idproject,project_task.idtask 
                  "; 
       // echo $qry;
        $this->setSqlQuery($qry);
     }


     /**
       * Function to get the distinct task id for a project which has the discussion for the month
       * @param $idproject -- Int
       * @param $mon -- String (month value)
       
     */
      function getDistinctTaskForProjectWithDiscussion($idproject,$mon){
          //if($this->report_month == ''){ $this->report_month = date("m"); }
	  if($_SESSION['adm_project_report_discuss']->week_range != '' && $_SESSION['adm_project_report_discuss']->week_start_date != '' && $_SESSION['adm_project_report_discuss']->week_end_date != ''){
	      $where = " Where project_discuss.date_added between '".$_SESSION['adm_project_report_discuss']->week_start_date."' AND '".$_SESSION['adm_project_report_discuss']->week_end_date."' AND project_task.idproject = ".$idproject;
	  }else{
	      $where = " Where project_discuss.date_added like '%".$this->formatSearchMonth($mon)."%' AND project_task.idproject = ".$idproject;
	  }
       
          $where .= " AND project_discuss.hours_work <>'0.00' ";
        
          $qry = "SELECT distinct 
                  project_task.idproject, project_task.idtask FROM project_task
                  left JOIN project_discuss ON project_discuss.idproject_task = project_task.idproject_task
                  ".$where." order by project_task.idproject,project_task.idtask 
                  "; 
        //echo $qry;
        $this->query($qry);
        
      }

    /**
      * Get the monthly work done for a project and task with the task discussion
      * @param $idproject -- Int
      * @param $idtask -- Int
    */
    function getMonthlyBillableHoursWithDiscussion($idproject,$idtask){
          if($this->report_month == ''){ $this->report_month = date("m"); }

	  if($this->week_range != '' && $this->week_start_date != '' && $this->week_end_date != ''){
	      $where = " Where project_discuss.date_added between '".$this->week_start_date."' AND '".$this->week_end_date."' AND project_task.idproject = ".$idproject;
	  }else{
	      $where = " Where project_discuss.date_added like '%".$this->formatSearchMonth($this->report_month)."%' AND project_task.idproject = ".$idproject;
	  }
       
          $where .= " AND project_discuss.hours_work <>'0.00' ";
          $where .= " AND project_task.idtask = ".$idtask;
          $qry = "SELECT project_discuss.discuss,project_discuss.date_added,document,
                  project_discuss.iduser,project_discuss.hours_work,
                  project_task.idproject, project_task.idtask FROM project_task
                  left JOIN project_discuss ON project_discuss.idproject_task = project_task.idproject_task
                  ".$where."  order by project_task.idproject,project_task.idtask 
                  "; 
        //echo $qry;exit;
        $this->setSqlQuery($qry);
     }

    /**
      * Get the monthly hours worked 
      * @param $iduser -- Int
      * @param $idproject -- Int
      * @return total hours
    */
    function getTotalHoursEnteredMonthly($iduser="",$idproject=""){
        if($this->report_month == ''){ $this->report_month = date("m"); }
        if($idproject!=""){ 
            $where = " where project_task.idproject = ".$idproject; 
        }
        if($this->week_range != '' && $this->week_start_date != '' && $this->week_end_date != ''){
           $where .= " AND project_discuss.date_added between '".$this->week_start_date."'
                       AND '".$this->week_end_date."'
                      "; 
        }else{
            $where .= " AND project_discuss.date_added like '%".$this->formatSearchMonth($this->report_month)."%'";
        }
        $where .= " AND project_discuss.hours_work <>'0.00' ";
        if($iduser!=""){ $where .= " AND project_discuss.iduser = ".$iduser ; }
        $q = new sqlQuery($this->getDbCon());
        
        $qry = "SELECT project_discuss.discuss,project_discuss.date_added,document,
                  project_discuss.iduser,sum( project_discuss.hours_work ) AS total_hrs,
                  project_task.idproject, project_task.idtask FROM project_task
                  left JOIN project_discuss ON project_discuss.idproject_task = project_task.idproject_task
                  ".$where." group by project_task.idproject
                  ";
        $q->query($qry);
        $q->fetch();
        return $q->getData("total_hrs");
        
    }


   


    /**
      * Method to generate the Month Dropdown for the reportings.
      * @return html combo
    */
    function getMonthDropDown(){
        if($this->report_month == ''){ $this->report_month = date("m"); }
        $html = '<select name ="work_for_mon" id = "work_for_mon" class="" onChange=\'$("#setFilterProjHrReport").submit();\' style="align:center;"">';
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


    /**
      * Method getting the number of weeks for a given month
      * @return num of week
    */
    function getNumberOfWeeks(){
          if($this->report_month == ''){ $this->report_month = date("m"); }
          if($this->report_year == ''){ $this->report_year = date("Y"); }
          $year = $this->report_year;
          $month = $this->report_month;
        
          $num_of_days = date("t", mktime(0,0,0,$month,1,$year));
          $firstdayname = date("D", mktime(0, 0, 0, $month, 1, $year));
          $firstday = date("w", mktime(0, 0, 0, $month, 1, $year));
          $lastday = date("t", mktime(0, 0, 0, $month, 1, $year));
          $min_week = 0;
          $max_week = 0;
          for ($day_of_week = 0; $day_of_week <= 6; $day_of_week++)
          {
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
	    //echo $d;
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

    /**
      * Methods generating the Week drop downs
      * @return html combo
    */
    function getWeekDropDowns(){
        $num_of_weeks = $this->getNumberOfWeeks(); //echo 'No of week :'.$num_of_weeks;
        $start_date = $this->report_year.'-'.$this->report_month.'-01';

        //extra 2 lines here by vivek
         $year = $this->report_year;
         $month = $this->report_month;
        
        $html = '';
        $html .= '<select name="work_for_week" id="work_for_week" class="" onChange=\'$("#setFilterProjHrReport").submit();\' style="align:center;"">';
        $html .= '<option value = "">'._('Select Week').'</option>';
        for($i=1;$i<=$num_of_weeks;$i++){
            if($i != 1){ 
              //Old code 2 lines 
              //$start_date = strtotime(date("Y-m-d",strtotime($end_date)) ."+1 day");
              //$start_date = date("Y-m-d",$start_date);
              
              
              $lastday = date("t", mktime(0, 0, 0, $month, 1, $year));
              $lastdate = "$year"."-"."$month"."-"."$lastday";
              
              $lastdate = strtotime(date("Y-m-d",strtotime($lastdate)) ."+1 day");
              
              $start_date = strtotime(date("Y-m-d",strtotime($end_date)) ."+1 day");
              
              if($start_date != $lastdate)
              {
              $start_date = date("Y-m-d",$start_date);
              }
              else
              {
              $html .= '</select>';
              return $html;
              }
            }
            $start_date_splited = explode("-",$start_date);
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
                        '.$this->getWeekFilter($date_range).'>Week '.$i.': '
                         .date("d S",strtotime($start_date)).' to '.date("d S",strtotime($end_date)).'</option>';
        }
        $html .= '</select>';
        return $html;

    }

    

    /**
      * Method to generate the year drop down for the report
      * @return html combo
    */
    function getYearDropDown(){ 
        if($this->report_year == ''){ $this->report_year = date("Y"); }
        $html = '<select name ="work_for_year" id = "work_for_year" class="" onChange=\'$("#setFilterProjHrReport").submit();\' style="align:center;"">';
        $html .='<option value = "'.date("Y").'" '.$this->getYearFilter(date("Y")).'>'.date("Y").'</option>';
        $html .='<option value = "'.date("Y",strtotime("-1 year",strtotime(date("Y")))).'" '.$this->getYearFilter(date("Y",strtotime("-1 year",strtotime(date("Y"))))).'>'.date("Y",strtotime("-1 year",strtotime(date("Y")))).'</option>';
        //$html .='<option value = "'.date("Y",strtotime("-2 year",strtotime(date("Y")))).'" '.$this->getYearFilter(date("Y",strtotime("-2 year",strtotime(date("Y"))))).'>'.date("Y",strtotime("-2 year",strtotime(date("Y")))).'</option>';
        //$html .='<option value = "'.date("Y",strtotime("-3 year",strtotime(date("Y")))).'" '.$this->getYearFilter(date("Y",strtotime("-3 year",strtotime(date("Y"))))).'>'.date("Y",strtotime("-3 year",strtotime(date("Y")))).'</option>';
		$html .= '</select>';
        return $html;
    }


    /**
      * Get week filter 
    */
    function getWeekFilter($selected = ""){
        if ($selected == $this->week_range) {
            return " selected";
        } else {
            return '';
        }
    }


    /**
      * Get the month filter
    */
    function getMonthFilter($selected="") {
        if ($selected == $this->report_month) {
            return " selected";
        } else {
            return $this->report_month;
        }
    }

    /**
      * Get the year filter
    */
    function getYearFilter($selected=""){
         if ($selected == $this->report_year) {
            return " selected";
        } else {
            return '';
        }
    }

    /**
      * Format the month for search/filter
      * @param $month -- String
      * @return the value by pre-pending the year
    */
    function formatSearchMonth($month){
        if($this->report_year != '')
            return $this->report_year.'-'.$month; 
        else
            return date("Y").'-'.$month; 
    }

    /**
      * Get the Total Number of project discussion for a user
      * @param $iduser -- Int
      * @return numbers
    */
    function getTotalNumProjectDiscussionsForUser($iduser) {
	    $q = new sqlQuery($this->getDbCon());
	    $sql = "SELECT COUNT(idproject_discuss) AS total_project_discuss
			    FROM `{$this->table}` 
			    WHERE `iduser` = {$iduser}
			";
	    $q->query($sql);
	    if($q->getNumRows()) {
		    $q->fetch();
		    return $q->getData("total_project_discuss");
	    } else {
		    return "0";
	    }
    }

	/*function getCountProjDiscussion() {
        $do_discuss =$_SESSION['do_project_task']->getChildProjectDiscuss("ORDER BY priority DESC, date_added DESC,idproject_discuss DESC");
		$this->prj_discussion_count = $do_discuss->getNumRows();
		$_SESSION['ProjectDiscussEditSave']->prj_discussion_count = $this->prj_discussion_count;
	}*/

    /**
      * Get the project discussion on scroll down
    */
    function autoLoadPrjDiscussionOnScrollDown() {

	    if($_SESSION['ProjectDiscussCount']->prj_discussion_count >= $_SESSION['ProjectDiscussCount']->sql_qry_start) {
		    $_SESSION['ProjectDiscussCount']->sql_qry_start = $_SESSION['ProjectDiscussCount']->sql_qry_start + $this->sql_view_limit;
	    }


	    echo '<script type="text/javascript">
	    $(document).ready(function() {
		    $("div[id^=notetext]").hover(function(){$("div[id^=trashcan]",this).show("fast");},function(){$("div[id^=trashcan]",this).hide("fast");});
		    //fnSetProgress(arguments[0]);
	    });
	    </script>';

	    $do_discuss = $_SESSION['do_project_task']->getChildProjectDiscuss("ORDER BY priority DESC, date_added DESC,idproject_discuss DESC LIMIT {$_SESSION['ProjectDiscussCount']->sql_qry_start},{$this->sql_view_limit}");

	    $do_discuss->sessionPersistent('ProjectDiscussEditSave', "project.php", OFUZ_TTL);

	    if ($do_discuss->getNumRows()) {
		    $item_count = 0;
		    while ($do_discuss->next()) {
			    $file = '';
			    $preview_item = '';
			    if($do_discuss->document != ''){
				    $doc_name = $do_discuss->document;
				    $doc_name = str_replace("  ","%20%20",$do_discuss->document);
				    $doc_name = str_replace(" ","%20",$doc_name);
				    $file_url = "/files/".$doc_name;
				    //$file_url = '/files/'.$do_discuss->document;
				    $file = '<br /><a href="'.$file_url.'" target="_blank">'.$do_discuss->document.'</a>';
			    }
			    
			    $item_text = $do_discuss->formatDiscussionItemDisplay($do_discuss->discuss);
			    if (substr_count($item_text, '<br />') > 4) {
				    $preview_item = preg_replace('/(.*?<br \/>.*?<br \/>.*?<br \/>.*?<br \/>)(.*)/','$1',str_replace("\n",'',$item_text)).' ';
			    } else if (strlen($item_text) > 500) {
				    $preview_item = substr($item_text, 0, 500);
			    }
			    if($do_discuss->iduser){
				    $added_by = $_SESSION['do_User']->getFullName($do_discuss->iduser);
			    }else{
				    $added_by = $do_discuss->drop_box_sender;
			    }
			    $e_gen_dropboxid = new Event('do_project_task->eventGenerateDropBoxIdTask');
			    $e_PrioritySort = new Event('ProjectDiscuss->eventPrioritySortNotes');
			    $e_PrioritySort->addParam('goto', 'task.php');
			    $e_PrioritySort->addParam('idnote', $do_discuss->idproject_discuss);
			    $star_img_url = '<img src="/images/'.($do_discuss->priority > 0?'star_priority.gif':'star_normal.gif').'" class="star_icon" width="14" height="14" alt="'._('Star this note to move it on top').'" />';
			    if (is_object($_SESSION['ProjectDiscussEditSave'])) {
				    $e_discuss_del = new Event('ProjectDiscussEditSave->eventTempDelNoteById');
			    }
			    $e_discuss_del->addParam('goto', 'task.php');
			    $e_discuss_del->addParam('id', $do_discuss->idproject_discuss);
			    $e_discuss_del->addParam('context', 'ProjectDiscuss');
			    $del_img_url = _('delete').' <img src="/images/delete.gif" width="14px" height="14px" alt="" />';
			    echo '<div id="notetext',$do_discuss->idproject_discuss,'" class="vpad10">';
			    echo '<div style="height:24px;position:relative;"><div class="percent95"><img src="/images/discussion.png" class="note_icon" width="16" height="16" alt='._('Task Discussion').'" />',$e_PrioritySort->getLink($star_img_url, ' title="'._('Star this note to move it on top').'"');
			    echo '<b>'.date('l, F j', strtotime($do_discuss->date_added)).'</b>&nbsp;(Added By :&nbsp;'.$added_by.')</div> 
			    <div id="trashcan', $item_count++, '" class="deletenote" style="right:0;">'.'<a href="#"  onclick="fnEditNote(\'notetext'.$do_discuss->idproject_discuss.'\','.$do_discuss->idproject_discuss.');return false;">'._('edit').'</a>&nbsp;|&nbsp;'.$e_discuss_del->getLink($del_img_url, ' title="'._('Delete this note').'"').'</div></div>';
			    if ($do_discuss->is_truncated) {
				    echo '<div id="notepreview',$do_discuss->idproject_discuss,'">',$item_text,'<a href="#" onclick="showFullNote(',$do_discuss->idproject_discuss,'); return false;">'._('more...').'</a><br /></div>';
			    } else {
				    echo $item_text;
			    }
			    echo $do_discuss->formatDocumentLink().'</div>
			    <div id="e'.$do_discuss->idproject_discuss.'" style="display: none;" class="note_edit_box"></div>
			    <div id="'.$do_discuss->idproject_discuss.'" class="message_box"></div>';
			    
		    }
	    }
    }

    /**
      * Get the total hours for a task within the range 
      * @param $idproject -- Int
    */
    function getTotalHoursPerTaskMonthly($idproject) { 
	    if($_SESSION['adm_project_report_discuss']->week_range != '' && $_SESSION['adm_project_report_discuss']->week_start_date != '' && $_SESSION['adm_project_report_discuss']->week_end_date != ''){
		    $sql = "SELECT t.task_description,SUM(pd.hours_work) AS tot_task_hrs
			FROM task AS t
			    INNER JOIN project_task AS pt ON t.idtask = pt.idtask
			    INNER JOIN project_discuss AS pd ON pd.idproject_task = pt.idproject_task
			    WHERE pt.idproject = {$idproject} AND
			    pd.date_added between '".$_SESSION['adm_project_report_discuss']->week_start_date."' AND '".$_SESSION['adm_project_report_discuss']->week_end_date."'
			    GROUP BY t.idtask
			";
	    }else{
		$sql = "SELECT t.task_description,SUM(pd.hours_work) AS tot_task_hrs
			FROM task AS t
			    INNER JOIN project_task AS pt ON t.idtask = pt.idtask
			    INNER JOIN project_discuss AS pd ON pd.idproject_task = pt.idproject_task
			    WHERE pt.idproject = {$idproject} AND
			    pd.date_added like '%".$this->formatSearchMonth($this->report_month)."%'
			    GROUP BY t.idtask
			";
	    }
	    //echo $sql;
	    $this->query($sql);
    }

    /**
      * Check if the file can be accessed for a project discuss
      * @param $idproject_task -- Int
      * @param $file -- String
      * @return boolean
    */
    function isFilePermitted($idproject_task,$file){
	$q = new sqlQuery($this->getDbCon()); 
	$q->query("select idproject_discuss from ".$this->table." where idproject_task =".$idproject_task." AND document ='".$file."'");
	//echo "select idproject_discuss from ".$this->table." where idproject_task =".$idproject_task." AND document ='".$file."'";
	if($q->getNumRows()){
	    return true;
	}else{ return false; }
    }


    /**
      * Copy the Contact notes to a project discuss
      * @param $evtcl -- Object
    */
    function eventCopyContactNoteToPrjDiscuss(EventControler $evtcl) {

    $fields = $evtcl->fields;
	    if($fields["note"] != "" && $evtcl->cpy_prj_tasks != "") {
		    $this->idproject_task = $evtcl->cpy_prj_tasks;
		    $this->discuss = $fields["note"];
		    $this->date_added = date("Y-m-d");
		    $this->document = $fields["document"];
		    $this->hours_work = $fields["hours_work"];
		    $this->iduser = $_SESSION['do_User']->iduser;
        
		    $this->query("INSERT INTO project_discuss (idproject_task,discuss,date_added,document,hours_work,iduser) VALUES (".$this->idproject_task.",'".$this->discuss."','".$this->date_added."','".$this->document."',".$this->hours_work.",".$this->iduser.")");
	    }
    }	
    
    
    /**
   * Get the Task first note which entered by any user 
   * @param idprojecttask
   * @return dicuss note
   **/
   
      function getFirstNote($idprojecttask) {
          $sql_first_note = "SELECT discuss FROM `project_discuss` WHERE idproject_task='$idprojecttask' limit 1";
          $this->query($sql_first_note);
           if($this->getNumRows() >0){
          return $this->getData('discuss');
           }else{
               return '';
           }
      }
      
      
       /**
      * Get the monthly hours worked 
      * @param $iduser -- Int
      * @param $idproject -- Int
      * @return total hours
    */
    function getTotalHoursEnteredByIndividual($iduser,$type){
        
        switch($type)
        {
           case 'Today':
                $where .= " AND project_discuss.date_added= CURDATE()";  
                break;
                
           case 'PreviousDay':
                $where .= " AND project_discuss.date_added = DATE_SUB(CURDATE(),INTERVAL 1 DAY)";  
                break;
                
           case 'LastWeek':
                $where .= " where project_discuss.date_added <= CURDATE() AND project_discuss.date_added >= DATE_SUB(CURDATE(),INTERVAL 7 day)";  
                break;    
        }
        
        $where .= " AND project_discuss.hours_work <>'0.00' ";
        if($iduser!=""){ $where .= " AND project_discuss.iduser = ".$iduser ; }
        
        $q = new sqlQuery($this->getDbCon());
        $qry = "SELECT sum( project_discuss.hours_work ) AS total_hrs FROM project_task
                  left JOIN project_discuss ON project_discuss.idproject_task = project_task.idproject_task
                  ".$where." ";
        //echo $qry;exit;          
        $q->query($qry);
        $q->fetch();
        return $q->getData("total_hrs");
        
    }  
      

}
?>
