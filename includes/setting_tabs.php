<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

if($GLOBALS['cfg_setting_tab_placement']->count() > 0 ){
  foreach($GLOBALS['cfg_setting_tab_placement'] as  $tab ){  
    if (is_object($tab)) {  
      if($tab->isActive() === true ){
	  $tab->processTab();
      }
    }  
  }  
}
?>