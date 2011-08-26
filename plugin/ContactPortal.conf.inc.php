<?php
// Copyright 2008-2010 SQLFusion LLC           info@sqlfusion.com
/**COPYRIGHTS**/

  /**
   * This is a configuration file for the ContactFileUploadUrl.
   * It loads class and set hooks 
   *
   * @package OfuzContactPortal
   * @author SQLFusion's Dream Team <info@sqlfusion.com>
   * @license ##License##
   * @version 0.1
   * @date 2010-11-03
   */

   $GLOBALS['cfg_plugin_contact_file_upload_url'] = true;

   // We include here our Block Object
   include_once("plugin/ContactPortal/ContactFileUploadUrl.class.php");
   include_once("plugin/ContactPortal/ContactPortal.class.php");
   include_once("plugin/ContactPortal/ContactMessage.class.php");

   // Hook for the block object
   $GLOBALS['cfg_block_placement']['contact'][] = "ContactFileUploadUrl";

   $GLOBALS['cfg_plugin_page']->append(new PlugIn("ContactPortal"));
   $GLOBALS['cfg_plugin_page']->next();
   $GLOBALS['cfg_plugin_page']->current()
                              ->setPages(Array (
                                           "cp_settings",
                                           "contact_portal"));
?>
