<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

/**
  * TabSetting class
  * Define content and display a tab in the settings. 
  * 
  * @author SQLFusion's Dream Team <info@sqlfusion.com>
  * @package OfuzCore
  * @license ##License##
  * @version 0.6
  * @date 2010-09-07
  * @since 0.6
  */

class TabSetting extends Tab{
    
    protected $title ='';
    /**
      * Display method for the tab 
      * parse through the pages to make a different CSS than the other pages
    */
    public function displayTab(){
	// Check the current file and process the tab
      $output = '';
      $setting_content = $_GET['setting'] ;
      if(in_array($setting_content,$this->getPages()) || $GLOBALS['thistabsetting'] == $this->getTabName() ){
        $output .= '<div class="settingstabon">';
	if (strlen($this->getPlugInName()) > 0) {
		$output .= '<a href="/Setting/='.$this->getPlugInName().'/'.$this->getDefaultPage().'">'.$this->getTabName().'</a>'; 
        } else {
		$output .= ' <a href="/'.$this->getDefaultPage().'.php">'.$this->getTabName().'</a>';
	}
        $output .= '</div>'; 
      }else{
        $output .= '<div class="settingstab">';
	if (strlen($this->getPlugInName()) > 0) {
		$output .= ' <a href="/Setting/'.$this->getPlugInName().'/'.$this->getDefaultPage().'">'.$this->getTabName().'</a>'; 
        } else {
		$output .= ' <a href="/'.$this->getDefaultPage().'.php">'.$this->getTabName().'</a>';
	}
        $output .= '</div>'; 
      }
      if($this->isActive()){
        echo $output ;
      }
    }
    
    public function setTitle($title) {
      $this->title = $title;
      return $this;
    }
    public function getTitle() {
      return $this->title; 
    }  
}

?>
