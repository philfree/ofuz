<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

   /**
    * Manage the Tasks of Projects
    * It extend the Task object as it complements it 
    * with additional variables and properties. 
    *
    * @author SQLFusion's Dream Team <info@sqlfusion.com>
    * @package OfuzCore
    * @license GNU Affero General Public License
    * @version 0.6
    * @date 2010-09-06
    * @since 0.3
    * @see OfuzCore.Project, OfuzCore.Task
    */

class ProjectTask extends Task {

    public $table = 'project_task';
    protected $primary_key = 'idproject_task';

    public $filter_user="";
    public $filter_category="";
    public $set_search = false;
    public $project_id_searched = "";
    public $access = "Privale";


    /**
      * Overwrite the Data Object method add()
    */

    function add() {
        $this->query("INSERT INTO task (task_description,due_date,task_category,iduser,due_date_dateformat) VALUES ('".$this->task_description."','".$this->due_date."','".$this->task_category."',".$_SESSION['do_User']->iduser.",'".$this->DueDateConvert($this->due_date)."')");
        $this->setPrimaryKeyValue($this->getInsertId('task', 'idtask'));
        $this->idtask = $this->getPrimaryKeyValue();
        $this->query("INSERT INTO project_task (idtask, idproject) VALUES (".$this->idtask.", ".$this->idproject.")");
        $this->setPrimaryKeyValue($this->getInsertId($this->getTable(), $this->getPrimaryKey()));
        $this->idproject_task = $this->getPrimaryKeyValue();
    }
    

    /**
      * Generating the Project Task add form
    */

    function getProjectTaskAddForm() {
        $this->setRegistry('project_task_fields');
        $f_projectForm = $this->prepareSavedForm('ofuz_add_project_task');
        $f_projectForm->setFormEvent($this->getObjectName()."->eventAdd", 1005);
        $f_projectForm->addEventAction($this->getObjectName()."->eventGenerateDropBoxIdTask", 1010);
        $f_projectForm->addEventAction($this->getObjectName()."->eventSendNewTaskEmail", 1020);
        $f_projectForm->addEventAction($this->getObjectName()."->eventClearSearch", 1030);// For clearing the search
        $f_projectForm->addEventAction($this->getObjectName()."->eventSetDateInFormat", 10);
        $f_projectForm->addEventAction("WorkFeedProjectTask->eventAddFeed", 1040);
        $f_projectForm->addParam('task_event_type','new_task_add');
        $f_projectForm->setAddRecord();
        $f_projectForm->setUrlNext($_SERVER['PHP_SELF']);
        $f_projectForm->setForm();
        //$f_projectForm->execute();
        return $f_projectForm->executeToString();
    }

    /**
      * Function to send new task email to the particepants
    */

    function eventSendNewTaskEmail(EventControler $event_controler) {
       if ($event_controler->insertid > 0) {
            
            $this->getId($event_controler->insertid);
            $co_workers = $_SESSION["do_project"]->getProjectCoWorkers();
                if ($co_workers !== false) {
                $emailer = new Radria_Emailer();
                $emailer->setEmailTemplate(new EmailTemplate("ofuz_new_project_task"));
                $project_task_url = $GLOBALS['cfg_ofuz_site_http_base'].'Task/'.$this->idproject_task;
                $project_url = $GLOBALS['cfg_ofuz_site_http_base'].'Project/'.$this->idproject;
                $email_data = Array('project-name' => $_SESSION['do_project']->name,
                                    'task-owner' => $_SESSION['do_User']->getFullName(),
                                    'task-name' => $this->task_description,
                                    'project-task-link' => $project_task_url, 
                                    'project-link' => $project_url
                                    );
                foreach ($co_workers as $co_worker) {
                    $email_data['firstname'] = $co_worker['firstname'];
                    $emailer->mergeArray($email_data);
                    //$this->setLog("\n Sending email to:".$co_worker['email']);
                    $emailer->addTo($co_worker['email']);
                    $emailer->send();
                    $emailer->cleanup();
                }
            }
        }
    }

    
    /**
      * Function to convert the due date to the mysql date format 
      * @param $due_date  -- STRING
      * @return mysql formatted date
    */
    function DueDateConvert($due_date) {
        switch($due_date) {
            case 'Today':
                $formatted_date = date('Y-m-d');
                break;
            case 'Tomorrow':
                $formatted_date = date('Y-m-d',strtotime('+1 day'));
                break;
            case 'This week':
                $formatted_date = date('Y-m-d',strtotime('next Friday'));
                break;
            case 'Next week':
                $formatted_date = date('Y-m-d',strtotime('next Friday',strtotime('+1 week')));
                break;
            case 'This month':
                $formatted_date = date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y').' 00:00:00'))));
                break;
            default:
                $formatted_date = '0000-00-00';
        }
        return $formatted_date;
    }


