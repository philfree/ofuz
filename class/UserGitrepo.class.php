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
        $data = array();$i=1;
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
 * FUnction to add git repository for the user
 * @event object
 * @return msg
**/
function eventAddGitRepo(EventControler $evtcl){
	//$msg = '';
	
	$goto = $evtcl->goto;
	//check if the below is already exist
	if(!$this->checkIfGitRepositoryExist($evtcl->repo_name, $evtcl->repo_url,$evtcl->iduser)) {
		//echo $evtcl->repo_name.",".$evtcl->repo_url."--".$evtcl->iduser;die();

		$sql = "INSERT INTO user_gitrepo(`iduser`,`git_repo`,`git_repourl`)
	      VALUES(".$evtcl->iduser.",'".$evtcl->repo_name."','".$evtcl->repo_url."')";
		$this->query($sql);

		//echo "git clone ".$evtcl->repo_url."";die();
		$path = getcwd()."/plugin/Git/";
		//echo "git clone ".$evtcl->repo_url."  $path".$evtcl->repo_name."";die();
		
		system("git clone ".$evtcl->repo_url."  $path".$evtcl->repo_name."");
		
		$_SESSION['msg'] = "New Git Repository has been added successfully.";
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

function checkIfGitRepositoryExist($repo_name,$repo_url,$iduser){
	
	$q = new sqlQuery($this->getDbCon());
    $q->query("Select * from ".$this->table. "
                  where iduser = '".$iduser."' and (git_repo = '".$repo_name."' or git_repourl = '".$repo_url."')");
    if($q->getNumRows() >= 1){
		return true;
	} else {
		return false;
	}
	
}

}
?>
