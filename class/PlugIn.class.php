<?php
// Copyright 2008-2010 SQLFusion LLC           info@sqlfusion.com
/**COPYRIGHTS**/

/**
  * PlugIn class
  * Base Class for the different PlugIn content classes
  *
  * @author SQLFusion's Dream Team <info@sqlfusion.com>
  * @package OfuzCore
  * @license ##License##
  * @version 0.6.2
  * @date 2010-11-13
  * @since 0.6.2
  */



class PlugIn extends BaseObject{
    protected $pages = array() ; 
    protected $is_active = true;
    protected $plugin_name;
    protected $current_page;

    /**
     * Constuctor
     * Start by setting the plugin name.
     * It should match the plugin folder.
     * @param string $plugin_name name of the plugin.
     */
    public function __construct($plugin_name='') {
        if (!empty($plugin_name)) {
          $this->setPlugInName($plugin_name);
        }
    }

    /**
     * setPlugInName
     * Set the name of the plugIn
     * This is required as it define the include path.
     * @param string $name name of the plugin
     */
    public function setPlugInName($name) {
      $this->plugin_name = $name;
      return $this;
    }
    
    /** 
     * getPlugInName
     * @return sting name of the plugin
     * @see setPlugInName()
     */
    public function getPlugInName() {
      return $this->plugin_name;
    }
    
    /**
      * Set the page names for which the tab is animanted to have a different look
      * This array should contains all the pages under that tab.
      * its the full page name without the .php extention.
      * @param array $pages all the page names
      * @return Tab current object.
      */
    public function setPages($pages){
      $this->pages = $pages;
      return $this;
    }
    
    /**  
      * Add on page to the pages array
      * @see setPages()
      * @param string page_name name of the page to add
      */
    public function addPage($page_name) {
    	$this->pages[] = $page_name;
    	return $this;
    }

    /**
     * Returns the pages set for the Tab
     * @return array with all the page name
     * @see setPages()
     */
    public function getPages(){
      return $this->pages ;
    }
    
     
    /** 
     * set Current page 
     * its sets the current page for the content
     * If the page name is not part of the pages array
     * it set an empty string and return false.
     * @param string $page_name name of the current page
     * @return boolean true is the page is set and false if not.
     */
    public function setCurrentPage($page_name) {
      if (in_array($page_name, $this->pages)) {
          $this->current_page = $page_name;
          return true;
      } else {
        $this->current_page = '';
        return false;
      }
    }
    /**
      * get Current page
      * return the current page name
      * @return the name of the current page
      */
    public function getCurrentPage() {
        return $this->current_page;
    }
    
    /**
      * Get current page filepath
      * based on the current page return the filepath
      * of the current page.
      * @return string file path of the current page.
      */
    public function getCurrentPageFilePath() {
      $content_file_path = 'plugin/'.$this->getPlugInName().'/'.$this->getCurrentPage().".php"; 
      return $content_file_path;
    }

    /**
      * Get current page url
      * This is used to replace things like $PHP_SELF
      * 
      * @return string url of the current plugIn Page.
      */
    public function getCurrentPageUrl() {
       return '/PlugIn/'.$this->getPlugInName().'/'.$this->getCurrentPage();
    
    }

    /**
      * Method checking if the block is active to be displayed
      * @return boolean if the plugin is active.
      */
    public function isActive(){
      return $this->is_active;
    }
    
    /**
      * Set if the block is active and should be display or not
      * @param boolean $bool_active true or false value.
      */
      
    public function setIsActive($bool_active) {
      $this->is_active = $bool_active; 
    }
    
    
}




?>