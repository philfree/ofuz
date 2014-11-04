<?php
// Copyright 2008-2014 SQLFusion LLC           info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/
  /**
   * SamplePlugIn configuration
   * This is a configuration file for the Sample plugin.
   * Its load class and set hooks 
   *
   * @package SamplePlugIn
   * @author Philippe Lewicki <phil@sqlfusion.com>
   * @license GNU Affero General Public License
   * @version 0.1
   * @date 2010-09-04  
   */

   // We include here our Block Object
   //include_once("plugin/SamplePlugIn/BlockSample.class.php");

   // Hook for the block object
   
   include_once("plugin/Zendesk/class/Zendesk.class.php");
                                          
                                         //Zendesk
   $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting("Zendesk"));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Zendesk"))
                                        ->setTitle("Zendesk API")
                                        ->setPages(Array ("zend_api",))
                                        
                                        ->setDefaultPage("zend_api");


?>
