<?php
// Copyright 2008-2010 SQLFusion LLC           info@sqlfusion.com
/**COPYRIGHTS**/

/**
  * Tab class
  * Display tabs and manage Tab plug-in content include.
  *
  * @author SQLFusion's Dream Team <info@sqlfusion.com>
  * @package OfuzCore
  * @license ##License##
  * @version 0.6
  * @date 2010-09-03
  * @since 0.6
  */

class Tab extends PlugIn{
    protected $tab_name = '';
    protected $tab_base_url = '';
    protected $tab_default_page = '#';
    // Array to Hold the page names where the Tab will have other animation than the other tabs
    protected $title;
    protected $message_key;
    
    /**
     * Set Tab Name
     * @param string $tab_name name of the tab
     */
    public function setTabName($tab_name){
    	$this->tab_name = $tab_name;
    	if (strlen($this->getTitle()) == 0) {
    		$this->setTitle($tab_name);
    	}
 	return $this;
    }

    /**
     * Returns the Tab Name 
     * @return string tab_name
     */
    public function getTabName(){
    return $this->tab_name;
    }
    
    /**
      * Set a base url for the tab pages
      * This to be used if pages are on a remote server
      * (facebook like)
      * or a different path than default plugin path.
      *
      * @param string $tab_url
      */
    public function setTabUrlBase($tab_url){
      $this->tab_base_url = $tab_url;
    }

    /**
      * Return the base url or file path for the plugin
      * Content pages.
      * @return string base url or file path.
      */
    public function getTabUrlBase(){
      return $this->tab_base_url;
    }
    
    /**
      * The default page is the entry page for the
      * Plug-in tab.
      * The default page need to be part of the pages set 
      * for this plug-in page.
      * @param string name of the page, usually a full page name as in the pages array
      * @see setPages();
      */
      
    public function setDefaultPage($page_name) {
      $this->tab_default_page = $page_name;
      return $this;
    }
    
    /**
     * Return the name of the default page for the plugin tab.
     * @return name of the default page.
     * @see setDefaultPage
     */
    public function getDefaultPage() {
      return $this->tab_default_page;
    }
    
    
    /**
     * returns the tab url
     * @return string tab url 
     */    
    public function getTabUrl() {
      return $this->tab_base_url.$this->pages[$this->getDefaultPage()];
    }

    /**
      * Get current page filepath
      * based on the current page return the filepath
      * of the current page.
      * @return string file path of the current page.
      */
    public function getCurrentPageFilePath() {
      $url_base = $this->getTabUrlBase();
      if ( empty( $url_base ) ) {
        $content_file_path = 'plugin/'.$this->getPlugInName().'/'.$this->getCurrentPage().".php"; 
      } else {
        $content_file_path = $this->getTabUrlBase().'/'.$this->getCurrentPage();
      }
      return $content_file_path;
    }

    /**
     * Display method for the tab 
     * parse through the pages to make a different CSS than the other pages
     * It generate a string with all the HTML code and echo it if the tab is active.
     */
                  
    public function displayTab(){
    // Check the current file and process the tab
      $output = ''; 
      $plugin_content = $_GET['content'] ;
      if(in_array($plugin_content,$this->getPages()) || $GLOBALS['thistab'] == $this->getTabName() ){
        $output .= '<div class="layout_navtab_on">
                <div class="layout_navtab_on_l"></div>
                <div class="layout_navtab_on_text">';
        if (strlen($this->getPlugInName()) > 0) {
          $output .= ' <a href="/Tab/'.$this->getPlugInName().'/'.$this->getDefaultPage().'">'.$this->getTabName().'</a>';
        } else {
          $output .= ' <a href="/'.$this->getDefaultPage().'.php">'.$this->getTabName().'</a>';
        }
        $output .= ' </div>
                <div class="layout_navtab_on_r"></div>
              </div>'; 
      }else{
        $output .= '<div class="layout_navtab">';
        if (strlen($this->getPlugInName()) > 0) {
            $output .= ' <a href="/Tab/'.$this->getPlugInName().'/'.$this->getDefaultPage().'">'.$this->getTabName().'</a>';
        } else {
            $output .= ' <a href="/'.$this->getDefaultPage().'.php">'.$this->getTabName().'</a>';
        }
        $output .= '        </div>'; 
      }
      if($this->isActive()){
        echo $output ;
      }
    }

    /**
      * Method to process the Tab
      * Child class will override the method as per need
      */
    public function processTab() {
	   $this->displayTab();
    }

    /** Title to display for that tab
     */
    public function setTitle($title) {
      $this->title = $title;
      return $this;
    }
    public function getTitle() {
      return $this->title; 
    }      
    /** Set the default plugin message.
     */
    public function setMessage($message) {
      	$this->message_key = $message;
       return $this;
    }
    
    public function getMessageKey() {
      return $this->message_key; 
    } 
    
    /**
     * Set a menu for all the pages in the plugin.
     */   
    public function setMenu(SubMenu $menu) {  
    	foreach ($this->getPages() as $page) {
            $GLOBALS['cfg_submenu_placement'][$page] = $menu;
        }  
        return $this;
    }
    
}