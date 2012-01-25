<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Project class
     * Managed most of the action, data manipulation and display related to Projects.
     *
     * description, due date, category, status (open/closed)
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzPage
     * @license GNU Affero General Public License
     * @version 0.6
     * @date 2010-09-06
     * @since 0.3
     */

class Project extends DataObject {
    
    public $table = 'project';
    protected $primary_key = 'idproject';
    public $project_status = "";
    function addNewProject($iduser,$name,$idcompany) {
        $this->iduser = $iduser;
        $this->name = $name;
        $this->idcompany = $idcompany;
        $this->add(); 
    }

    /**
     * display a form to add a project
     * The form HTML is in the forms/ofuz_add_project.form.xml
     * template.
     * @return the HTML code to display the form
     */

    function getProjectAddForm() {
        $this->setRegistry('ofuz_add_project');
        $f_projectForm = $this->prepareSavedForm('ofuz_add_project');
        $f_projectForm->setAddRecord();
        $f_projectForm->setUrlNext('projects.php');
        $f_projectForm->setForm();
        //$f_projectForm->execute();
	    return $f_projectForm->executeToString();
    }

    function getAllProjects($filter = "") {
         if($filter == ""){ 
            $status = " AND status = 'open'";
         }else{ $status = " AND status = '".$filter."'"; }
         //$status= "";
         $projects_array = $this->getProjectsAsCoWorkers();
         if(!$projects_array){
             $users = $_SESSION['do_User']->iduser;
             $qry = "SELECT * FROM project WHERE 
                      iduser = ".$_SESSION['do_User']->iduser. $status."  
                      ORDER BY name, end_date_dateformat";
         }else{
             $projects = array();
             if(is_array($projects_array)){
                   foreach($projects_array as $projects_array){
                     $projects[] =  $projects_array;
                   }
              $qry = "select * FROM project where (iduser = ".$_SESSION['do_User']->iduser." OR
                      idproject IN (".implode(",",$projects).") )".$status." ORDER BY name,end_date_dateformat
                     " ;
             }else{
                $qry = "SELECT * FROM project WHERE 
                      iduser = ".$_SESSION['do_User']->iduser.$status." 
                      ORDER BY name,end_date_dateformat";
             }
         }
        //echo $qry;
        $this->query($qry);
    }

    /**
    * Function to get the projectid by name from the list of projects including as Co-Worker
    * return idproject if found else returns false
    * @param name :string
    */
    function getProjectIdByName($name){
        $this->getAllProjects();
        $ret_flag = false;
        while($this->next()){
              if($this->name == $name){
                  $ret_flag =  $this->idproject;
                  break;
              }
        }
        return $ret_flag;  
    }

    function eventSetProjectStatus(EventControler $evtcl){
        $this->setProjectStatus($evtcl->status);
    }    

    function setProjectStatus($status){
      $this->project_status = $status;
    }
  
    function getProjectStatus(){
        return $this->project_status;
    }


    function viewProjects() {    
        $html = '';
        while ($this->next()) {
            $html .= '<div class="project" id="p'.$this->idproject.'">';
            //$html .= '<span class="project_name"><a href="'.$this->getProjectUrl($this->idproject).'">'.$this->name . '</a></span><br />';
            $html .= '<span class="project_name"><a href="/Project/'.$this->idproject.'">'.$this->name . '</a></span>&nbsp;&nbsp;&nbsp;';
            if ($this->idcompany > 0) $html .= $this->getCompanyName($this->idcompany);
            $html .= '</div>'."\n".'<div class="solidline"></div>'."\n";
        }
        return $html;
    }

    /*
     * Creates HTML options list from getAllProjects()
     */
    function getProjectsSelectOptions($selected_value=0) {
        $html = '';
        while ($this->next()) {
        	$selected = ($this->idproject == $selected_value) ? ' selected="selected"' : '';
            $html .= '<option value="'.$this->idproject.'"'.$selected.'>'.$this->name.'</option>';
        }
        return $html;
    }

    function getProjectUrl($idproject=0) {
        $e_detail = new Event('mydb.gotoPage');
        $e_detail->addParam('goto', 'project.php');
        $e_detail->addParam('idproject', $idproject);
        $e_detail->addParam('tablename', 'project');
        //$e_detail->requestSave("eDetail_contact", "contacts.php");
        return $e_detail->getUrl();
    }

    function getCompanyName($idcompany=0) {
        $q = new sqlQuery($this->getDbCon());
        $q->query("select name from company where idcompany = ".$idcompany);
        while ($q->fetch()) {
            $company_name = $q->getData('name');
        }
        return $company_name;
    }

    function getProjectName($idproject = ""){
        if($idproject == "" ) { 
			$project_name = $this->name;
		} else {
			$q = new sqlQuery($this->getDbCon());
			$q->query("select name from ".$this->table. " where ".$this->primary_key." = ".$idproject);
			$q->fetch();
			$project_name = $q->getData("name");
		}
		return $project_name;
    }

    function getProjectDetails($idproject="") {
        if($idproject == 0 ){ $idproject = $this->idproject; }
        $this->query("SELECT p.*, c.name AS company_name FROM project p LEFT JOIN company c ON p.idcompany = c.idcompany WHERE p.idproject = ".$idproject);
        return $this->getValues();
    }


    /**
     * Add a breadcrumb for current project
     */

    function setBreadcrumb() {
        $do_breadcrumb = new Breadcrumb();
        $do_breadcrumb->type = 'Project';
        if (is_object($_SESSION['do_User'])) {
            $do_breadcrumb->iduser = $_SESSION['do_User']->iduser;
        }
        $do_breadcrumb->id = $this->idproject;
        $do_breadcrumb->add();
    }

  
    /**
     *   Function to get the list of projects that are assigned to a user
     *   as a Co-Worker. 
     *   Returns the array with the idproject if there is some data
     *   Returns false if no project as a Co-Worker.
    */
    function getProjectsAsCoWorkers(){
      $q = new sqlQuery($this->getDbCon());
      $q->query("select idproject from project_sharing where idcoworker = ".$_SESSION["do_User"]->iduser);
      if($q->getNumRows()){
            $projects_array = array();
            while($q->fetch()){
                $projects_array[] = $q->getData("idproject");
            }
            return $projects_array;
      }else{
        return false;
      }
    }

    function getOtherUserAsProjectCoworker(){
            $projects = $this->getProjectsAsCoWorkers();
            if($projects && is_array($projects)){
                $project_worker_array = array();
                $q = new sqlQuery($this->getDbCon());
                foreach($projects as $projects ){
                      $q->query("select * from project_sharing where idproject = ".$projects["idproject"]);
                      //echo "select * from project_sharing where idproject = ".$projects["idproject"];
                      if($q->getNumRows()){ 
                          while($q->fetch()){
                              if($q->getData("idcoworker") != $_SESSION['do_User']->iduser){
                                  $project_worker_array[] = $q->getData("idcoworker");
                              }
                          }
                      }
                }
              
              $project_worker_array = array_unique($project_worker_array);
              return $project_worker_array;
            }else{ return false; }
    }

    /**
      *  Function to generate the form for adding a Co-Worker to a project.
      *  Will check for the project is some of the Co-Worker are already
      *  sharing the project them they will not be shown in the drop down 
      *  of Co-Workers.
    */

    function addProjectCoWorkerForm(){
        $q = new sqlQuery($this->getDbCon());
        /*$q->query(" SELECT user.firstname as firstname,user.lastname as lastname,user.iduser as iduser from user 
                    where
                    user.iduser IN (select idcoworker from user_relations 
                    where iduser = ".$_SESSION["do_User"]->iduser." AND accepted = 'Yes') AND
                    user.iduser NOT IN 
                    (select idcoworker from project_sharing where iduser = ".$_SESSION["do_User"]->iduser." AND idproject =". $this->idproject.") Order by firstname");

        if($q->getNumRows()){
            $output = '';
            $output .='<select name="fields[co_worker]">';
            $output .='<option value="">'._('Select One').'</option>';
            while($q->fetch()){
              $output .= '<option value="'.$q->getData("iduser").'">'.$q->getData("firstname").' '.$q->getData("lastname").'</option>';
              $output .= '<br />'.$q->getData("firstname");
            }
            $output .= '</select><br /><br />';
            $output .='<input value="'._('Add this Co-Worker').'" type="submit">';
            $output .='<br /><br />'._('not in this list ? ').'<a href="/co_workers.php">'._('add Co-Workers').'</a>';
        }else{
            $output = '';
            $output .= '<br />'._('No Co-Workers found. To share this project with others').' <a href="/co_workers.php">'._('invite Co-Workers').'</a>';
        }
        */
        
        /*$q->query("select idcoworker from user_relations 
                    where iduser = ".$_SESSION["do_User"]->iduser." AND accepted = 'Yes'"); */
          
        $q->query("select user_relations.idcoworker,user.firstname,user.lastname from user_relations
                   INNER JOIN user on user.iduser = user_relations.idcoworker 
                   where user_relations.iduser = ".$_SESSION["do_User"]->iduser." AND user_relations.accepted = 'Yes'
                   order by user.firstname
                  ");
        $co_worker_not_in_project_array = array();
        if($q->getNumRows() > 0 ){
              //$do_user_data = new User();
              while($q->fetch()){
                  if($this->isProjectSharedToCoworker($q->getData("idcoworker")) === false ){
                        //$do_user_data->getId($q->getData("idcoworker"));
                        $data = array("iduser"=>$q->getData("idcoworker"),"firstname"=>$q->getData("firstname"),"lastname"=>$q->getData("lastname"));
                        $co_worker_not_in_project_array[] = $data ;
                  }
              }
              if(is_array($co_worker_not_in_project_array) && count($co_worker_not_in_project_array) > 0 ) {
                  $output = '';
                  $output .='<select name="fields[co_worker]">';
                  $output .='<option value="">'._('Select One').'</option>';
                  foreach($co_worker_not_in_project_array as $coworker){
                      $output .= '<option value="'.$coworker["iduser"].'">'.$coworker["firstname"].' '.$coworker["lastname"].'</option>';
                      //$output .= '<br />'.$coworker["firstname"];
                  }    
                  $output .= '</select><br /><br />';
                  $output .='<input value="'._('Add this Co-Worker').'" type="submit">';
                  $output .='<br /><br />'._('not in this list ? ').'<a href="/co_workers.php">'._('add Co-Workers').'</a>';
              }else{
                  $output = '';
                  $output .= '<br />'._('No Co-Workers found. To share this project with others').' <a href="/co_workers.php">'._('invite Co-Workers').'</a>';
              }
        }else{
              $output = '';
              $output .= '<br />'._('No Co-Workers found. To share this project with others').' <a href="/co_workers.php">'._('invite Co-Workers').'</a>';
        }
        
        return $output ;
    }
    
    /**
     *   Function to get the project Co-Workers and store them in an array.
     *   return false if no Co-Worker is found else returns the array with
     *   the Co-Worker ids.
    */

    function getProjectCoWorkersAsOwner(){
      $q = new sqlQuery($this->getDbCon());
      /*$q->query(" SELECT user.firstname as firstname,user.lastname as lastname, user.email as email,
                    user.iduser as iduser 
                    FROM user 
                      WHERE
                            user.iduser IN ( SELECT idcoworker FROM user_relations 
                                             WHERE 
                                                 iduser = ".$_SESSION["do_User"]->iduser." 
                                             AND accepted = 'Yes'
                                            ) 
                        AND user.iduser IN ( SELECT idcoworker FROM project_sharing 
                                             WHERE (iduser = ".$_SESSION["do_User"]->iduser.")
                                                AND idproject =". $this->idproject.") 
                      ORDER BY firstname");*/


     /* $q->query("SELECT idcoworker FROM project_sharing 
                                             WHERE (iduser = ".$_SESSION["do_User"]->iduser.")
                                                AND idproject =". $this->idproject); */

      $q->query("select project_sharing.idcoworker, user.firstname, user.lastname,user.email from project_sharing
                 INNER JOIN user on user.iduser = project_sharing.idcoworker
                 where project_sharing.iduser = ".$_SESSION["do_User"]->iduser." 
                 AND project_sharing.idproject =". $this->idproject." 
                 Order By user.firstname
                 "
                );

      if($q->getNumRows()){
          $array_coworker = array();
          $array_data = array();
          //$do_user_data = new User() ;
          while($q->fetch()){
              /*$array_coworker["idcoworker"] = $q->getData("iduser");
              $array_coworker["firstname"]= $q->getData("firstname");
              $array_coworker["lastname"]= $q->getData("lastname");
              $array_coworker["email"] = $q->getData("email");*/
              //$do_user_data->getId($q->getData("idcoworker"));
              $array_coworker["idcoworker"] = $q->getData("idcoworker");
              $array_coworker["firstname"]= $q->getData("firstname");
              $array_coworker["lastname"]= $q->getData("lastname");
              $array_coworker["email"] = $q->getData("email");
                
              $array_data[] = $array_coworker;
          }
          return $array_data;
      }else{
          return false;
      }
    }

    function getDistinctTaskCategoryForProject(){
        $q = new sqlQuery($this->getDbCon());
        $q->query("SELECT distinct(t.task_category) FROM task t inner join project_task as pt on t.idtask = pt.idtask
            WHERE pt.idproject = ".$this->idproject." ORDER BY t.task_category");
        if($q->getNumRows()){
              $data = array();
              $returned_array = array();
              while($q->fetch()){
                  if($q->getData("task_category") != ''){
                      $data["category"] = $q->getData("task_category");
                      $returned_array[] = $data;
                  }
              } 
              return $returned_array;
        }else{
            return false;
        }
    }

    function getDistinctTaskCategoryForProjectUnionUser(){
        $q = new sqlQuery($this->getDbCon());
        $q->query("SELECT distinct t.task_category as task_category 
            FROM task t inner join project_task as pt on t.idtask = pt.idtask
            WHERE 
            pt.idproject = ".$this->idproject." 
            Union
            SELECT distinct task_category  FROM task  where iduser = ".$_SESSION['do_User']->iduser."
            ORDER BY task_category");
        if($q->getNumRows()){
              $data = array();
              $returned_array = array();
              while($q->fetch()){
                  if($q->getData("task_category") != ''){
                      $data["category"] = $q->getData("task_category");
                      $returned_array[] = $data;
                  }
              } 
              return $returned_array;
        }else{
            return false;
        }
    }


    
    /**
      * This method has been depricated by getProjectCoWorkers due to performance issue 
      * @version 0.6.3
    */
    function getAllProjectParticipant(){
         $q = new sqlQuery($this->getDbCon());
            $idproject_owner = $this->getProjectOwner();
            if($idproject_owner != $_SESSION['do_User']->iduser){
                $q->query(" SELECT user.firstname as firstname,user.lastname as lastname, user.email as email,
                    user.iduser as iduser 
                        FROM user 
                        WHERE
                            user.iduser IN ( 
                                  SELECT idcoworker 
                                  FROM project_sharing 
                                  WHERE (iduser = ".$idproject_owner.")
                                     AND idproject =". $this->idproject."
                                       ) 
                            OR  user.iduser IN ( 
                                    SELECT distinct iduser 
                                    FROM project_sharing 
                                    WHERE ( iduser = ".$idproject_owner.")
                                        AND idproject =". $this->idproject."
                                            ) 
                            ORDER BY firstname");
            }else{
                    $q->query("( SELECT user.firstname as firstname,user.lastname as lastname, user.email as email,
                    user.iduser as iduser 
                    FROM user 
                      WHERE
                            user.iduser IN ( SELECT idcoworker FROM user_relations 
                                             WHERE 
                                                 iduser = ".$_SESSION["do_User"]->iduser." 
                                             AND accepted = 'Yes'
                                            ) 
                        AND user.iduser IN ( SELECT idcoworker FROM project_sharing 
                                             WHERE (iduser = ".$_SESSION["do_User"]->iduser.")
                                                AND idproject =". $this->idproject.") )
                         UNION
                         (SELECT user.firstname AS firstname, user.lastname AS lastname, user.email AS email, user.iduser AS iduser
                            FROM user
                          WHERE user.iduser =".$_SESSION["do_User"]->iduser.") 
                      ORDER BY firstname");
            }

      if($q->getNumRows()){
          $array_coworker = array();
          $array_data = array();
          while($q->fetch()){
              $array_coworker["idcoworker"] = $q->getData("iduser");
              $array_coworker["firstname"]= $q->getData("firstname");
              $array_coworker["lastname"]= $q->getData("lastname");
              $array_coworker["email"] = $q->getData("email");
              $array_data[] = $array_coworker;
          }
          return $array_data;
      }else{
          return false;
      }
    }

   function getProjectCoWorkers(){
      $q = new sqlQuery($this->getDbCon());
      //$idproject_owner = $this->getProjectOwner();
      /*$q->query(" SELECT user.firstname as firstname,user.lastname as lastname, user.email as email,
                    user.iduser as iduser 
                  FROM user 
                  WHERE
                        user.iduser IN ( 
                                  SELECT idcoworker 
                                  FROM project_sharing 
                                  WHERE (iduser = ".$idproject_owner.")
                                     AND idproject =". $this->idproject."
                                       ) 
                        OR  user.iduser IN ( 
                                    SELECT distinct iduser 
                                    FROM project_sharing 
                                    WHERE ( iduser = ".$idproject_owner.")
                                        AND idproject =". $this->idproject."
                                            ) 
                  ORDER BY firstname");

      if($q->getNumRows()){
          $array_coworker = array();
          $array_data = array();
          while($q->fetch()){
              $array_coworker["idcoworker"] = $q->getData("iduser");
              $array_coworker["firstname"]= $q->getData("firstname");
              $array_coworker["lastname"]= $q->getData("lastname");
              $array_coworker["email"] = $q->getData("email");
              $array_data[] = $array_coworker;
          }
          return $array_data;
      }else{
          return false;
      }*/

      $q->query("select * from project_sharing where idproject = ".$this->idproject );
      if($q->getNumRows() > 0 ){
            $project_worker_array = array();
            while($q->fetch()){
                    $project_worker_array[] = $q->getData("iduser");
                    $project_worker_array[] = $q->getData("idcoworker");
            }
            $project_worker_array = array_unique($project_worker_array);
            $array_coworker = array();
            $array_data = array();
            $do_user_data = new User();
            foreach($project_worker_array as $iduser){
              $do_user_data->getId($iduser);
              $array_coworker["idcoworker"] = $iduser;
              $array_coworker["firstname"]= $do_user_data->firstname;
              $array_coworker["lastname"]= $do_user_data->lastname;
              $array_coworker["email"] = $do_user_data->email;
              $array_data[] = $array_coworker;
            }
            usort($array_data,array($this, "sort_fname"));
            /*if(version_compare(PHP_VERSION,'5.3.0') >= 0){  
                usort($array_data, function($a, $b) {
                    return strcmp($a['firstname'], $b['firstname']);
                });
            }else{
                usort($array_data,array($this, "sort_fname"));
                
            }*/
            return $array_data;
      }else{ return false ; }

    }

    /**
      * Utility function for usort prior PHP_VERSION 5.3
    */
    function sort_fname($a, $b){
                    return strcmp($a['firstname'], $b['firstname']);
    }

    /**
     *   Event method to add a Co-Worker for a project
    */
    function eventShareProjects(EventControler $evtcl) {
      $idproject = $evtcl->idproject ;
      $fields = $evtcl->fields;
      $idcoworker =  $fields["co_worker"];
      if($idcoworker != ''){
          if(!$this->isProjectSharedToCoworker($idcoworker,$idproject)){
              $this->addProjectCoWorker($idcoworker,$idproject);
              $do_workfeedprojectassign= new WorkFeedProjectAssignCoworker();
              $do_workfeedprojectassign->eventAddFeed($idcoworker,$idproject);
              $message = _("Co-Worker is assigned successfuly for the project");
          }else{
              $message = _("Co-Worker is already assigned this project");
          }
          
      }else{
          $message = _("You must select a Co-Worker");
      }
      $goto = $evtcl->goto;
      $dispError = new Display($goto) ;
      $dispError->addParam("message", $message) ;
      $evtcl->setDisplayNext($dispError) ;
    }
    

    /**
      * Event method to remove a Co-Worker from a project  
    */

    function eventDelProjectCoWorker(EventControler $evtcl){
        $idcoworker = $evtcl->getParam("idcoworker");
        $idproject = $evtcl->idproject ;
        $q = new sqlQuery($this->getDbCon()) ;
        $q->query("delete from project_sharing where idproject = ".$idproject."
                   AND iduser = ".$_SESSION["do_User"]->iduser." AND idcoworker = ".$idcoworker." Limit 1");
        /* Once the Co-Worker is removed from the project assign all the open tasks for that user
           back to the owner
        */
        $do_project_task = new ProjectTask();
        $do_project_task->getAllProjectTasksForUser($idproject,$idcoworker);
        if($do_project_task->getNumRows()){
            $q = new sqlQuery($this->getDbCon()) ;
            while($do_project_task->next()){
              $q->query("update task set iduser = ".$_SESSION["do_User"]->iduser." where idtask = ".$do_project_task->idtask." Limit 1");
            }
        }
    }

    /**
      * Event method to remove a Co-Worker himself/herself from a project  (self removal from project)
    */

    function eventSelfDelProjectCoWorker(EventControler $evtcl){
        $idcoworker = $evtcl->getParam("idcoworker");
        $idproject = $evtcl->getParam("idproject");
        $iduser = $this->getProjectOwner($idproject);
        //echo '<br />Please dont delete me :( ';exit;
        $q = new sqlQuery($this->getDbCon()) ;
        $q->query("delete from project_sharing where idproject = ".$idproject."
                   AND iduser = ".$iduser." AND idcoworker = ".$idcoworker." Limit 1");

        /* Once the Co-Worker is removed from the project assign all the open tasks for that user
           back to the owner
        */
        $do_project_task = new ProjectTask();
        $do_project_task->getAllProjectTasksForUser($idproject,$idcoworker);
        if($do_project_task->getNumRows()){
            $q = new sqlQuery($this->getDbCon()) ;
            while($do_project_task->next()){
              $q->query("update task set iduser = ".$_SESSION["do_User"]->iduser." where idtask = ".$do_project_task->idtask." Limit 1");
            }
        }
    }

    /**
        Method to add a Co-Worker for a project
    */

    function addProjectCoWorker($idcoworker,$idproject=""){
	 if($idproject == ""){
	    $idproject = $this->idproject ;
	 }
         $q = new sqlQuery($this->getDbCon()) ;
         $qry_ins = "INSERT INTO project_sharing (idproject,iduser,idcoworker)
                        VALUES
                      (".$idproject.",".$_SESSION["do_User"]->iduser.",'$idcoworker')";

         $q->query($qry_ins); 
    }
    
    /**
      * Checks if a project is shared by some Co-Worker.
      * @param integer idcoworker and cheks if the project is shared
      * by the Co-Worker.
      * Returns true if shared else returns false.
    */

    function isProjectSharedToCoworker($idcoworker,$idproject=""){
       if($idproject == ""){
            $idproject = $this->idproject;
       }
       $q = new sqlQuery($this->getDbCon());
       $q->query("select * from project_sharing where iduser = ".$_SESSION["do_User"]->iduser."
                  AND idcoworker = ".$idcoworker." AND idproject = ".$idproject."
                ");
       if($q->getNumRows()){
          return true;
       }else{
          return false;
       }
    }


    /**
      * Get all the users particepating in the project
      * @param $idproject -- int
      * @return array with the owner and coworker if its shared else return false
    */
    function getAllUserFromProjectRel($idproject=""){
        if($idproject==""){ $idproject = $this->idproject;}
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from project_sharing where idproject = ".$idproject);
        if($q->getNumRows()){
            $user_array = array();
            while($q->fetch()){
               $user_array[] =$q->getData("iduser");
               $user_array[] =$q->getData("idcoworker");
            }
         return array_unique($user_array);
        }else{
            return false;
        }
    }

    /**
      * Function to check if the project is owned by the user
      * Returns true if yes else reurns false
      * @param integer $idproject
      * @param integer $iduser
      * @return boolean true/false
    */
    function isProjectOwner($idproject=0,$iduser=0){
       if (empty($idproject)) { $idproject = $this->idproject; }
       if (empty($iduser)) { $iduser = $_SESSION['do_User']->iduser; }
       $q = new sqlQuery($this->getDbCon());
       $q->query("select * from ".$this->table." where iduser = ".$iduser." AND 
                  idproject = ".$idproject);
       if($q->getNumRows()){
          return true;
       }else{
          return false;
       }
    }

    /**
      * Function to check if the the user is a Co-Worker of the project
      * @param idproject
      * @param idcoworker
      * Returns true if the user is a Co-Worker else returns false
    */

    function isProjectCoWorker($idproject=0,$idcoworker=0){
        if (empty($idproject)) { $idproject = $this->idproject; }
        if (empty($idcoworker)) { $idcoworker = $_SESSION["do_User"]->iduser; }
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from project_sharing where idcoworker = ".$idcoworker." AND idproject = ".$idproject);
        if($q->getNumRows()){
          return true;
       }else{
          return false;
       }
    }

    /**
      * Function to check if the project is a public project or not
      * returns true if the project is public else returns false
      * @param idproject
    */
    function isPublicProject($idproject = 0){
        if (empty($idproject)) { $idproject = $this->idproject; }
        $q = new sqlQuery($this->getDbCon());
        $q->query("select is_public from ".$this->table." where idproject = ".$idproject." ");
        $q->fetch();
        if($q->getData("is_public") == 1){
            return true;
        }else{
            return false;;
        }
    }

    /**
     * Return the id user of this current project
     * It will re-query the project table to get the user id.
     * @Note Need to check why no just to return $this->iduser ? and why not return a full user object ?
     * Note by PhL
     * @return integer with primary key value iduser for user table.
     */
    function getProjectOwner($idproject = ""){
        if($idproject == ""){$idproject = $this->idproject;}
        $q = new sqlQuery($this->getDbCon());
        $q->query("select iduser from ".$this->table." where ".$this->primary_key." = ".$idproject);
        $q->fetch();
        return $q->getData("iduser");
    }

    /**
      * Function to get the num of project shared between the owner and the Co-Worker
      * @param $idowner -- int
      * @param $idcoworker -- int
      * @return num of projects
    */
     function getNumProjectsShared($idowner,$idcoworker){
	$q = new sqlQuery($this->getDbCon());
	$q->query("select * from project_sharing where iduser = ".$idowner." AND idcoworker = ".$idcoworker);
	return $q->getNumRows();
     }
      

    /**
     * Receives project changes and writes them to the DB
     */
    function eventUpdateProject(EventControler $event_controler) {
        $q = new sqlQuery($this->getDbCon());
        /*$q->query("UPDATE project SET 
                    name = '{$event_controler->name}',
                    status = '{$event_controler->status}',
                    effort_estimated_hrs =  '{$event_controler->effort_estimated_hrs}'
                    WHERE idproject = {$event_controler->idproject}");*/

        $q->query("UPDATE project SET 
                    name = '{$event_controler->fields[name]}',
                    status = '{$event_controler->status}',
                    is_public = '{$event_controler->is_public}',
                    effort_estimated_hrs =  '{$event_controler->fields[effort_estimated_hrs]}',
                    idcompany = '{$event_controler->fields[idcompany]}'
                    WHERE idproject = {$event_controler->idproject}");

    }

	function getTotalNumProjectsForUser($iduser) {
		$q = new sqlQuery($this->getDbCon());
		$sql = "SELECT COUNT(idproject) AS total_projects 
				FROM `{$this->table}` 
				WHERE `iduser` = {$iduser}
			   ";
		$q->query($sql);
		if($q->getNumRows()) {
			$q->fetch();
			return $q->getData("total_projects");
		} else {
			return "0";
		}
	}

  /**
   * get user's projects
   * @return query object
   */
  function getUserProjects() {
    $sql = "SELECT *
    FROM `{$this->table}` 
    WHERE iduser = {$_SESSION['do_User']->iduser}
    ";
    $this->query($sql);
  }

  /**
   * Gets all the public projects.
   * @return Object : Query
   */
  function getAllPublicProjects() {
    $sql = "SELECT * 
            FROM ".$this->table." 
            WHERE `is_public` = 1
            ORDER BY `name`";
    $this->query($sql);
  }

  /**
   * Gets the total number of Public Projects in the Database.
   * @return int
   */
  function getTotalPublicProjects() {
    $sql = "SELECT `idproject` 
            FROM ".$this->table." 
            WHERE `is_public` = 1";
    $this->query($sql);
    return $this->getNumRows();
  }







      function getTaskCoWorkers(){
        $q = new sqlQuery($this->getDbCon());
          $passed_value=explode(',',$this->idproject);
          $array_size=count($passed_value);  
      
          $and_query="";
          if($array_size==1){
            $and_query.="idproject=".$passed_value[0];
          }else{
            foreach($passed_value as $p){
              $and_query.="( idproject =".$p.") and ";
            }
              $and_query= rtrim($and_query,' and ');
        }


    

  //echo "select * from project_sharing where ".$and_query;
            $q->query("select * from project_sharing where ".$and_query );
            if($q->getNumRows() > 0 ){
                  $project_worker_array = array();
                  while($q->fetch()){
                          $project_worker_array[] = $q->getData("iduser");
                          $project_worker_array[] = $q->getData("idcoworker");
                  }
                  $project_worker_array = array_unique($project_worker_array);
                  $array_coworker = array();
                  $array_data = array();
                  $do_user_data = new User();
                  foreach($project_worker_array as $iduser){
                    $do_user_data->getId($iduser);
                    $array_coworker["idcoworker"] = $iduser;
                    $array_coworker["firstname"]= $do_user_data->firstname;
                    $array_coworker["lastname"]= $do_user_data->lastname;
                    $array_coworker["email"] = $do_user_data->email;
                    $array_data[] = $array_coworker;
                  }
                  usort($array_data,array($this, "sort_fname"));
                 
                  return $array_data;
            }else{ return false ; }








}






}
?>
