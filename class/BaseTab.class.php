<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

/**
  * BaseTab class
  * 
  * @author 
  */

class BaseTab extends BaseObject{
    protected $tab_name = '';
    protected $tab_base_url = '';
    protected $tab_default_page = '#';
    protected $pages = array() ; // Array to Hold the page names where the Tab will have other animation than the other tabs
    protected $is_active = true;
    protected $current_page;
    protected $plugin_name;


    public function __construct($plugin_name='') {
        if (!empty($plugin_name)) {
		$this->setPlugInName($plugin_name);
        }
    }

    /**
      * Set Tab Name
      @param string $tab_name 
    */
    public function setTabName($tab_name){
    	$this->tab_name = $tab_name;
 	return $this;
    }

    /**
      * Returns the Tab Name 
    */
    public function getTabName(){
    return $this->tab_name;
    }
    
    public function setPlugInName($name) {
      $this->plugin_name = $name;
      return $this;
    }
    public function getPlugInName() {
      return $this->plugin_name;
    }
    
    /**
      * Set the page names for which the tab is animanted to have a different look
      * This array should contains all the pages under that tab.
      * its the full page name without the .php extention.
      * @param array $pages
    */
    public function setPages($pages){
      $this->pages = $pages;
      return $this;
    }

    /**
      * Returns the pages set for the Tab
    */
    public function getPages(){
      return $this->pages ;
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
     * set Current page 
     * its sets the current page for the content
     * If the page name is not part of the pages array
     * it set an empty string and return false.
     * @param string name of the current page
     * @return true is the page is set and false if not.
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
      * returns the tab url
    */    
    public function getTabUrl() {
      return $this->tab_base_url.$this->pages[$this->getDefaultPage()];
    }

    /**
      * Get current page filepath
      * based on the current page return the filepath
      * of the current page.
      * @return file path of the current page.
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
    */
//                   <a href="/tab_template.php?plugin='.$this->getPlugInName().'&content='.$this->getDefaultPage().'">'.$this->getTabName().'</a>
    public function displayTab(){
    // Check the current file and process the tab
      $output = '';
      $plugin_content = $_GET['content'] ;
      if(in_array($plugin_content,$this->getPages()) || $GLOBALS['thistab'] == $this->getTabName() ){
        $output .= '<div class="layout_navtab_on">
                <div class="layout_navtab_on_l"></div>
                <div class="layout_navtab_on_text">';
	if (strlen($this->getPlugInName()) > 0) {
        	$output .= ' <a href="/'.$this->getPlugInName().'/'.$this->getDefaultPage().'">'.$this->getTabName().'</a>';
        } else {
		$output .= ' <a href="/'.$this->getDefaultPage().'.php">'.$this->getTabName().'</a>';
	}
        $output .= ' </div>
                <div class="layout_navtab_on_r"></div>
              </div>'; 
      }else{
        $output .= '<div class="layout_navtab">';
	if (strlen($this->getPlugInName()) > 0) {
        	$output .= ' <a href="/'.$this->getPlugInName().'/'.$this->getDefaultPage().'">'.$this->getTabName().'</a>';
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

    /**
      * Method checking if the block is active to be displayed
    */
    public function isActive(){
      return $this->is_active;
    }
    
    /**
      * Set if the block is active and should be display or not
      * @param boolean true or false value.
      */
      
    public function setIsActive($bool_active) {
      $this->is_active = $bool_active; 
    }
    
    
}

?>
