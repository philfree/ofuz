<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

class OfuzList extends BaseObject {
  
  public $is_sortable = false ; 
  public $is_multiselect = false ;
  public $data_list = array();
  public $obj = '';
  public $data;
  public $uniq_key;

  public function __construct($obj_name) {
 
 if(is_object($obj_name)){
     $this->obj = $obj_name ;
            $this->data = $obj_name;
 }
  }

  /**
    * Function set sortable for the  list
    * @param $boolean - Boolean
  */
  public function setSortable($boolean){
      $this->is_sortable = $boolean ;
  }

  /**
    * Function returns boolean is_sortable
  */
  public function getSortable(){
      return $this->is_sortable ;
  }

  /**
    * Function set multiselect for the list
    * @param $boolean - Boolean
  */
  public function setMultiSelect($boolean){
    $this->is_multiselect = $boolean ;
  }

  /**
    * Function return boolean is_multiselect
  */
  public function getMultiSelect(){
    return $this->is_multiselect ;
  }

  function getDataList(){
 return $this->data_list ;
  }

  function setObject($display_object){
 $this->display_object = $display_object ;
  }

  function getDisplayObject(){
      return $this->display_object ;
  }
  function getUniqRow() {
     return $this->data->getData($this->uniq_key);
  }

  /**
    * Display method for the list
  */

  public function displayList(){
      $class_name = get_class($this->obj);

      if ($class_name == 'ProjectTask') {echo '<ul id="project_tasks">',"\n"; }
      while($this->obj->next()){
          echo $this->getRowStart(); 
          $this->displayRow($class_name) ;
          echo $this->getRowEnd();
      }
      if ($class_name == 'ProjectTask' || $class_name='Task') { echo '</ul>',"\n"; }
  
  }
  
