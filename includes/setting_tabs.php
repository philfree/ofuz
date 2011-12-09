<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/*if($GLOBALS['cfg_setting_tab_placement']->count() > 0 ){
  foreach($GLOBALS['cfg_setting_tab_placement'] as  $tab ){  
    if (is_object($tab)) {  
      if($tab->isActive() === true ){
	  $tab->processTab();
      }
    }  
  }  
}*/


    $do_plugin_enable = new PluginEnable();
        if($GLOBALS['cfg_setting_tab_placement']->count() > 0 ){
               foreach($GLOBALS['cfg_setting_tab_placement'] as  $tab ){   
                  if (is_object($tab)) {  
                  $setting_tab_name=$tab->getTabName();
            
                if(in_array($setting_tab_name,$core_setting_tab_name)){                  
                    if($tab->isActive() === true ){
                        $tab->processTab();
                    }
                  }else{
                    //if($tab->isActive() === true ){
                        $idplugin_enabled = $do_plugin_enable->isEnabled($tab->getTitle());                        
                        if($tab->isActive() === true && $idplugin_enabled!==false ){
                        $tab->processTab();
                    }
                 }  
              }
            }
         }   









?>