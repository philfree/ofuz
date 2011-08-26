<?php
// Copyright 2008-2011 SQLFusion LLC           info@sqlfusion.com
/**COPYRIGHTS**/
  /**
   * OfflineContacts post configuration
   * Here we update the Tabs
   * If google gear is turn on we link the Contacts tab 
   * to the ggears_contacts.
   *
   * @package OfflineContacts
   * @author Philippe Lewicki <phil@sqlfusion.com>
   * @license ##License##
   * @version 0.1
   * @date 2010-09-04  
   */ 
   if (isset($_SESSION['do_User'])) {
   $user_settings = new UserSettings();
   if ($user_settings->getSetting("google_gears") == "Yes") {							
	   if($GLOBALS['cfg_tab_placement']->count() > 0 ){
		    $new_tabs = new ArrayIterator();
		    $GLOBALS['cfg_tab_placement']->rewind();
		    
		    while($GLOBALS['cfg_tab_placement']->valid()) {   
			    $tab = $GLOBALS['cfg_tab_placement']->current();
				if ($tab->getTabName() == _('Contacts')) {  
					// uncomment the setPlugInName to link to the plugIn Folder.
					 $tab->setPlugInName("OfflineContacts");
					 $tab->setTabName("Contacts Offline")
					     ->setPages(Array ("ggears_contacts"))
						 ->setDefaultPage("ggears_contacts");
				}  
				$GLOBALS['cfg_tab_placement']->next();
			}
		}												
   }
   }
