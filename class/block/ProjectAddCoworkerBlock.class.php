<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class ProjectAddCoworkerBlock extends BaseBlock{

      public $short_description = 'Add co-worker for project block';
      public $long_description = 'For project owners to add co-workers for the project';
    
      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extend BaseBlock
      */

      function processBlock(){

	  if($_SESSION["do_project"]->isProjectOwner()){
	    $this->setTitle(_('Add a Co-Worker'));
	  } else {
	    $this->setTitle(_('Co-Workers'));
	  }
	  
	  $this->setContent($this->generateAddCoworkerBlock());
	  $this->displayBlock();

      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML form
       * @see class/UserRelations.class.php
       * @see class/Project.class.php
      */

      function generateAddCoworkerBlock(){
        $output = '';
        
        $output .= '<div class="percent95">';

        $isProjectOwner = false;

        if($_SESSION["do_project"]->isProjectOwner()){
              $isProjectOwner = true;
        }
            /* Adding Co-Workers are allowed only if the project owner is
               the user
             */
        if($isProjectOwner) {
            $do_user_rel = new UserRelations();
            $do_user_rel->getAllCoWorker();
            $co_worker_added_in_ofuz = false;
            if($do_user_rel->getNumRows()){
              $co_worker_added_in_ofuz = true;
            }
            if($co_worker_added_in_ofuz){
                $e_share_project = new Event("do_project->eventShareProjects");
                $e_share_project->addParam("goto", "Project/".$_SESSION["do_project"]->idproject);
                $e_share_project->addParam("idproject", $_SESSION["do_project"]->idproject);
                $output .= $e_share_project->getFormHeader();
                $output .= $e_share_project->getFormEvent();
                $output .= $_SESSION["do_project"]->addProjectCoWorkerForm();
                $output .= $e_share_project->getFormFooter();
                $co_workers = $_SESSION["do_project"]->getProjectCoWorkersAsOwner();
                if(!$co_workers){
                  //echo '<br />';
                  //echo _('No Co-Workers added for this project');
                }else{
                  if(is_array($co_workers)){
                    $output .= '<br /><br /><b>';
                    $output .=  _('Participating Co-Workers:');
                    $output .= '</b><br/>';
                    foreach($co_workers as $co_workers){
                        $e_del_coworker = new Event("do_project->eventDelProjectCoWorker");
                        $e_del_coworker->addParam("goto","Project/".$_SESSION["do_project"]->idproject);
                        $e_del_coworker->addParam("idproject",$_SESSION["do_project"]->idproject);
                        $e_del_coworker->addParam("idcoworker",$co_workers["idcoworker"]);
                                  $output .= '<div id="templt'.$count.'" class="co_worker_item co_worker_desc">'; 
                                  $output .= '<div style="position: relative;">';  
                                  $output .= $co_workers["firstname"].' '.$co_workers["lastname"];
                                  $img_del = '<img class="delete_icon_tag" border="0" width="14px" height="14px" src="/images/delete.gif">';
                                  $output .= '<div width="15px" id="trashcan'.$count.'" class="deletenote" style="right:0;">'.$e_del_coworker->getLink($img_del, ' title="'._('Remove').'"').'</div>';
                                  $output .= '</div></div>';

                    }
            
                  }
                }
              }else{
                $output .= _('If you want to share this project with others, add Co-Workers to your Ofuz account');
                $output .= '<br />';
                $output .= '<a href="/co_workers.php">';
                $output .= _('add Co-Workers');
                $output .= '</a>';
              }
          }else{
                $co_workers = $_SESSION["do_project"]->getProjectCoWorkers($_SESSION["do_project"]->idproject);
                if(is_array($co_workers)){
                    foreach($co_workers as $co_workers){
                        if($co_workers["idcoworker"] == $_SESSION["do_User"]->iduser) {
                            $e_del_coworker = new Event("do_project->eventSelfDelProjectCoWorker");
                            $e_del_coworker->addParam("goto","Project/".$_SESSION["do_project"]->idproject);
                            $e_del_coworker->addParam("idcoworker",$co_workers["idcoworker"]);
                            $e_del_coworker->addParam("idproject",$_SESSION["do_project"]->idproject);
                            $output .= '<div id="templt'.$count.'" class="co_worker_item co_worker_desc">'; 
                            $output .= '<div style="position: relative;">';  
                            $output .= $co_workers["firstname"].' '.$co_workers["lastname"];
                            $img_del = '<img class="delete_icon_tag" border="0" width="14px" height="14px" src="/images/delete.gif">';
                            $output .= '<div width="15px" id="trashcan'.$count.'" class="deletenote" style="right:0;">'.$e_del_coworker->getLink($img_del, ' title="'._('Remove').'"').'</div>';
                            $output .= '</div></div>';
                        } else {
                            $output .= $co_workers["firstname"].' '.$co_workers["lastname"].'<br />';
                        }
                      }
                  }
          }

        $output .= '</div>';

        return $output;

      }

      

      
}

?>
