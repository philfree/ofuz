<?php

/**
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class TimesheetBlockCoWorker extends BaseBlock{
    
      /**
        * processBlock() , This method must be added  
        * Required to set the Block Title and The Block Content Followed by displayBlock()
        * Must extend BaseBlock
      */

      function processBlock(){
        $this->setTitle(_('Hours By co-workers'));
        $this->setContent($this->generateTimesheetBlock());
        $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML
      */

      function generateTimesheetBlock(){
        
        $output = '';
        $do_project_block = new Project();
        $do_project_rel = new ProjectSharing();
        $all_users = $do_project_rel->getAllUsersFromProjectRel();
        $do_user_tab = new User();

        $users_array = array();
        if(is_array($all_users) && count($all_users) > 0 ){
            foreach($all_users as $users){
                $users_array[] = $users["iduser"];
                $users_array[] = $users["idcoworker"];
            }
           $users_array = array_unique($users_array);
           //$users_array = array_pop($users_array,$_SESSION['do_User']->iduser);

          $do_prob_dis_tab = new TimesheetBlockProjectDiscuss();
          $do_prob_dis_tab->getAllProjectHoursForCoWorker($users_array);
          
           if($do_prob_dis_tab->getNumRows() > 0 ){
                  $tot_hr = 0;
                  $used_project = array() ;
                  $result_array = array();
                  while($do_prob_dis_tab->next()){
                    if(!in_array($do_prob_dis_tab->idproject,$used_project)){
                        $user_data_array = array();
                        $used_project[] = $do_prob_dis_tab->idproject ;
                    }
                    //$data = array("project_name"=>$do_prob_dis_tab->name,"iduser"=>$do_prob_dis_tab->iduser,"hours"=>$do_prob_dis_tab->hours_work);
                    $data = array("hours"=>$do_prob_dis_tab->hours_work);
                    //$result_array[$do_prob_dis_tab->idproject][$do_prob_dis_tab->name][$do_prob_dis_tab->iduser][] = $data ;  
                    $result_array[$do_prob_dis_tab->iduser][] =  $data;
                  }
                  foreach($result_array as $key => $value){
                       $output .= '<hr>';
                       $output .= '<b>'.$do_user_tab->getFullName($key).'</b><br />' ;
                       $tot_hour = 0.00 ;
                       foreach($value as $hour){
                            $h= (float)$hour["hours"];
                            $tot_hour +=  $h;
                       }
                       $output .= $tot_hour.' hrs <br />' ;
                       $tot_hour = 0.00 ;
                  }
                  $output .= '<hr>';
                  return $output;
              }else { $this->setIsActive(false); }
              
          }else{ $this->setIsActive(false); }
        

      }

}


class TimesheetBlockProjectDiscuss extends ProjectDiscuss {

     function getAllProjectHoursForCoWorker($user_array){
            $do_prj_discuss_coworker = new ProjectDiscuss();
            $do_prj_discuss->report_month = $_SESSION['adm_project_report_discuss']->report_month;
            $do_prj_discuss->report_year = $_SESSION['adm_project_report_discuss']->report_year;
            $do_prj_discuss->week_start_date = $_SESSION['adm_project_report_discuss']->week_start_date;
            $do_prj_discuss->week_end_date = $_SESSION['adm_project_report_discuss']->week_end_date;  
            $users = implode(",",$user_array);  
             if($_SESSION['adm_project_report_discuss']->week_range != '' && $_SESSION['adm_project_report_discuss']->week_start_date != '' && $_SESSION['adm_project_report_discuss']->week_end_date != ''){

                $sql = "SELECT project.idproject, project.name, project_task.idproject_task, project_discuss.hours_work ,project_discuss.idproject_discuss, project_discuss.iduser, project_discuss.idproject_task,project_discuss.date_added
                        FROM project_task
                        LEFT JOIN project_discuss ON project_discuss.idproject_task = project_task.idproject_task
                        LEFT JOIN project ON project_task.idproject = project.idproject
                        LEFT JOIN project_sharing ON project_sharing.idproject = project.idproject
                        WHERE project_discuss.hours_work  > 0 AND
                        project_discuss.date_added between '".$_SESSION['adm_project_report_discuss']->week_start_date."' AND '".$_SESSION['adm_project_report_discuss']->week_end_date."'
                        AND project_discuss.iduser IN (".$users.")
                        AND project.idproject IS NOT NULL
                        AND project_task.idproject_task IS NOT NULL 
                        AND project_discuss.iduser IS NOT NULL 
                        group by project_discuss.idproject_discuss
                        Order By project.idproject, project.name";
              }else{
                $sql = "SELECT project.idproject, project.name, project_task.idproject_task, project_discuss.hours_work,project_discuss.idproject_discuss, project_discuss.iduser, project_discuss.idproject_task,project_discuss.date_added
                        FROM project_task
                        LEFT JOIN project_discuss ON project_discuss.idproject_task = project_task.idproject_task
                        LEFT JOIN project ON project_task.idproject = project.idproject
                        LEFT JOIN project_sharing ON project_sharing.idproject = project.idproject
                        WHERE project_discuss.hours_work  >0 
                        AND project_discuss.date_added like '%".$_SESSION['adm_project_report_discuss']->formatSearchMonth($_SESSION['adm_project_report_discuss']->report_month)."%'
                        AND project_discuss.iduser IN (".$users.")
                        AND ( project.idproject IS NOT NULL AND project.idproject <> '')
                        AND ( project_task.idproject_task IS NOT NULL  AND  project_task.idproject_task <> '' )
                        AND ( project_discuss.iduser IS NOT NULL  AND project_discuss.iduser <> '' )
                        group by project_discuss.idproject_discuss
                        Order By project.idproject, project.name";
                //echo $sql; 
                  //AND project_discuss.date_added like '%".$_SESSION['adm_project_report_discuss']->formatSearchMonth($_SESSION['adm_project_report_discuss']->report_month)."%'
                 //AND project_discuss.iduser <> ".$_SESSION['do_User']->iduser."
                
              }
             $this->query($sql);
      }

}

?>
