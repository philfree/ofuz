<?php

/**
  * A  plugin class for creating custom Tab for setting
  * The class must extends the BaseTab
  * setTitle() will set the Block Title
  * setContent() will set the content
  * displayBlock() call will display the block
  * @author 
  */

class PluginSettingTimesheet extends BaseSettings{
    
    
      function __construct() {
        $this->setPlugInName("PluginSettingTimesheet");
        $this->setTabName("Test Setting");
        $this->setTitle(_('Test Setting'));
        $this->setPages(array("sqlfusion"));
        $this->setDefaultPage('sqlfusion');      
      }
      /**
	* processTab() , This method must be added  
	* Must extent BaseTab
      */
      function processTab(){
        $this->displayTab();
      }
}

?>
