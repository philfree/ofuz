<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Tag class
     * Using the DataObject
     * description, due date, category, status (open/closed)
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzCore
     * @license GNU Affero General Public License
     * @version 0.6
     * @date 2010-09-06
     * @since 0.2
     */

class Task extends DataObject {
    
    public $table = "task";
    protected $primary_key = "idtask";

    function __construct(sqlConnect $conx=NULL, $table_name="") {
		parent::__construct($conx, $table_name);
		if (RADRIA_LOG_RUN_OFUZ) {
			$this->setLogRun(OFUZ_LOG_RUN_CONTACT);
		}
    }    

    function addNewTask($description,$due_date,$category,$status){
      $this->description = $description;
      $this->due_date = $due_date;
      $this->category = $category;
      $this->status = $status;
      $this->add();
      
    }

    function updateTask($idtask,$description,$due_date,$category,$status){
      $this->getId($idtask);
      $this->description =$description;
      $this->due_date =$due_date;
      $this->category =$category;
      $this->status =$status;
      $this->update();
      
    }
    
    function deleteTask($idtask){
      $this->getId($idtask);
      $this->delete();
    }

    function updateStatus($idtask,$status){
      $this->getId($idtask);
      $this->status = $status;
      $this->date_completed = date("Y-m-d");
      $this->update();
      if($this->getContactForTask($idtask)){
        include_once("class/ContactNotes.class.php");
        include_once("class/TaskCategory.class.php");
        $do_contact_obj = new ContactNotes();
        $do_task_cat_obj = new TaskCategory();
        //$do_contact_obj->note = 'Task Completed:<br /><span class="task_category">'.$do_task_cat_obj->getTaskCategoryName($this->getTaskCategory($idtask)).'</span>&nbsp;<STRIKE>'.$this->getTaskDetail($idtask).'</STRIKE>';
        $do_contact_obj->note = _('Task Completed:').'<br /><span class="task_category">'.$this->getTaskCategory($idtask).'</span>&nbsp;<STRIKE>'.$this->getTaskDetail($idtask).'</STRIKE>';
        $do_contact_obj->idcontact = $this->getContactForTask($idtask);
        $do_contact_obj->date_added = date("Y-m-d");
        $do_contact_obj->iduser = $_SESSION['do_User']->iduser;
        $do_contact_obj->add();
        
      }
    }

    function tmpUpdateTaskCat($id,$category){
      $q = new sqlQuery($this->getDbCon());
      $q->query("update task set task_category = '".$category."' where idtask = ".$id );
    }

