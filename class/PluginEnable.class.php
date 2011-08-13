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
        $q->query("select * from ".$this->table. " where plugin = '".$plugin."' AND iduser = ".$iduser);
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

    
}


?>
