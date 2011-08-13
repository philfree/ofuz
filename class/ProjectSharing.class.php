<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * ProjectSharing class
     * Using the DataObject
     * Share Project between iduser: project owner and coworkers.
     * @author Jay Link info@sqlfusion.com
     */

class ProjectSharing extends DataObject {
    public $table = 'project_sharing';
    protected $primary_key = 'idproject_sharing';

  /**
	 * getCoWorkers
	 * Get all the coworkers from a project
	 * @param project object
	 */
	 
  function getCoWorkers($do_project) {
    $this->query("SELECT idcoworker, iduser FROM project_sharing WHERE idproject = ".$do_project->idproject);
  }
	
  /** 
  * getCoWorkersAsArray();
  * Return an array with all the coworkers of the project passed in param.
  * @param object project 
  * @return Array of coworkers primarykey
  */
  function getCoWorkersAsArray($do_project) {
    $this->getCoWorkers($do_project);
    while ($this->next()) {
    $coworkers[] = $this->idcoworker;
    }
    return $coworkers;
  }

  function getAllUsersFromProjectRel($iduser = ""){
      $ret_data = array();
      if($iduser == "") $iduser = $_SESSION['do_User']->iduser;
      $this->query("select * from ".$this->table." where idcoworker = ".$iduser." OR iduser = ".$iduser);
      if($this->getNumRows()){
          while($this->next()){
              $data["iduser"] = $this->iduser;
              $data["idcoworker"] = $this->idcoworker;
              $ret_data[] = $data;
          }
          return $ret_data;
      }
  }
	
}