  public function getRowStart() {
    $html = '';
    if (!empty($this->uniq_key)) {
     if ($this->getMultiSelect()) {
         $html .= '<div class="ofuz_list_contact" id="cid'.$this->getUniqRow().'" onclick="fnHighlight(\''.$this->getUniqRow().'\')"><table><tr><td><input type="checkbox" name="ck[]" id="ck'.$this->getUniqRow().'" value="'.$this->getUniqRow().'" class="ofuz_list_checkbox" onclick="fnHighlight(\''.$this->getUniqRow().'\')" /></td>';
     } else { $html .= '<div class="ofuz_list_contact" <table><tr>'; }
    }
    return $html;
  }
  public function getRowEnd() { 
    $html = '';
    if (!empty($this->uniq_key)) {
     if ($this->getMultiSelect()) {
            $html .= '</tr></table></div>';
            $html .= '<div class="spacerblock_2"></div><div class="solidline"></div><div id="'.$this->getUniqRow().'" class="message_box"></div>';
        } else { 
           $html .= '</tr></table></div>';
           $html .= '<div class="spacerblock_2"></div><div class="solidline"></div>';
        }
    }
    return $html;
}
  /**
  */
  public function displayRow($class_name){
   $html = '';
    switch($class_name){
     case 'ProjectTask':
            $contact_full_name = '';
            if($this->obj->idcontact) {
                $do_contact = new Contact();
                $contact_full_name = ' ('.$do_contact->getContactName($this->obj->idcontact).')';
            }
            $progress_pixels = $this->obj->progress;
            
            if (!is_numeric($progress_pixels) || $progress_pixels < 0 || $progress_pixels > 100) $progress_pixels = '0';
            if(empty($this->obj->due_date_dateformat) || $this->obj->due_date_dateformat == '' || $this->obj->due_date_dateformat == '0000-00-00'){
                $bg_color = 'style = "background-color:#ffffff;"';
                $color = "#ffffff" ;
                $change_to = "#ffffdd";
                $ddtasks = "ddtasks";
                
            }elseif(strtotime($this->obj->due_date_dateformat) == strtotime(date("Y-m-d"))){
                $bg_color = 'style = "background-color:#b8eacc;"';
                $color = "#b8eacc" ;
                $change_to = "#b8eaaa";
                $ddtasks = "ddtasks_today";
            }elseif(strtotime($this->obj->due_date_dateformat) < strtotime(date("Y-m-d")) ){
                $bg_color = 'style = "background-color:#ffe9ce;"';
                $color = "#ffe9ce" ;
                $change_to = "#ffe9ad";
                $ddtasks = "ddtasks_overdue";
            }else{
                $bg_color = 'style = "background-color:#ffffff;"';
                $color = "#ffffff" ;
                $change_to = "#ffffdd";
                $ddtasks = "ddtasks";
            }
                $strike_class = '';
                if($this->obj->status == 'closed'){
                    $strike_class = ' class="ptask_closed"';
                    $bg_color = 'style = "background-color:#ffffff;"';
                    $color = "#ffffff" ;
                    $change_to = "#ffffdd";
                    $ddtasks = "ddtasks";
                }
            //$task_class = 'ptask_name';    
            //$ddtask_ul = 'ddtasks';
            if($this->obj->access == 'Public'){
                $html .= '<li id="pt_'.$this->obj->idtask.'" class="ddtasks">'.
                            '<div class="ptask_name" onclick = "" id="list'.$this->obj->idtask.'" >
                                <span class="task_category">'.$this->obj->task_category.'</span>&nbsp;
                                <span'.$strike_class.'><a href="/PublicTask/'.$this->obj->idproject_task.'" >'.$this->obj->task_description.'</a>'.$contact_full_name.'</span>
                              </div>'.
                            '<div class="ptask_progbar1">';
            }else{
                //$task_class = 'ptask_name';      
                if($this->getMultiSelect() === true ){
                    $html .= '<li id="pt_'.$this->obj->idtask.'" class="'.$ddtasks.'" '.$bg_color.'>
                              <div class="ptask_name" onclick="fnHighlight(\''.$this->obj->idtask.'\',\''.$color.'\',\''.$change_to.'\')" id="list'.$this->obj->idtask.'"> 
                                  <span><input type="checkbox" name="ck[]" id="ck'.$this->obj->idtask.'" value="'.$this->obj->idtask.'" class="ofuz_list_checkbox" onclick="fnHighlight(\''.$this->obj->idtask.'\',\''.$color.'\',\''.$change_to.'\')" /></span>
                                  <span class="task_category">'.$this->obj->task_category.'</span>&nbsp;
                                  <span'.$strike_class.'><a href="/Task/'.$this->obj->idproject_task.'" onclick="allowHighlight=false;" >'.$this->obj->task_description.'</a>'.$contact_full_name.'</span>
                              </div><div class="ptask_progbar1">';
                }else{
                    $html .= '<li id="pt_'.$this->obj->idtask.'" class="'.$ddtasks.'">
                              <div class="'.$task_class.'" id="list'.$this->obj->idtask.'"> 
                                  <span class="task_category">'.$this->obj->task_category.'</span>&nbsp;
                                  <span'.$strike_class.'><a href="/Task/'.$this->obj->idproject_task.'" onclick="allowHighlight=false;" >'.$this->obj->task_description.'</a>'.$contact_full_name.'</span>
                              </div><div class="ptask_progbar1">';
                }
            }
            if ($this->obj->status == 'closed') {
                $html .= _('closed').'<div class="ptask_progbar3" style="width: 100px;"></div></div>'."\n";
            } else {
                $html .= _('progress').'<div class="ptask_progbar2" style="width: '.$progress_pixels.'px;"></div></div>'."\n";
            }
            if($this->obj->access != 'Public'){
                $html .= '<div class="ptask_handle"></div>';
            }
            $html .= '</li>'."\n";
      break;
        case 'Contact':
            $html .= '<div class="ofuz_list_contact" id="cid'.$this->obj->idcontact.'" onclick="fnHighlight(\''.$this->obj->idcontact.'\')"><table><tr>';
            $html .= '<td class="ofuz_list_contact_col1">
                          <input type="checkbox" name="ck[]" id="ck'.$this->obj->idcontact.'" value="'.$this->obj->idcontact.'" class="ofuz_list_checkbox" onclick="fnHighlight(\''.$this->obj->idcontact.'\')" />
                          <img src="'.$this->obj->getContactPicture($this->obj->idcontact).'" width="34" alt="" />
                      </td>';
            $html .= '<td class="ofuz_list_contact_col2">
                          <span class="contact_name"><a href="/Contact/'.$this->obj->idcontact.'" onclick="allowHighlight=false;">'.$this->obj->firstname.'&nbsp;'.$this->obj->lastname.'</a></span>';
                          if (strlen($this->obj->position)>0) {
                              $e_detail_com = new Event('mydb.gotoPage');
                              $e_detail_com->addParam('goto', 'company.php');
                              $e_detail_com->addParam('idcompany',$this->obj->idcompany);
                              $e_detail_com->addParam('tablename', 'company');
                              $e_detail_com->requestSave('eDetail_company', $_SERVER['PHP_SELF']);
                              $companyURL = $e_detail_com->getUrl();
                              $html .= '<div class="contact_position"><i>'.$this->obj->position.'</i> '._('at').' <a href="'.$companyURL.'" onclick="allowHighlight=false;">'.$this->obj->company.'</a></div>';
                          }
                          $html .= '</td>';
            $html .= '<td class="ofuz_list_contact_col3">
                          '.$this->obj->phone_number.'<br />
                          <a href="mailto:'.$this->obj->email_address.'">'.$this->obj->email_address.'</a><br />
                          <i>'.str_replace(",", ", ", $this->obj->tags).'</i>
                      </td>';
            $html .= '</tr></table></div>';
            $html .= '<div class="spacerblock_2"></div><div class="solidline"></div><div id="'.$this->obj->idcontact.'" class="message_box"></div>';
            break;
          
          case 'Task':
            $contact_full_name = '';
            if($this->obj->idcontact) {
                $do_contact = new Contact();
                $contact_full_name = ' ('.$do_contact->getContactName($this->obj->idcontact).')';
            }
            $progress_pixels = $this->obj->progress;
            
            if (!is_numeric($progress_pixels) || $progress_pixels < 0 || $progress_pixels > 100) $progress_pixels = '0';
            if(empty($this->obj->due_date_dateformat) || $this->obj->due_date_dateformat == '' || $this->obj->due_date_dateformat == '0000-00-00'){
                $bg_color = 'style = "background-color:#ffffff;"';
                $color = "#ffffff" ;
                $change_to = "#ffffdd";
                $ddtasks = "ddtasks";
                
            }elseif(strtotime($this->obj->due_date_dateformat) == strtotime(date("Y-m-d"))){
                $bg_color = 'style = "background-color:#b8eacc;"';
                $color = "#b8eacc" ;
                $change_to = "#b8eaaa";
                $ddtasks = "ddtasks_today";
            }elseif(strtotime($this->obj->due_date_dateformat) < strtotime(date("Y-m-d")) ){
                $bg_color = 'style = "background-color:#ffe9ce;"';
                $color = "#ffe9ce" ;
                $change_to = "#ffe9ad";
                $ddtasks = "ddtasks_overdue";
            }else{
                $bg_color = 'style = "background-color:#ffffff;"';
                $color = "#ffffff" ;
                $change_to = "#ffffdd";
                $ddtasks = "ddtasks";
            }
                $strike_class = '';
                if($this->obj->status == 'closed'){
                    $strike_class = ' class="ptask_closed"';
                    $bg_color = 'style = "background-color:#ffffff;"';
                    $color = "#ffffff" ;
                    $change_to = "#ffffdd";
                    $ddtasks = "ddtasks";
                }
            //$task_class = 'ptask_name';    
            //$ddtask_ul = 'ddtasks';

                 $html .='<ul id="project_tasks">';

            if($this->obj->access == 'Public'){
                $html .= '<li id="pt_'.$this->obj->idtask.'" class="ddtasks">'.
                            '<div class="ptask_name" onclick = "" id="list'.$this->obj->idtask.'" >
                                <span class="task_category">'.$this->obj->task_category.'</span>&nbsp;
                                <span'.$strike_class.'><a href="/PublicTask/'.$this->obj->idproject_task.'" >'.$this->obj->task_description.'</a>'.$contact_full_name.'</span>
                              </div>'.
                            '<div class="ptask_progbar1">';
            }else{
                //$task_class = 'ptask_name';      
                if($this->getMultiSelect() === true ){
                    /*$html .= '<li id="pt_'.$this->obj->idtask.'" class="'.$ddtasks.'" '.$bg_color.'>
                              <div class="ptask_name" onclick="fnHighlight(\''.$this->obj->idtask.'\',\''.$color.'\',\''.$change_to.'\')" id="list'.$this->obj->idtask.'"> 
                                  <span><input type="checkbox" name="ck[]" id="ck'.$this->obj->idtask.'" value="'.$this->obj->idtask.'" class="ofuz_list_checkbox" onclick="fnHighlight(\''.$this->obj->idtask.'\',\''.$color.'\',\''.$change_to.'\')" />
                                  <input type="hidden" name="prid[]" id="prid" value="'.$this->obj->idproject.'"/></span>
                                  <span class="task_category">'.$this->obj->task_category.'</span>&nbsp;
                                  <span'.$strike_class.'><a href="/Task/'.$this->obj->idproject_task.'" onclick="allowHighlight=false;" >'.$this->obj->task_description.'</a>'.$contact_full_name.'</span>*/

                                

                     $html .= '<li id="pt_'.$this->obj->idtask.'" class="'.$ddtasks.'" '.$bg_color.'>
                              <div class="ptask_name" onclick="fnHighlight(\''.$this->obj->idtask.'\',\''.$color.'\',\''.$change_to.'\')" id="list'.$this->obj->idtask.'"> 
                                  <span><input type="checkbox" name="ck[]" id="ck'.$this->obj->idtask.'" value="'.$this->obj->idtask.'-'.$this->obj->idproject.'" class="ofuz_list_checkbox" onclick="fnHighlight(\''.$this->obj->idtask.'\',\''.$color.'\',\''.$change_to.'\')" />
                                 </span>
                                  <span class="task_category">'.$this->obj->task_category.'</span>&nbsp;
                                  <span'.$strike_class.'><a href="/Task/'.$this->obj->idproject_task.'" onclick="allowHighlight=false;" >'.$this->obj->task_description.'</a>'.$contact_full_name.'</span>




                              </div><div class="ptask_progbar1">';


                 /*$img_url = '<img src="/images/discussion.png" width="16" height="16" alt="" />';
               $html .='&nbsp;&nbsp;<b>
                        <a href="/Task/'.$this->obj->idproject_task.'">'.$do_task_project->name.'</b></a>
                          &nbsp;&nbsp;<a href="'.$project_task_url.'">'.$img_url.'</a>';*/







                }else{
                    $html .= '<li id="pt_'.$this->obj->idtask.'" class="'.$ddtasks.'">
                              <div class="'.$task_class.'" id="list'.$this->obj->idtask.'"> 
                                  <span class="task_category">'.$this->obj->task_category.'</span>&nbsp;
                                  <span'.$strike_class.'><a href="/Task/'.$this->obj->idproject_task.'" onclick="allowHighlight=false;" >'.$this->obj->task_description.'</a>'.$contact_full_name.'</span>
                              </div><div class="ptask_progbar1">';
                }
            }
            if ($this->obj->status == 'closed') {
                $html .= _('closed').'<div class="ptask_progbar3" style="width: 100px;"></div></div>'."\n";
            } else {
                $html .= _('progress').'<div class="ptask_progbar2" style="width: '.$progress_pixels.'px;"></div></div>'."\n";
            }
            if($this->obj->access != 'Public'){
                $html .= '<div class="ptask_handle"></div>';
            }
            $html .= '</li>'."\n";
      break;
    }
    echo $html;
  }

  /**
  */
  public function displayHeader(){
  }

    
}
?>
