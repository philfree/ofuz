<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class ProjectGitRepositoryAddBlock extends BaseBlock{
    
  public $short_description = 'Add project git repository block';
  public $long_description = 'Git repository for the project';

      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extend BaseBlock
      */

      function processBlock(){
	  $this->setTitle(_('Add Git Repository'));
	  $this->setContent($this->generateAddGitRepositoryBlock());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML
      */

      function generateAddGitRepositoryBlock(){

	    $output = '';
	    $data = Array();
	    $do_user_git = new UserGitrepo();
	    $git_repo = $do_user_git->CheckGitProjectExist($_SESSION["do_project"]->idproject);
	    
	    if(!is_array($git_repo)){
			$data = $do_user_git->GetAllGitRepositoryForUser($_SESSION["do_User"]->iduser);
			
			if($data != ''){
				$output .= _('Select the repository name from the list to add a git repository for this project.');
				$output .= '<br />';
				$e_git_repo = new Event("UserGitrepo->eventAddProjectGitRepo");
				$e_git_repo->addParam("goto", "Project/".$_SESSION["do_project"]->idproject);
				$e_git_repo->addParam("idproject", $_SESSION["do_project"]->idproject);
				$output .= $e_git_repo->getFormHeader();
				$output .= $e_git_repo->getFormEvent();
				$output .= $data;
				$output .= $e_git_repo->getFormFooter('Add this Git Repository');
			} else {
				$path  = $_SERVER['SERVER_NAME'].'/Setting/Git/git_repo';
				$output .= _('If you want to share git project repository, add Git Repository to your Ofuz account');
				$output .= '<br /><br />';
				$output .= '<a href="/Setting/Git/git_repo">';
				$output .= _('Add Git-Repository');
				$output .= '</a>';
			}
			
		} else {
			
			$output .= _('Currently The Following Git Repository is associated with this Project');
			$output .= '<br /><br />';
			$e_del_gitrepo = new Event("UserGitrepo->eventSelfDelProjectGitRepo");
			$e_del_gitrepo->addParam("goto","Project/".$_SESSION["do_project"]->idproject);
			$e_del_gitrepo->addParam("idgit_project",$git_repo["idgit_project"]);
			$output .= '<div id="templt" class="co_worker_item co_worker_desc">'; 
			$output .= '<div style="position: relative;">';  
			$output .= '<b>'.$git_repo['git_repo'].'</b>';
			$img_del = '<img class="delete_icon_tag" border="0" width="14px" height="14px" src="/images/delete.gif">';
			$output .= '<div width="15px" id="trashcan" class="deletenote" style="right:0;">'.$e_del_gitrepo->getLink($img_del, ' title="'._('Remove').'"').'</div>';
			$output .= '</div></div>';
			
		}
		
		
		
	    return $output;

      }

      

      
}

?>