    /**
      * Function to get all the Task for the project
      * @param $idproject -- INTO  
    */

    function getAllProjectTasks($idproject=0) {
        $this->project_id_searched = $idproject;
        $this->setSqlQuery("SELECT * FROM project_task p INNER JOIN task t ON p.idtask = t.idtask  
        WHERE p.idproject = ".$idproject." ORDER BY p.priority ASC,t.status DESC,t.due_date_dateformat");
        //WHERE p.idproject = ".$idproject." ORDER BY p.priority ASC,t.status DESC,t.due_date_dateformat";
        //AND end_date_dateformat <> '0000-00-00' AND status = 'open'
    }
    
    /**
      * Function to get all the project task for a specific user
      * @param $idproject -- INT
      * @param $iduser -- INT
    */
    
    function getAllProjectTasksForUser($idproject=0,$iduser=0) {
        $this->query("SELECT * FROM task t inner join project_task as pt on t.idtask = pt.idtask
        WHERE pt.idproject = ".$idproject." AND t.iduser = ".$iduser." AND t.status = 'open'");
        //AND end_date_dateformat <> '0000-00-00' AND status = 'open'
    }
    

    /**
      * Function to clear the search and sets all the values to default
    */
    function clearSearch(){
        $this->setSqlQuery("");
        $this->filter_user="";
        $this->filter_category="";
        $this->set_search=false;
    }

    /**
      * Event method to clear the search.
      * @param $evtcl -- OBJECT
    */
    function eventClearSearch(EventControler $evtcl){
       if(is_object($_SESSION['do_list_project_task'])){
            $_SESSION['do_list_project_task']->clearSearch();
        }
    }


    /**
      * Event method to filter project task
      * @param $evtcl -- OBJECT
    */
    function eventFilterProjectTask(EventControler $evtcl){
        if($evtcl->proj_task_cat == "" && $evtcl->proj_workers == 0){ 
            $this->set_search = false;
        }else{
            $this->set_search = true;
        }
        $this->setUserFilter($evtcl->proj_workers);
        $this->setCategoryFilter($evtcl->proj_task_cat);
        if($evtcl->proj_task_cat != ""){ 
                $cat_where = " AND t.task_category = '".$evtcl->proj_task_cat."'" ;
            }else{  
                $cat_where = "";
        }

        if($evtcl->proj_workers != 0 ){
            $this->setSqlQuery("SELECT * FROM task t inner join project_task as pt on t.idtask = pt.idtask
            WHERE pt.idproject = ".$evtcl->idproject." AND t.iduser = ".$evtcl->proj_workers.$cat_where." ORDER BY pt.priority ASC,t.status DESC,t.due_date_dateformat");
        }else{
            $this->setSqlQuery("SELECT * FROM project_task p INNER JOIN task t ON p.idtask = t.idtask  
             WHERE p.idproject = ".$evtcl->idproject.$cat_where." ORDER BY p.priority ASC,t.status DESC,t.due_date_dateformat");
        }
    }
  
    
    /**
      * Set the Filter for user with the iduser
      * @param $id -- INT 
    */
    function setUserFilter($id) {
        $this->filter_user = $id;
    }  
    
    /**
      * Set the Filter for Category
      * @param $cat
    */
    function setCategoryFilter($cat){
        $this->filter_category = $cat;        
    }

    /**
      * Function get the user filer 
    */
    function getUserFilter($selected="") {
        if ($selected == $this->filter_user) {
            return " selected";
        } else {
            return $this->filter_user;
        }
    }


    /**
      * Function get category filter
    */

    function getCategoryFilter($selected="") {
        if ($selected == $this->filter_category) {
            return " selected";
        } else {
            return $this->filter_category;
        }
    }
    
    /**
      * Check if the user is related to the user to access it
      * @param integer $idtask 
      * @param integer $iduser 
      * @return boolean
    */
    function isProjectTaskReletedToUser($idtask=0,$iduser=0){
         $return_val = false;
         if (empty($idtask)) { $idtask = $this->idproject_task; }
         if (empty($iduser)) { $iduser = $_SESSION['do_User']->iduser; }
         $idproject = $this->getProjectForTask($idtask);
         $do_project = new Project();
         if($do_project->isProjectOwner($idproject,$iduser) === true){
            $return_val = true;
        }else{
            if($do_project->isProjectCoWorker($idproject) === true){
               $return_val = true;
            }else{
              $return_val = false;
            }
        }
        return $return_val;
    }
    

    /**
      * check if the task is public
      * @param integer $idtask 
      * @return boolean
    */
    function isPublicAccess($idtask=0){
        if (empty($idtask)) { $idtask = $this->idproject_task; }
        
        $idproject = $this->getProjectForTask($idtask);
        $do_project = new Project();
        if($do_project->isPublicProject($idproject) === true){
            return true;
        }else{  
            return false;
        }
    }
    
    /**
      * Get the project for a task
      * @param integer $idtask 
      * @return idproject
    */

    function getProjectForTask($idtask=0){
        if (empty($idtask)) { $idtask = $this->idproject_task; }
        $q = new sqlQuery($this->getDbCon());
        $q->query("select idproject from ".$this->table." where idproject_task = ".$idtask);
        $q->fetch();
        return $q->getData("idproject");
    }
    
    /**
      * Function to view the project task using ListObject
      * @param string $access
      * @see ListObject
    */
    function viewProjectTasks($access='Private') {
     $this->access = $access;
     $OfuzList = new OfuzList($this);
     $OfuzList->setMultiSelect(true);
     $OfuzList->displayList();
 
    /*$do_contact = new Contact();
            $html = '<ul id="project_tasks">';
            while ($this->next()) {
      $contact_full_name = "";
      if($this->idcontact) {
        $contact_full_name = ' ('.$do_contact->getContactName($this->idcontact).')';
      }
                $progress_pixels = $this->progress;
                $strike_class = '';
                if($this->status == 'closed'){
                    $strike_class = ' class="ptask_closed"';  
                }
                if (!is_numeric($progress_pixels) || $progress_pixels < 0 || $progress_pixels > 100) $progress_pixels = '0';

                if($access == 'Public'){
                      $html .= '<li id="pt_'.$this->idtask.'">'.
                          '<div class="ptask_name"><span class="task_category">'.$this->task_category.'</span>&nbsp;<span'.$strike_class.'><a href="/PublicTask/'.$this->idproject_task.'">'.$this->task_description.'</a>'.$contact_full_name.'</span></div>'.
                          '<div class="ptask_progbar1">';
                }else{
                      $html .= '<li id="pt_'.$this->idtask.'">'.
                          '<div class="ptask_name"><span class="task_category">'.$this->task_category.'</span>&nbsp;<span'.$strike_class.'><a href="/Task/'.$this->idproject_task.'">'.$this->task_description.'</a>'.$contact_full_name.'</span></div>'.
                          '<div class="ptask_progbar1">';
                }
                if ($this->status == 'closed') {
                    $html .= _('closed').'<div class="ptask_progbar3" style="width: 100px;"></div></div>'."\n";
                } else {
                    $html .= _('progress').'<div class="ptask_progbar2" style="width: '.$progress_pixels.'px;"></div></div>'."\n";
                }
                if($access != 'Public'){
                    $html .= '<div class="ptask_handle"></div>';
                }
                $html .= '</li>'."\n";
            }
            $html .= '</ul>';
           return $html;*/
 
    }

    /**
      * Get the project task detail by idtask
      * @param integer $idtask
      * @return $q query object
    */
    function getProjectTaskDetailsByTaskId($idtask){
        $q = new sqlQuery($this->getDbCon());
        $q->query("SELECT * FROM project_task p 
                      INNER JOIN task t ON p.idtask = t.idtask 
                      INNER JOIN project pr on p.idproject = pr.idproject
                      WHERE p.idtask = ".$idtask." ORDER BY p.priority");
        $q->fetch();
        return $q ;
    }

    /**
      * Get the project task details
      * @param Integer $idproject_task
    */
    function getProjectTaskDetails($idproject_task=0) {
        $this->query("SELECT * FROM project_task p INNER JOIN task t ON p.idtask = t.idtask WHERE p.idproject_task = ".$idproject_task." ORDER BY p.priority");
        return $this->getValues();
    }

    /**
      * Overwrite Data Object getId() method
    */
    function getId($id) { $this->getProjectTaskDetails($id); }

   /**
    * Function to render HTML output to change the task owner
    * @param Integer $idproject 
    * @return HTML output
   */

    function renderChangeTaskOwnerList($idproject=""){

      $do_proj = new Project();
      if($idproject == ""){
      $do_proj->idproject = $this->idproject;
      }else{$do_proj->idproject = $idproject ;}
      $co_workers = $do_proj->getProjectCoWorkers();
      $output = '';
      if(!$co_workers){
          $output .= '<br />'._('No Co-Worker found for this project').'<br />';
      }else{  
          if(is_array($co_workers)){
              $output .='<select name="fields[co_worker]">';
              $output .='<option value="">'._('Select One').'</option>';
              foreach($co_workers as $co_workers){
                  $selected = '';
                  $output .= '<option value="'.$co_workers["idcoworker"].'" '.$selected.'>'.$co_workers["firstname"].' '.$co_workers["lastname"].'</option>';
              }
              $output .= '</select>';
              $output .='<input value="'._('Change Owner').'" type="submit">';
          }else{
              $output .= '<br />'._('No Co-Worker found for this project').'<br />';
          }
      }
      return $output;
    }


    /**
      * Event method to change the task owner
      * @param OBJECT $evtcl 
    */
    function eventChangeTaskOwner(EventControler $evtcl){
        $fields = $evtcl->fields;
        $idcoworker =  $fields["co_worker"];
        if($idcoworker != ''){
            $idtask = $evtcl->idtask;
            $this->changeTaskOwner($idcoworker,$idtask);
            $message = "Task owner is updated";
        }else{
            $message = "Please select one Co-Worker";
        }
        $goto = $evtcl->goto;
        $dispError = new Display($goto) ;
        $dispError->addParam("message", $message) ;
        $evtcl->setDisplayNext($dispError) ;
    }

    /**
      * Function to change the Task Owner
      * @param Integer $idcoworker 
      * @param Integer $idtask
    */
    function changeTaskOwner($idcoworker,$idtask){
        $do_task = new Task();
        $do_task->getId($idtask);
        $do_task->iduser = $idcoworker;
        $do_task->status = 'open';
        $do_task->update();
        $qobj = $this->getProjectTaskDetailsByTaskId($idtask);
        $this->sendEmailOnTaskOwnershipChange($idcoworker,$qobj);//sending ownership change alert
    }

    /**
      * If a task owner is changed to iduser then send and email notification to the user
      * @param integer $iduser 
      * @param Object $qobj, query object holding the data added since V- 0.6.1
    */
    
    function sendEmailOnTaskOwnershipChange($iduser,$qobj){
        $email_id = $_SESSION['do_User']->getEmailId($iduser);
        $full_name = $_SESSION['do_User']->getFullName($iduser);
        $email_template = new EmailTemplate("ofuz_task_ownership_change");
        
        $project_task_url = $GLOBALS['cfg_ofuz_site_http_base'].'Task/'.$qobj->getData("idproject_task");
        $project_link =  $GLOBALS['cfg_ofuz_site_http_base'].'Project/'.$qobj->getData("idproject");

        $email_data = Array('project-name' => $qobj->getData("name"),
                      'project-link' => $project_link,
                                    'task-name' => $qobj->getData("task_description"),
                                    'project-task-link' => $project_task_url
                                    );

        $emailer = new Radria_Emailer();
        $emailer->setEmailTemplate($email_template);
        $email_data['firstname'] = $full_name;
        $emailer->mergeArray($email_data);
        $this->setLog("\n Sending email to:".$email_id);
        $emailer->addTo($email_id);
        $emailer->send();                        
      
    }

    /**
      * Method to get the Task owner name
      * @return owner name
    */

    function getTaskOwnerName(){
        $q = new sqlQuery($this->getDbCon());
        //$q->query("select iduser from task where idtask = ".$this->idproject_task);
        $q->query("select user.firstname as firstname,
                   user.lastname as lastname from user where user.iduser = (select iduser from task
                   where task.idtask = ".$this->idtask."
                   )"
                  );
        
        $q->fetch();
        return $q->getData("firstname").' '.$q->getData("lastname");
    }

    /**
      * Generate the drop box id for a task 
      * @param $event_controler -- OBJECT
    */

    function eventGenerateDropBoxIdTask(EventControler $event_controler){
      $drop_box_code = $this->idproject_task;
      $q = new sqlQuery($this->getDbCon());
      $q->query("update ".$this->table." set drop_box_code = ".$drop_box_code." where ".$this->primary_key." = ".$this->idproject_task);
      $_SESSION['do_project_task']->drop_box_code = $drop_box_code;
    } 

    /**
      * function to generate the random dropbox code for a task
      * Derpricated
    */
    function generateRandomDropBoxIdTask(){
      $drop_box_code = rand(0, pow(10, 5));
      $q = new sqlQuery($this->getDbCon());
      $q->query("select * from ".$this->table." where drop_box_code = ".$drop_box_code);
      if ($q->getNumRows()) { 
        $i = 1;
        $drop_box_code = $this->generateRandomDropBoxIdTask();
      }elseif(strlen($drop_box_code < 5)){
          $drop_box_code = $this->generateRandomDropBoxIdTask();
      }
      return $drop_box_code;
    }

    /**
      * Function to get the dropbox id
    */
    function getDropBoxEmail() {
        if (strlen($this->drop_box_code) > 0) {
            return  'task-'.$this->drop_box_code.'@ofuz.net';
        } 
    }

    /**
      * Function to get the details of the project task from the drop_box_code
      * @param integer $drop_box_code
    */
    function getTaskDetailByDropBoxCode($drop_box_code){
        $this->query("select * from ".$this->table." where drop_box_code = ".$drop_box_code );
        $this->getValues();
    }

    /**
     * Receives new sort order and writes it to the DB
     */
    function eventAjaxPrioritySort(EventControler $event_controler) {
        $q = new sqlQuery($this->getDbCon());
          echo "aneesj";
              echo $event_controler->pt;
      print_r($event_controler->pt);
          exit;

        foreach ($event_controler->pt as $priority => $idtask) {
            $q->query("UPDATE project_task SET priority = $priority WHERE idtask = $idtask");
        }
        $event_controler->addOutputValue(true);
    }

    /**
     * Receives progress bar % value and writes to the DB
     */
    function eventAjaxUpdateProgress(EventControler $event_controler) {
        $q = new sqlQuery($this->getDbCon());
        $q->query("UPDATE project_task SET progress = '{$event_controler->progress}' WHERE idproject_task = {$event_controler->idproject_task}");
        
        $WorkFeedProjectTask = new WorkFeedProjectTask();
        $evctl = new EventControler;
        $evctl->addParam('task_event_type','task_progress');
        $evctl->addParam('progress',$event_controler->progress);
        $WorkFeedProjectTask->eventAddFeed($evctl);

        $event_controler->addOutputValue(true);
    }

    /**
     * Receives task changes and writes them to the DB
     */
    function eventUpdateProjectTask(EventControler $event_controler) {
        $do_task = new Task();
        $q = new sqlQuery($this->getDbCon());
        $q->query("UPDATE task SET task_description = '{$event_controler->task_description}',
                  task_category = '{$event_controler->task_category}',
                  due_date_dateformat = '{$event_controler->due_date}',
                  status = '{$event_controler->status}'
                  WHERE idtask = {$event_controler->idtask}");
        $q->query("UPDATE project_task SET 
                  idproject = {$event_controler->idproject} ,
                  hrs_work_expected = '{$event_controler->hrs_work_expected}'
                  WHERE idproject_task = {$event_controler->idproject_task}");
    }


    /**
      * Generate the drop down with the tasks for a project
    */
    function eventGetProjectTasks(EventControler $evtcl){
        $dropdown = "";
        if($evtcl->idproject){
            $this->query("SELECT * FROM task t inner join project_task as pt on t.idtask = pt.idtask
            WHERE pt.idproject = ".$evtcl->idproject." AND t.iduser = ".$_SESSION['do_User']->iduser." ORDER BY pt.priority ASC,t.status DESC,t.due_date_dateformat");

            if($this->getNumRows()) {
              $dropdown = '<select name="cpy_prj_tasks" id="cpy_prj_tasks">';
              $dropdown .= '<option value="">'._('Select Task').'</option>';
              while($this->fetch()) {
              $idproject_task = $this->getData("idproject_task");
              $task_description = $this->getData("task_description");
              $dropdown .= '<option value="'.$idproject_task.'">'.$task_description.'</option>';
              }
              $dropdown .= '</select>';
            } else {
              $dropdown = '<select name="cpy_prj_tasks" id="cpy_prj_tasks">';
              $dropdown .= '<option value="">'._('You do not have Tasks for this Project.').'</option>';
              $dropdown .= '</select>';
            }
         }
         echo $dropdown;
    }

    /**
      * Function get Task name 
      * @param $idproject_task -- INT
      * @return task name
    */
    function getTaskName($idproject_task=0) {
        $task_description = "";
        $this->query("SELECT t.task_description FROM project_task p INNER JOIN task t ON p.idtask = t.idtask WHERE p.idproject_task = ".$idproject_task);
        if($this->getNumRows()) {
            $this->fetch();
            $task_description = $this->getData("task_description");
        }

        return $task_description;
    }

    /**
      * Event Method to close multiple task
    */
    function eventCloseTaskMultiple(EventControler $evctl){ 
      $task_ids = $evctl->ck;
      if(is_array($task_ids) && count($task_ids)>0){
          $do_task = new Task();
          foreach($task_ids as $idtask){
            $do_task->updateStatus($idtask,"closed");
          }
      }
    }

    /**
      * Event method change multiple Task Owner
    */
    function eventChangeOwnerMultiple(EventControler $evctl){
      $task_ids = $evctl->ck;       
      if (preg_match("/-/",$task_ids[0])) {
          $taskid=$evctl->ck;
          $task_ids= array();
          foreach($taskid as $ids){      
            $explode= explode('-',$ids);
            if(in_array($explode[0],$task_ids)==false){
                array_push($task_ids,$explode[0]);
            }
          }
      }

      if($evctl->fields["co_worker"] != '' && is_array($task_ids) && count($task_ids)>0 ){
          foreach($task_ids as $idtask){
              $this->changeTaskOwner($evctl->fields["co_worker"],$idtask);
          }
      }
    }

    /**
      * Event method to change the project for multiple task
    */
    function eventChangeProjectForTaskMultiple(EventControler $evctl){
      $task_ids = $evctl->ck;
      if($evctl->project_id != '' && is_array($task_ids) && count($task_ids)>0 ){
          $q = new sqlQuery($this->getDbCon());
          foreach($task_ids as $idtask){
            $q->query("update ".$this->table." set idproject = ".$evctl->project_id. " where idtask = ".$idtask." limit 1");
          }
      }
    }

    /**
      * Event method to change due date for multiple task
    */

    function eventChangeDueDateMultiple(EventControler $evctl){


        if( $evctl->fields['due_date_mul'] != '' &&  $evctl->fields['due_date_mul'] != '0000-00-00'){
              $task_ids = $evctl->ck;

             

           if (preg_match("/-/",$task_ids[0])) {
                $taskid=$evctl->ck;
                $task_ids= array();
                foreach($taskid as $ids){      
                  $explode= explode('-',$ids);
                    if(in_array($explode[0],$task_ids)==false){
                      array_push($task_ids,$explode[0]);
                    }
                }
            }

          $today = date("Y-m-d");//Todays Date
          $form_date=$evctl->fields['due_date_mul'];//Entered Date
                      
          $dateDiff = strtotime($form_date) - strtotime($today);
          $no_of_days = floor($dateDiff/(60*60*24));//Date Difference

         

          $next_day = strtotime ('+1 day',strtotime ($today));//Add one day to the current date to check wheather next day is saturday
          $next_day = date ( 'D',$next_date);

          $this_saturdays_date = date("Y-m-d", strtotime('next Saturday'));//get this saturday's date          
          $sat_difference=strtotime($this_saturdays_date)-strtotime($form_date);
          $sat_no_of_days=round($sat_difference/(60*60*24));



          $this_month_end_date=date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y').' 00:00:00'))));//this month calculation
          $month_difference=strtotime($this_month_end_date)-strtotime($form_date);
          $month_difference=round($month_difference/(60*60*24));
          



 
         if($no_of_days<0){
              $due_date='Today';
          }elseif($no_of_days==0){
              $due_date='Today';
          }elseif($no_of_days==1 && $next_day!='Sat'){
               $due_date='Tomorrow';
          }elseif($no_of_days>=2 && $no_of_days <=5 && $sat_difference>0){
            $due_date='This week';
          }elseif(($sat_no_of_days==0) || ($sat_no_of_days>=-6)){            
            $due_date='Next week';
          }elseif(($month_difference>=0)){
              $due_date='This Month';
          }elseif($month_difference<0){
              $due_date='Later';
              $form_date = '0000-00-00';
          }
  


              if(is_array($task_ids) && count($task_ids) > 0 ){
                  $q = new sqlQuery($this->getDbCon());
                  foreach($task_ids as $idtask){
                    //echo "update task set due_date='$due_date', due_date_dateformat = '".$evctl->fields['due_date_mul']."' where idtask = ".$idtask;exit;
                    $q->query("update task set due_date='$due_date' , due_date_dateformat = '$form_date' where idtask = ".$idtask);
                  }
              }
        }
        
    }

     function eventRenderChangeTaskOwnerList(EventControler $evtcl){
      $idproject = $evtcl->idproject;

       if (preg_match("/-/",$idproject)) {          
              $id_project = $evtcl->idproject;

          $f_explode=explode(',',$id_project);
          $result= array();
          foreach($f_explode as $nf_explode){
            $s_explode= explode('-',$nf_explode);
              if($s_explode[1]==''){
                  $s_explode[1]=0;
                }
              if(in_array($s_explode[1],$result)==false){
                 array_push($result,$s_explode[1]);
              }
            }
          $id='';
              foreach($result as $s_result){
                    $id.=$s_result.',';
              }

          $idproject= rtrim($id,',');
      }

      $do_proj = new Project();
      if($idproject == ""){
      $do_proj->idproject = $this->idproject;
      }else{$do_proj->idproject = $idproject ;}
      $co_workers = $do_proj->getTaskCoWorkers();
      $output = '';
      if(!$co_workers){
          $output .= '<br />'._('No Co-Worker found for this project').'<br />';
      }else{  
          if(is_array($co_workers)){
              $output .='<select name="fields[co_worker]">';
              $output .='<option value="">'._('Select One').'</option>';
              foreach($co_workers as $co_workers){
                  $selected = '';
                  $output .= '<option value="'.$co_workers["idcoworker"].'" '.$selected.'>'.$co_workers["firstname"].' '.$co_workers["lastname"].'</option>';
              }
              $output .= '</select>';
              $output .='<input value="'._('Assign Task To').'" type="submit">';
          }else{
              $output .= '<br />'._('No Co-Worker found for this project').'<br />';
          }
      }
      echo  $output;
  }













}
?>
