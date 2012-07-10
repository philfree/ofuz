<?php


class PluginEnable extends DataObject {
    public $table = "plugin_enable";
    public $primary_key = "idplugin_enable";
    
    /**
      * Function to enable plugin
      * Checks if the plugin is disabled and if yes enable the plugin 
      * If the plugin is not yet in the table for that user then will add a new entry
      * @param String $plugin, the plugin object name
      * @param Integer $iduser
    */
    public function enablePlugin($plugin,$iduser=""){
         if($iduser == "") $iduser = $_SESSION['do_User']->iduser ;
         $obj = $this->isPluginAddedBefore($plugin,$iduser);
         if($obj === false){
            $this->addNew(); 
            $this->plugin = $plugin;
            $this->enabled = 1 ;
            $this->iduser = $iduser;
            $this->date_modified = date("Y-m-d H:i:s");
            $this->add();
        }else{
            $this->getId($obj->getData("idplugin_enable"));
            $this->enabled = 1 ;
            $this->date_modified = date("Y-m-d H:i:s");
            $this->update();
        }
    }


    /**
      * Function disabling the plugin
      * @param Integer $id, primary key of the table plugin_enable
    */
    public function disablePlugin($id){
        if(isset($id) && $id != '' ){
            $this->getId((int)$id);
            $this->enabled = 0 ;
            $this->date_modified = date("Y-m-d H:i:s");
            $this->update();  
        }
    }


    /**
      * Checks if the plugin is enabled
      * @param String $plugin, the plugin object name
      * @param Integer $iduser
      * @return false if not enabled, else the primary_key value
    */
    public function isEnabled($plugin,$iduser=""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser ;
        $obj  = $this->isPluginAddedBefore($plugin,$iduser);
        if($obj === false ){
            return false;
        }else{
            if($obj->getData("enabled") == 1 ){
                return $obj->getData("idplugin_enable");
            }else{
                return false ;
            }
        }
    }

    /**
      * Method to check if the plugin value is in the table plugin_enable
      * @param String $plugin, the plugin object name
      * @param Integer $iduser
      * @return false if no data found else the query object
    */
    public function isPluginAddedBefore($plugin,$iduser=""){
        if($iduser == "") $iduser = $_SESSION['do_User']->iduser ;
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from {$this->table} where plugin = '{$plugin}' AND iduser = {$iduser}");
        if($q->getNumRows() > 0 ){
            $q->fetch();
            return $q ;
           // return $q->getData("idplugin_enable");
        }else{
            return false ;
        }
    }

    /**
      * Event method to enable plugin
      * @param Object $evtcl
     */
    public function eventEnablePlugin(EventControler $evtcl){ 
        $this->enablePlugin($evtcl->plugin);
    }

    /**
      * Event method to disable plugin
      * @param Object $evtcl
     */
    public function eventDisablePlugin(EventControler $evtcl){ 
        $this->disablePlugin($evtcl->idplugin_enable);
    }

    

    /** Event funtion to enable the AddOn
      *@param Object $evctl
    **/
    
    public function eventEnableAddOn(EventControler $evctl){
        $tabs= $evctl->tabs;
        $settings = $evctl->settings;        
        $blocks = $evctl ->blocks;
               
        if(!empty($tabs)){
          foreach($tabs as $tabname){
             $this->enablePlugin($tabname);
          }
        }
  
        if(!empty($settings)){
          foreach($settings as $setting_name){
            $this->enablePlugin($setting_name);
          }
        }
       
        if(!empty($blocks)){
          foreach($blocks as $block_name){
            $this->enablePlugin($block_name);
          }
        }
    }

    /** Event funtion to Disable the AddOn
      *@param Object $evctl
    **/
    public function eventDisableAddOn(EventControler $evctl){
        
        $idplugin=$evctl->idplugin_enable;
        
        foreach($idplugin as $ids){
          $this->disablePlugin($ids);
        }
    }

    /** funtion to check wheather all components of a plugin is enabled or not .Return false if any of the component is not enabled 
      * otherwise it  returns pluginid values.
      *@param Array $plugins
    **/
    public function isAddOnEnabled($plugins){
      
      $plugin_id_values=array();

      if(!empty($plugins['tabs'])){
          foreach($plugins['tabs'] as $plugin_tab_name){                
            if(($this->isEnabled($plugin_tab_name))==false){
                return false;
            }else{
              array_push($plugin_id_values, $this->isEnabled($plugin_tab_name));
            }
          }
      }

      if(!empty($plugins['settings'])){
          foreach($plugins['settings'] as $plugin_settings_name){           
            if(($this->isEnabled($plugin_settings_name))==false){
                  return false;
            }else{
              array_push($plugin_id_values, $this->isEnabled($plugin_settings_name));
            }
          }
      }

      if(!empty($plugins['blocks'])){
          foreach($plugins['blocks'] as $plugin_blocks_name){            
            if(($this->isEnabled($plugin_blocks_name))==false){
                return  false;
            }else{
              array_push($plugin_id_values,$this->isEnabled($plugin_blocks_name));
            }
          }

          
          return $plugin_id_values;
      }
    }

}


?>
