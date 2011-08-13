<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

/**
  * BaseTab class
  * 
  * @author 
  */

class BaseSettingsTab extends Tab{
    
    protected $title ='';
    /**
      * Display method for the tab 
      * parse through the pages to make a different CSS than the other pages
    */
    public function displayTab(){
	// Check the current file and process the tab

      $output = '';
      $setting_content = $_GET['setting'] ;
      if(in_array($setting_content,$this->getPages())){
        $output .= '<div class="settingstabon"><a href="/setting_template.php?plugin='.get_class($this).'&setting='.$this->getDefaultPage().'">'.$this->getTabName().'</a></div>'; 
      }else{
        $output .= '<div class="settingstab"><a href="/setting_template.php?plugin='.get_class($this).'&setting='.$this->getDefaultPage().'">'.$this->getTabName().'</a></div>'; 
      }
      if($this->isActive()){
        echo $output ;
      }
    }
    
    public function setTitle($title) {
      $this->title = $title;
    }
    public function getTitle() {
      return $this->title; 
    }
    


    
    
    
}

?>
