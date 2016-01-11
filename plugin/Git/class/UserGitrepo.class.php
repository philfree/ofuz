<?php

class UserGitrepo extends DataObject {
	
	public $table = 'user_gitrepo';
    protected $primary_key = 'iduser_gitrepo';


	/**
	 * Function Getall Git Repo for particular user
	 * @param iduser
	 * @return query object
	 **/

	 function GetUserGitRepo($iduser){
		 
		$q = new sqlQuery($this->getDbCon());
		$q->query("Select git_repo,git_repourl from ".$this->table. "
					  where iduser = ".$iduser."");
		if($q->getNumRows() >= 1){
			$data = array();
			 while($q->fetch()){
				  $data[$q->getData("git_repo")] = $q->getData("git_repourl");
				  //$data["git_repourl"][$i] = $q->getData("git_repourl");   
				  $i++;
			   }
			   return $data;
		  }else{
			  return false;
		  }
	 }


	/**
	 * Function to get all the repository name for the dropbox
	 * @param iduser
	 * @array list
	 **/
	 function GetAllGitRepositoryForUser($iduser){
		 $q = new sqlQuery($this->getDbCon());
		 $q->query("Select iduser_gitrepo,git_repo from ".$this->table. "
					  where iduser = ".$iduser."");
		if($q->getNumRows() >= 1){
			$data = '';
			$data = '<select name="iduser_gitrepo" name="iduser_gitrepo">';
			 while($q->fetch()){
				 $data .= '<option value='.$q->getData("iduser_gitrepo").'>'.$q->getData("git_repo").'</option>';
			  }
			  return $data;
		  }else{
			  return false;
		  }
		 
	 }
 
 
	/**
	 * FUnction to add git repository for the user
	 * @event object
	 * @return msg
	**/
	function eventAddGitRepo(EventControler $evtcl){
		//$msg = '';
		
		$goto = $evtcl->goto;
		//check if the below is already exist
		//if(!$this->checkIfGitRepositoryExist($evtcl->repo_name, $evtcl->repo_url,$evtcl->iduser)) {
		  if(!$this->checkIfGitRepositoryExist($evtcl->repo_url,$evtcl->iduser)) {	
			//echo $evtcl->repo_name.",".$evtcl->repo_url."--".$evtcl->iduser;die();
			
			$repo_name1 = split('/',$evtcl->repo_url);
			$size = sizeof($repo_name1);
			$repo_name = split('\.git',$repo_name1[$size-1]);
			//$path = getcwd()."/plugin/Git/";
			$path = "plugin/Git/repos/";
			$path .= $repo_name[0];
			//echo $path;die();
			if(is_dir($path)){
			
				$sql = "INSERT INTO user_gitrepo(`iduser`,`git_repo`,`git_repourl`)
				  VALUES(".$evtcl->iduser.",'".$repo_name[0]."','".$evtcl->repo_url."')";
				$this->query($sql);

				//echo "git clone ".$evtcl->repo_url."";die();
				
				//echo "git clone ".$evtcl->repo_url."  $path".$evtcl->repo_name."";die();
				
				//system("git clone ".$evtcl->repo_url."  $path".$evtcl->repo_name."");
				
				$_SESSION['msg'] = "New Git Repository has been added successfully.";
			} else {
				$_SESSION['msg'] = 'Need do <b>git clone '.$evtcl->repo_url.'</b> inside /plugin/Git/repos/ folder before adding the Git Repository<br /><br />';
			}
		}else{
			$_SESSION['msg'] = "Duplicate Entry, Git Repository Already Exist Please check Repository Name or URL to avoid the duplicates.";
		}	
		//echo $msg;	
		$evtcl->setDisplayNext(new Display($goto));
	} 

	/**
	 * Function to check if already repository is exist or not
	 * @param repo_name
	 * @param repo_url
	 * @return true or flase
	 **/

	function checkIfGitRepositoryExist($repo_url,$iduser){
		
		$q = new sqlQuery($this->getDbCon());
		/*$q->query("Select * from ".$this->table. "
					  where iduser = '".$iduser."' and (git_repo = '".$repo_name."' or git_repourl = '".$repo_url."')");*/
		$q->query("Select * from ".$this->table. "
					  where iduser = '".$iduser."' and git_repourl = '".$repo_url."'");			  
		if($q->getNumRows() >= 1){
			return true;
		} else {
			return false;
		}
		
	}

	/**
	 * Function to add git repo for the project
	 * @param eventcontroller 
	**/
	function eventAddProjectGitRepo(EventControler $evtcl){
		
		$goto = $evtcl->goto;
		
		$q = new sqlQuery($this->getDbCon());
		$q->query("Insert Into git_project (iduser_gitrepo,idproject) values('".$evtcl->iduser_gitrepo."','".$evtcl->idproject."')");
		$evtcl->setDisplayNext(new Display($goto));
		
	} 

	/**
	 * Function to check the project is already associated with git repository
	 * @param idproject
	 * @return Git Repository Name
	**/
	function CheckGitProjectExist($idproject){
		$q = new sqlQuery($this->getDbCon());
		$q->query("SELECT u.git_repo as git_repo,g.idgit_project as idgit_project from git_project g inner join user_gitrepo u on g.iduser_gitrepo=u.iduser_gitrepo where g.idproject='".$idproject."'");
		if($q->getNumRows() >= 1){
			$data = array();
			while($q->fetch()){
				$data['git_repo'] = $q->getData('git_repo');
				$data['idgit_project'] = $q->getData('idgit_project');
			}
			return $data;
		} else {
			return false;
		}
	}

	/**
	 * Function to delete the git repository which is selected for the current project
	 * @param Eventcontroller 
	**/
	function eventSelfDelProjectGitRepo(Eventcontroler $evtcl){
		$goto = $evtcl->goto;
		$q = new sqlQuery($this->getDbCon());
		$q->query("delete from git_project where idgit_project='".$evtcl->idgit_project."' limit 1");
		$evtcl->setDisplayNext(new Display($goto));
	} 
}