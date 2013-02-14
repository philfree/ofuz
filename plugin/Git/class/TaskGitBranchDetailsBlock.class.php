<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class TaskGitBranchDetailsBlock extends BaseBlock{
    
  public $short_description = 'Git Branch Details';
  public $long_description = 'Git Branch details for the current task';

      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extend BaseBlock
      */

      function processBlock(){
	  $this->setTitle(_('Git Branch Details'));
	  $this->setContent($this->generateGitBranchBlock());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML
      */

      function generateGitBranchBlock(){

	    $output = '';
	    
	    $do_user_git = new UserGitrepo();
	    $git_repo = $do_user_git->CheckGitProjectExist($_SESSION["do_project"]->idproject);
	    
	    if(is_array($git_repo)){
			include_once('plugin/Git/class/Git.class.php');
			
			$repo_path = "plugin/Git/repos/";
			
			
			//$output .= '<br />';
			$e_del_gitrepo = new Event("UserGitrepo->eventSelfDelProjectGitRepo");
			$e_del_gitrepo->addParam("goto","Project/".$_SESSION["do_project"]->idproject);
			$e_del_gitrepo->addParam("idgit_project",$git_repo["idgit_project"]);
			$output .= '<div id="templt" class="co_worker_item co_worker_desc">'; 
			$output .= '<div style="position: relative;">';  
			//$output .= '<b>Repository Name : '.$git_repo['git_repo'].'<br /></b>';
			$idproject_task = (int)$_GET['idprojecttask'];
			$repo_name = $git_repo['git_repo'];
			$folder = "$repo_path"."$repo_name";
			if(is_dir($folder)){
				$repo = Git::open($folder);
				$branch_name = $repo->branchlist($idproject_task);
				
				$branch_name = split('	',$branch_name);
				//echo'<pre>';print_r($branch_name);echo'</pre>';
				$size = sizeof($branch_name);
				for($i=1;$i<$size;$i++){
					$branch_title = split('/',$branch_name[$i]);
					//echo'<pre>';print_r($branch_title);echo'</pre>';
					$b_size = sizeof($branch_title);
					$b_name = split(' ',$branch_title[$b_size-1]);
					$br_name .= $b_name[0].'<br />';
					
				}
				
				if(!empty($br_name)){
				$output .= _('Currently The Following Git branches are associated with this Task <br />');
				//$output .= '<br />Branches assoicated with current task are :<br />';	
				$output .= '<b>'.$br_name.'</b>';
				} else {
					$output .= _('No Git branches found which are associated with this Task <br />');
				}
			} else {
				$output .= _('Invalid Respository, Missing git repository in the plugin/Git/repos/'.$repo_name.', Please check and try again <br />');
				
			}
			$output .= '</div></div>';
			
		} else {
			
			$output .='No Git Repository is associated with this Project Task';
		}		
		
		
	    return $output;

      }

      

      
}

?>