    function getDistinctCategory(){
      $this->query("(select distinct task_category as task_category from ".$this->table." where task_category <> '') 
      UNION
      (select distinct name as task_category from task_category where iduser = 0 )
       order by task_category
      ");
    }

      
    function getDistinctCategoryForUser(){
      $this->query("(select distinct task_category as task_category from ".$this->table." where task_category <> '' AND iduser=".$_SESSION['do_User']->iduser.") 
      UNION
      (select distinct name as task_category from task_category where iduser = ".$_SESSION['do_User']->iduser." OR iduser = 0 )
      order by task_category
      ");
    }

    function eventSetTaskCategory(EventControler $evtcl) {
      //echo 'here ';exit;
    }
    
    function getContactForTask($idtask){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select idcontact from task where idtask = ".$idtask) ;
       //echo "select firstname.lastname from contact where  idcontact = ".$idcontact;
       
       if ($q->getNumRows()) {
          while($q->fetch()){
            $idcontact = $q->getData("idcontact");
          }
        return $idcontact;
       }else{ return false;}
    }

    
    function getTaskDetail($idtask){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select task_description from task where idtask = ".$idtask) ;
       //echo "select firstname.lastname from contact where  idcontact = ".$idcontact;
       
          while($q->fetch()){
            $task_description = $q->getData("task_description");
          }
          return $task_description;
       
    }

    function isTasKOwner($idtask = ""){
        if($idtask == ""){
            $idtask = $this->idtask;
        }
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from ".$this->table ." Where idtask = ".$idtask. " AND iduser = ".$_SESSION['do_User']->iduser);
        if($q->getNumRows()){ 
            return true;
        }else{
            return false;
        }
    }

    // This method can be deleted need to make sure before deleting its no where in use
    /*function getTaskCategory($idtask){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select category from task where idtask = ".$idtask) ;
          while($q->fetch()){
            $task_category = $q->getData("category");
          }
          return $task_category;
       
    }*/

    function getTaskCategory($idtask){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select task_category from task where idtask = ".$idtask) ;
       //echo "select firstname.lastname from contact where  idcontact = ".$idcontact;
       
          while($q->fetch()){
            $task_category = $q->getData("task_category");
          }
          return $task_category;
       
    }
    /**
     * The method to generate the task add form on the task page
     * Uses an event call eventSetDateInFormat
     * using setAddRecord() Dataobject Method
     * 
    */
    function getTaskAddForm(){
      $this->setRegistry("ofuz_add_task");
      $f_taskForm = $this->prepareSavedForm("ofuz_add_task");
      // should be less than 1010 as 1010 is assigned to event eventSetProjectTask
      $f_taskForm->setFormEvent($this->getObjectName()."->eventAdd", 1005);
      $f_taskForm->addEventAction($this->getObjectName()."->eventSetDateInFormat", 10);
      $f_taskForm->setAddRecord();
      $f_taskForm->setUrlNext("tasks.php");
      $f_taskForm->setForm();
      //$f_taskForm->execute();
      return $f_taskForm->executeToString();
    }

    function getiTaskAddForm(){
      $this->setRegistry("task");
      $f_taskForm = $this->prepareSavedForm("i_ofuz_add_task");
      $f_taskForm->setFields("task_mobile");
      $f_taskForm->setFormEvent($this->getObjectName()."->eventAdd", 1005);
      $f_taskForm->addEventAction($this->getObjectName()."->eventSetDateInFormat", 10);
      $f_taskForm->setAddRecord();
      $f_taskForm->setUrlNext("i_tasks.php");
      $f_taskForm->setForm();
      $f_taskForm->execute();
    }

    function getTaskAddContactRelatedForm(){
      if (is_object($_SESSION["eDetail_contact"])) {
        $this->setRegistry("task_contact_related");
      }elseif(is_object($_SESSION['ContactEditSave'])) {
        $this->setRegistry("task_contact_related_ContactEditSave");
      }
      $f_taskForm = $this->prepareSavedForm("ofuz_add_task_contact_related");
      // should be less than 1010 as 1010 is assigned to event eventSetProjectTask
      $f_taskForm->setFormEvent($this->getObjectName()."->eventAdd", 1005);
      $f_taskForm->addEventAction($this->getObjectName()."->eventSetDateInFormat", 10);
      $f_taskForm->setAddRecord();
      $f_taskForm->setUrlNext("tasks.php");
      $f_taskForm->setForm();
      //$f_taskForm->execute();
      return $f_taskForm->executeToString();
    }

    
    
    /**
      * Event method to set a project on a task.
      * Get the last inserted id from the task and then check
      * if there is a project then add to project_task
    */
    function eventSetProjectTask(EventControler $evtcl) {
      $project_name = trim($evtcl->fields["project"]);
      $idtask = $evtcl->insertid;
      $q = new sqlQuery($this->getDbCon());
      if($project_name !=''){
        $do_project = new Project();
        $do_project_task = new ProjectTask();
        $idproject = $do_project->getProjectIdByName($project_name);
        
        if($idproject !== false){
           $q->query("INSERT INTO project_task (idtask, idproject) VALUES (".$idtask.", ".$idproject.")");
        }else{
           $do_project->addNew();
           $do_project->iduser = $_SESSION['do_User']->iduser;
           $do_project->name = $project_name;
           $do_project->status = 'open';
           $do_project->add();
           $idproject = $do_project->getPrimaryKeyValue();
           $q->query("INSERT INTO project_task (idtask, idproject) VALUES (".$idtask.", ".$idproject.")");
        }
      }
    }

    /**
     * Event Controller that will take the form data for the task
     * The due date here is a string so that string will be
     * converted to Mysql date format Y-m-d with cool PHP 
     * strtotime() method 
     * Finally the field array will be updated
     * with the modified data.
     * 
    */
    function eventSetDateInFormat(EventControler $evtcl) {
      $fields = $evtcl->getParam("fields");
      $dutedate = $fields["due_date"];
      $sp_date_sel_hidden = $_POST["sp_date_selected"];
      if($sp_date_sel_hidden == ""){
        if($dutedate != 'Later'){
          switch($dutedate){
            case "Today":
                $formated_date = date("Y-m-d");
                break;
            case "Tomorrow":
                $formated_date = date("Y-m-d",strtotime("+1 day"));
                break;
            case "This week":
                $formated_date = date("Y-m-d",strtotime("next Friday"));
                break;
            case "Next week":
                $formated_date = date("Y-m-d",strtotime("next Friday",strtotime("+1 week")));
                break;
            case "This month":
                $formated_date = date('Y-m-d',strtotime('-1 second',strtotime('+1 month',strtotime(date('m').'/01/'.date('Y').' 00:00:00'))));
                break;
          }
          $fields["due_date_dateformat"] = $formated_date;
          //print_r($fields);exit;
          $evtcl->updateParam("fields", $fields) ;
        }else{
          $fields["due_date_dateformat"] = '0000-00-00';
          $evtcl->updateParam("fields", $fields) ;
        }
      }elseif($sp_date_sel_hidden == "Yes"){
        $formated_date = $fields["due_date_dateformat"];
        $today = date("Y-m-d");
        if($formated_date == '0000-00-00' || $formated_date == '' || empty($formated_date)){
          $return_string = 'Later';
        }else{
          $difference = strtotime($formated_date) - strtotime($today);
          $date_diff = round((($difference/60)/60)/24,0);
          if($date_diff < 0 ){
            $return_string = 'Today';
          }elseif($date_diff == 0 ){
            $return_string = 'Today';
          }elseif($date_diff == 1){
            $return_string = 'Tomorrow';
          }elseif($date_diff >=2 && $date_diff < 8){
            $return_string = 'This week';
          }elseif($date_diff >7 && $date_diff < 15){
            $return_string = 'Next week';
          }elseif($date_diff > 15){
            $return_string = 'Later';
          }
        }
        $fields["is_sp_date_set"] = "Yes";
        $fields["due_date"] = $return_string;
        //print_r($fields);exit;
        $evtcl->updateParam("fields", $fields) ;
      }
    }

    /**
     * Method to change the date into the string
     * 
    */
    function convertDateToString($date){ 
      $return_string  = '';
      $today = date("Y-m-d");
      if($date == '0000-00-00' || $date == '' || empty($date)){
          $return_string = 'Later';
      }else{
        $difference = strtotime($date) - strtotime($today);
        $date_diff = round((($difference/60)/60)/24,0);
        if($date_diff < 0 ){
          $return_string = 'Today';
        }elseif($date_diff == 0 ){
          $return_string = 'Today';
        }elseif($date_diff == 1){
          $return_string = 'Tomorrow';
        }elseif($date_diff >=2 && $date_diff < 8){
          $return_string = 'This week';
        }elseif($date_diff >7 && $date_diff < 15){
          $return_string = 'Next week';
        }elseif($date_diff > 15){
          $return_string = 'Later';
        }
      }
      return $return_string;
    }

    function getDateFormatForTask($idtask){
      $q = new sqlQuery($this->getDbCon());
      $q->query("select due_date_dateformat from task where idtask =".$idtask);
      $q->fetch();
      return $q->getData("due_date_dateformat");
    }

    /**
     * Method to get the task for the day
     * 
    */    

    function getAllTasksToday(){
      $today = date('Y-m-d');
      $this->query("SELECT * FROM task WHERE DATEDIFF(due_date_dateformat,'".$today."') = 0  
      AND due_date_dateformat <> '0000-00-00' AND status = 'open' 
      AND iduser = ".$_SESSION['do_User']->iduser." 
      ORDER BY due_date_dateformat");
    }

    /**
     * Method to get the task which is overdue
     * 
    */ 
    function getAllTasksOverdue(){
      $today = date('Y-m-d');
      $this->query("select * from task where DATEDIFF(due_date_dateformat,'".$today."') < 0
       AND due_date_dateformat <> '0000-00-00' AND status = 'open' 
       AND iduser = ".$_SESSION['do_User']->iduser." 
       ORDER BY   due_date_dateformat LIMIT 10");
    }

    function eventAjaxGetAllTasksOverdue(EventControler $evtcl) {
      $today = date('Y-m-d');
      $this->query("select * from task where DATEDIFF(due_date_dateformat,'".$today."') < 0
       AND due_date_dateformat <> '0000-00-00' AND status = 'open' 
       AND iduser = ".$_SESSION['do_User']->iduser." 
       ORDER BY   due_date_dateformat");
	  $html = $this->viewTasks();
	  echo $html;
	}

    function getNumAllTasksOverdue(){
      $today = date('Y-m-d');
      $this->query("select * from task where DATEDIFF(due_date_dateformat,'".$today."') < 0
       AND due_date_dateformat <> '0000-00-00' AND status = 'open' 
       AND iduser = ".$_SESSION['do_User']->iduser." 
       ORDER BY   due_date_dateformat");
	  return $this->getNumRows();
    }

    /**
     * Method to get the task for tomorrow
     * 
    */ 
    function getAllTasksTomorrow(){
      $today = date('Y-m-d');
      $this->query("select * from task where DATEDIFF(due_date_dateformat,'".$today."') = 1
      AND due_date_dateformat <> '0000-00-00' AND status = 'open'
      AND iduser = ".$_SESSION['do_User']->iduser." 
      ORDER BY due_date_dateformat");
    }

    /**
     * Method to get the task for this week
     * 
    */ 
    function getAllTasksThisWeek(){
      $today = date('Y-m-d');
      $this->query("select * from task where DATEDIFF(due_date_dateformat,'".$today."') >=2 
      AND DATEDIFF(due_date_dateformat,'".$today."') < 8
      AND due_date_dateformat <> '0000-00-00' AND status = 'open'
      AND iduser = ".$_SESSION['do_User']->iduser." 
       ORDER BY due_date_dateformat");
    }

    /**
     * Method to get the task for next week
     * 
    */
    function getAllTasksNextWeek(){
      $today = date('Y-m-d');
      $this->query("select * from task where DATEDIFF(due_date_dateformat,'".$today."') >7 
      AND DATEDIFF(due_date_dateformat,'".$today."') < 15
      AND due_date_dateformat <> '0000-00-00' AND status = 'open'
      AND iduser = ".$_SESSION['do_User']->iduser." 
       ORDER BY due_date_dateformat");
    }

    /**
     * Method to get the top 20 tasks for later
     * 
    */
    function getAllTasksLater(){
      $today = date('Y-m-d'); //cur_year-cur_month
	  $later_date = date('Y-m');
      //$this->query("SELECT * FROM task WHERE (due_date_dateformat = '0000-00-00' OR DATEDIFF(due_date_dateformat,'".$today."') >15 ) AND status = 'open' AND iduser = ".$_SESSION['do_User']->iduser." ORDER BY due_date_dateformat DESC LIMIT 20");
      $this->query("SELECT * FROM task WHERE (due_date_dateformat = '0000-00-00' OR DATE_FORMAT( due_date_dateformat, '%Y-%m' ) > '".$later_date."' ) AND status = 'open' AND iduser = ".$_SESSION['do_User']->iduser." 
	   AND `due_date` != 'Tomorrow'
	   AND `due_date` != 'This week'
	   AND `due_date` != 'Next week'
	  ORDER BY due_date_dateformat DESC LIMIT 20");

    }

    function getNumAllTasksLater(){
      $today = date('Y-m-d');
      $this->query("SELECT * FROM task WHERE (due_date_dateformat = '0000-00-00' OR DATEDIFF(due_date_dateformat,'".$today."') >15 ) AND status = 'open' AND iduser = ".$_SESSION['do_User']->iduser." 
	   AND `due_date` != 'Tomorrow'
	   AND `due_date` != 'This week'
	   AND `due_date` != 'Next week'
      ORDER BY due_date_dateformat DESC");
	  return $this->getNumRows();
    }

    /**
     * Method to get All tasks for later
     * 
    */
    function eventAjaxGetAllTasksLater(EventControler $evtcl) {
      $today = date('Y-m-d');
      $this->query("SELECT * FROM task WHERE (due_date_dateformat = '0000-00-00' OR DATEDIFF(due_date_dateformat,'".$today."') >15 ) AND status = 'open' AND iduser = ".$_SESSION['do_User']->iduser." 
	   AND `due_date` != 'Tomorrow'
	   AND `due_date` != 'This week'
	   AND `due_date` != 'Next week'
	  ORDER BY due_date_dateformat DESC");
	  $html = $this->viewTasks();
	  echo $html;
	}

    /**
     * Method to get the task/tasks for this month
     * 
    */ 

    function getAllTasksThisMonth(){
	  $current_month = date('m');
	  $current_year = date('Y');
      $this->query("select * from task where YEAR(`due_date_dateformat`) = '{$current_year}'
	   AND MONTH(`due_date_dateformat`) = '{$current_month}'
       AND due_date_dateformat <> '0000-00-00' AND status = 'open' 
       AND iduser = ".$_SESSION['do_User']->iduser." 
	   AND `due_date` != 'Today'
	   AND `due_date` != 'Tomorrow'
	   AND `due_date` != 'This week'
	   AND `due_date` != 'Next week'
       ORDER BY   due_date_dateformat DESC LIMIT 20");
    }

    function eventAjaxGetAllTasksThisMonth(EventControler $evtcl) {
	  $current_month = date('m');
	  $current_year = date('Y');
      $this->query("select * from task where YEAR(`due_date_dateformat`) = '{$current_year}'
	   AND MONTH(`due_date_dateformat`) = '{$current_month}'
       AND due_date_dateformat <> '0000-00-00' AND status = 'open' 
       AND iduser = ".$_SESSION['do_User']->iduser." 
	   AND `due_date` != 'Today'
	   AND `due_date` != 'Tomorrow'
	   AND `due_date` != 'This week'
	   AND `due_date` != 'Next week'
       ORDER BY   due_date_dateformat DESC");
	  $html = $this->viewTasks();
	  echo $html;
	}

    function getNumAllTasksThisMonth(){
	  $current_month = date('m');
	  $current_year = date('Y');
      $this->query("select * from task where YEAR(`due_date_dateformat`) = '{$current_year}'
	   AND MONTH(`due_date_dateformat`) = '{$current_month}'
       AND due_date_dateformat <> '0000-00-00' AND status = 'open' 
       AND iduser = ".$_SESSION['do_User']->iduser." 
	   AND `due_date` != 'Today'
	   AND `due_date` != 'Tomorrow'
	   AND `due_date` != 'This week'
	   AND `due_date` != 'Next week'
       ORDER BY   due_date_dateformat DESC");
	  return $this->getNumRows();
    }

    /** 
     * View to display the tasks
     */
    function viewTasks() {
       return $this->taskDisplay("Yes");
    }
    /** 
     * View to display the tasks for contacts
     */
    function viewContactsTasks() {
        return $this->taskDisplay();
    }
    /** 
     * HTML display part of task
     */
    function taskDisplay($showContact="No"){
        $do_task_category = new TaskCategory();
        $do_contact_task = new Contact();
        $html = '';
        while ($this->next()) {
            //$category = $do_task_category->getTaskCategoryName($this->category);
            $category = $this->task_category;
            $html .= "\n".'<span id="t'.$this->idtask.'" class="task_item">';
            $html .='<input type="checkbox" name="c'.$this->idtask.'" class="task_checkbox" onclick="fnTaskComplete(\''.$this->idtask.'\')" />';
            if ($category != '') { 
                $html .= '<span class="task_category">'.$category.'</span>&nbsp;'; 
            }
            $html .='<span class="task_desc"><a href="#" onclick="fnEditTask(\''.$this->idtask.'\'); return false;">'.$this->task_description.'</a>';
            if($showContact == 'Yes'){
                if ($this->idcontact) {
                    $do_contact_task->idcontact = $this->idcontact;
                    if($this->from_note){
                        $html .= $do_contact_task->getContactNameTaskRelated();
                    }else{
                        $html .= $do_contact_task->getContactNameContactRelatedTask();
                    }
                }
            }
            $proj_task_data = $this->isProjectRelatedTask();//print_r($proj_task_data);
            if($proj_task_data){
               $do_task_project = new Project();
               $do_task_project->getId($proj_task_data["idproject"]);
               $currentpage = $_SERVER['PHP_SELF'];
               /*$e_detail = new Event("mydb.gotoPage");
               $e_detail->addParam("goto", "task.php");
               $e_detail->addParam("idproject_task",$proj_task_data["idproject_task"]);
               $e_detail->addParam("tablename", "project_task");
               $e_detail->requestSave("eDetail_ProjectTask", "task.php");
 
               $e_proj = new Event("mydb.gotoPage");
               $e_proj->addParam("goto", "project.php");
               $e_proj->addParam("idproject",$proj_task_data["idproject"]);
               $e_proj->addParam("tablename", "project");
               $e_proj->requestSave("eDetail_Project", "project.php");*/
               
               $project_url = '/Project/'.$proj_task_data["idproject"];
               $project_task_url = '/Task/'.$proj_task_data["idproject_task"];

               $img_url = '<img src="/images/discussion.png" width="16" height="16" alt="" />';
               $html .='&nbsp;&nbsp;<b>
                        <a href="'.$project_url.'">'.$do_task_project->name.'</b></a>
                          &nbsp;&nbsp;<a href="'.$project_task_url.'">'.$img_url.'</a>';
            }
            $html .= '</span></span>';
            $html .= '<div id="e'.$this->idtask.'" style="display: none;"></div>';
            $html .= '<span id="b'.$this->idtask.'"><br /></span>';
        } 
        return $html;
    }

    function isProjectRelatedTask(){
      $q = new sqlQuery($this->getDbCon()) ;
      $q->query("select * from project_task where idtask = ".$this->idtask);
      if($q->getNumRows()){
          $data_array = array();
          $q->fetch();
          $data_array["idproject"]= $q->getData("idproject");
          $data_array["idproject_task"] = $q->getData("idproject_task");
          return $data_array;
      }else{
          return false;
      }
    }
    /**
     * Method to get the distinct completed task dates in 
     * the form Month Year
     * 
    */
    function getDistinctCompletedTaskDates(){
      $this->query("select distinct  DATE_FORMAT(date_completed,'%b %Y') as formated_date 
      from task where status = 'closed'  AND iduser = ".$_SESSION['do_User']->iduser." Order by date_completed desc" ); 
    }

    /**
     * Method to get the task for the formated 
     * date as Month Year
     * 
    */
    function getAllCompletedTasks($monthyear){
      $this->query("select * from task where 
      status = 'closed' 
      AND iduser = ".$_SESSION['do_User']->iduser." 
      AND DATE_FORMAT(date_completed,'%b %Y')= '".$monthyear."' order by idtask desc"); 
    }

    /**
     * Event Method to generate the Task edit form
     * This is an Ajax call
    */
    function eventAjaxEditTaskForm(EventControler $event_controler) {
        $form = '<div class="taskbox1a"><div class="taskbox1b"><div class="taskbox1c">';
        $this->getId($event_controler->id);
        $this->sessionPersistent("TaskEdit", "index.php", 120);
        $e_task = new Event("TaskEdit->eventUpdate");
        $e_task->setLevel(1999);
        $e_task->setDomId('form'.$event_controler->id);
        $e_task->setSecure(false);
        $e_task->addEventAction("TaskEdit->eventSetDateInFormat",1487);
        $form .= $e_task->getFormHeader();
        $form .= $e_task->getFormEvent();
        $is_sp_date_set = $_SESSION['TaskEdit']->is_sp_date_set;
        $task_desc = $_SESSION['TaskEdit']->task_description;
        $_SESSION['TaskEdit']->setRegistry("task");
        $_SESSION['TaskEdit']->setApplyRegistry(true, "Form");
     
        //$_SESSION['TaskEdit']->fields->addField(new FieldTypeChar("task_description"));
        //$_SESSION['TaskEdit']->fields->task_description->css_form_style = "width:100%;";
        
        //$form .= $_SESSION['TaskEdit']->task_description . '<br /><br />';
        // FieldTypeChar needs to be corrected to support the NON ASCII characters
        $form .= '<input type="text" name="fields[task_description]" style="width: 100%;" value="'.$task_desc.'">'. '<br /><br />';
        $_SESSION['TaskEdit']->due_date = $this->convertDateToString($this->getDateFormatForTask($event_controler->id));

        if($is_sp_date_set == "Yes"){
            $form .= '<span class="text8">'._('When\'s it due? (Specific Date) (YYYY/mm/dd)').'<br />';
            $_SESSION['TaskEdit']->fields->addField(new strFBFieldTypeDateSQL("due_date_dateformat"));
            $_SESSION['TaskEdit']->fields->due_date_dateformat->datesql = "Y/m/d::";
            $form .= $_SESSION['TaskEdit']->due_date_dateformat . '</span><br /><br />';
            $form .= '<input type="hidden" name="sp_date_selected" id="sp_date_selected" value="Yes">';
        }else{
            $form .= '<span class="text8" id="when_pop_up">'._('When\'s it due?').'</span><br />';
            $form .= $_SESSION['TaskEdit']->due_date . '<br /><br />';
            $form .= '<input type="hidden" name="sp_date_selected" id="sp_date_selected" value="">';
        }
        
        $form .= '<span class="text8">'._('Choose a category').'</span><br />';
        //$form .= $_SESSION['TaskEdit']->category . '<br /><br />';
        $form .= $_SESSION['TaskEdit']->task_category . '<br /><br />';
        $form .= $e_task->getFormFooter(_("Save this task"));
        $form .= '<div class="cancellink">'._('or').' <a href="#" onclick="fnCancelEdit(' . $event_controler->id . '); return false;">'._('cancel').'</a></div>';
        $form .= '</div></div></div>';
        $event_controler->addOutputValue($form);
    }

    function eventAjaxEditTaskFormRHS(EventControler $event_controler) {
        $form = '<div class="taskbox1a"><div class="taskbox1b"><div class="taskbox1c">';
        $this->getId($event_controler->id);
        $this->sessionPersistent("TaskEdit", "index.php", 120);
        $e_task = new Event("TaskEdit->eventUpdate");
        $e_task->setLevel(1999);
        $e_task->setDomId('form'.$event_controler->id);
        $e_task->setSecure(false);
        $e_task->addEventAction("TaskEdit->eventSetDateInFormat",1487);
        $form .= $e_task->getFormHeader();
        $form .= $e_task->getFormEvent();
        $is_sp_date_set = $_SESSION['TaskEdit']->is_sp_date_set;
        $_SESSION['TaskEdit']->setRegistry("task");
        $_SESSION['TaskEdit']->setApplyRegistry(true, "Form");

        $_SESSION['TaskEdit']->fields->addField(new strFBFieldTypeText("task_description"));
        $_SESSION['TaskEdit']->fields->task_description->css_form_style = "width:100%;";
        
        $form .= $_SESSION['TaskEdit']->task_description . '<br /><br />';
        $_SESSION['TaskEdit']->due_date = $this->convertDateToString($this->getDateFormatForTask($event_controler->id));

        if($is_sp_date_set == "Yes"){
            $form .= '<span class="text8">'._('When\'s it due? (Specific Date) (YYYY/mm/dd)').'<br />';
            $_SESSION['TaskEdit']->fields->addField(new strFBFieldTypeDateSQL("due_date_dateformat"));
            $_SESSION['TaskEdit']->fields->due_date_dateformat->datesql = "Y/m/d::";
            $form .= $_SESSION['TaskEdit']->due_date_dateformat . '</span><br /><br />';
            $form .= '<input type="hidden" name="sp_date_selected" id="sp_date_selected" value="Yes">';
        }else{
            $form .= '<span class="text8" id="when_pop_up">'._('When\'s it due?').'</span><br />';
            $form .= $_SESSION['TaskEdit']->due_date . '<br /><br />';
            $form .= '<input type="hidden" name="sp_date_selected" id="sp_date_selected" value="">';
        }

        $form .= '<span class="text8">'._('Choose a category').'</span><br />';
        //$form .= $_SESSION['TaskEdit']->category . '<br /><br />';
        $form .= $_SESSION['TaskEdit']->task_category . '<br /><br />';
        $form .= $e_task->getFormFooter(_("Save this task"));
        $form .= '<div class="cancellink">or <a href="#" onclick="fnCancelEdit(' . $event_controler->id . ');">'._('cancel').'</a></div>';
        $form .= '</div></div></div>';
        $event_controler->addOutputValue($form);
    }

    function eventAjaxTaskComplete(EventControler $evctl) {
        $this->updateStatus($evctl->id,'closed');
        $this->query("UPDATE project_task SET priority = (SELECT priority FROM (SELECT MAX(priority)+1 AS priority FROM project_task WHERE idproject = (SELECT idproject FROM project_task WHERE idtask = {$evctl->id})) AS pri) WHERE idtask = {$evctl->id};");
        $evctl->addOutputValue('ok');
    }

    function updateTaskCategory($idtask_category){
        $this->query("update task set category = '' where category = ".$idtask_category);
    }
 
    function getContactRelatedOverdueTask($idcontact){
      $today = date('Y-m-d');
      $this->query("select * from task where idcontact = ".$idcontact
                   ." And DATEDIFF(due_date_dateformat,'".$today."') < 0 AND
                    status = 'open' AND iduser = ".$_SESSION['do_User']->iduser
                   ." ORDER BY   due_date_dateformat");
     }

     function getContactRelatedTodayTask($idcontact){
      $today = date('Y-m-d');
      $this->query("SELECT * FROM task WHERE DATEDIFF(due_date_dateformat,'".$today."') = 0  
       AND due_date_dateformat <> '0000-00-00' AND status = 'open' 
       AND iduser = ".$_SESSION['do_User']->iduser." AND  idcontact = ".$idcontact
      ." ORDER BY due_date_dateformat");
     }

     function getContactRelatedTomorrowTask($idcontact){
      $today = date('Y-m-d');
      $this->query("select * from task where DATEDIFF(due_date_dateformat,'".$today."') = 1
       AND due_date_dateformat <> '0000-00-00' AND status = 'open'
       AND iduser = ".$_SESSION['do_User']->iduser." AND idcontact = ".$idcontact 
      ." ORDER BY due_date_dateformat");
     }

     function getContactRelatedThisWeekTask($idcontact){
      $today = date('Y-m-d');
      $this->query("select * from task where DATEDIFF(due_date_dateformat,'".$today."') >=2 
       AND DATEDIFF(due_date_dateformat,'".$today."') < 8
       AND due_date_dateformat <> '0000-00-00' AND status = 'open'
       AND iduser = ".$_SESSION['do_User']->iduser." AND idcontact = ".$idcontact
      ." ORDER BY due_date_dateformat");
     }

     function getContactRelatedNextWeekTasks($idcontact){
      $today = date('Y-m-d');
      $this->query("select * from task where DATEDIFF(due_date_dateformat,'".$today."') >7 
       AND DATEDIFF(due_date_dateformat,'".$today."') < 15
       AND due_date_dateformat <> '0000-00-00' AND status = 'open'
       AND iduser = ".$_SESSION['do_User']->iduser." AND idcontact = ".$idcontact
      ." ORDER BY due_date_dateformat");
     }

     function getContactRelatedLaterTasks($idcontact){
       $today = date('Y-m-d');
       $this->query("SELECT * FROM task WHERE (due_date_dateformat = '0000-00-00' OR DATEDIFF(due_date_dateformat,'".$today."') >15 ) AND status = 'open' AND iduser = ".$_SESSION['do_User']->iduser." AND idcontact = ".$idcontact." ORDER BY due_date_dateformat");
     }

	function getTotalNumTasksForUser($iduser) {
		$q = new sqlQuery($this->getDbCon());
		$sql = "SELECT COUNT(idtask) AS total_tasks 
				FROM `{$this->table}` 
				WHERE `iduser` = {$iduser}
			   ";
		$q->query($sql);
		if($q->getNumRows()) {
			$q->fetch();
			return $q->getData("total_tasks");
		} else {
			return "0";
		}
	}


      /**
          Method to get all the tasks related to projects
      */

      function getAllTaskProjectRelated(){
          $this->query("SELECT project_task.idtask,project_task.idproject_task,project_task.idproject,project_task.priority, 
                        task.task_description, task.task_category, task.iduser, task.status, project.name,project.status
                        FROM project_task
                        INNER JOIN project ON project.idproject = project_task.idproject
                        INNER JOIN task ON task.idtask = project_task.idtask
                        WHERE task.iduser =".$_SESSION['do_User']->iduser."
                        AND task.status = 'open'
                        AND project.status = 'open'
                        ORDER BY project.name,project_task.priority asc");
      }

  /**
   * get task associated with contact but task not associated with Project.
   * @param int idcontact for the task
   * @return query object
   */

  function getContactTaskWithoutProject($idcontact) {
    $sql = "SELECT t.*
    FROM `{$this->table}` AS t
    LEFT JOIN `project_task` AS pt ON t.idtask = pt.idtask
    WHERE t.idcontact = {$idcontact}
    AND t.iduser = {$_SESSION['do_User']->iduser}
    AND pt.idtask IS NULL
    ";
    $this->query($sql);
  }

  /**
   * get tasks which are neither associated with Project nor contact.
   * @return query object
   */

  function getTasksWithoutProject() {
    $sql = "SELECT t.*
    FROM `{$this->table}` AS t
    LEFT JOIN `project_task` AS pt ON t.idtask = pt.idtask
    WHERE t.idcontact = 0
    AND t.iduser = {$_SESSION['do_User']->iduser}
    AND pt.idtask IS NULL
    ";
    $this->query($sql);
  }

  /**
   * get tasks which are associated with Project but not with contact.
   * @return query object
   * @param int : $idproject
   */

  function getTasksAssociatedWithProject($idproject) {
    $sql = "SELECT pt.*,t.*
    FROM `project_task` AS pt
    INNER JOIN `{$this->table}` AS t ON t.idtask = pt.idtask
    WHERE pt.idproject = {$idproject} AND t.idcontact = 0
    ";
    $this->query($sql);
  }
      
  /**
   * get Contact tasks which are associated with Project.
   * @return query object
   * @param int : $idcontact
   */

  function getContactTasksAssociatedWithProject($idcontact) {
    $sql = "SELECT pt.*,t.*
    FROM `project_task` AS pt
    INNER JOIN `{$this->table}` AS t ON t.idtask = pt.idtask
    WHERE t.iduser = {$_SESSION['do_User']->iduser} AND  t.idcontact = {$idcontact}
    ";
    $this->query($sql);
  }


/**
   * get task details for particular user.
   * @return query object
   * @param int : $iduser
   */

 function getAllTaskByuser($iduser){
  
  $sql = "select * from task where iduser='$iduser' and status !='closed' order by due_date_dateformat desc";
  
  $this->query($sql);
  
 }

/**
   * get iduser from user table.
   * @return query object
   * @param int : $apikey
   */

 function getUserid($apikey){

        $q = new sqlQuery($this->getDbCon());
        $q->query("select iduser from user where api_key='$apikey'");
       
       
       if ($q->getNumRows()) {
          while($q->fetch()){
            $iduser = $q->getData("iduser");
            
          }
        return $iduser;
       }else{ return false;}
    }



}
?>
