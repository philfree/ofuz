<?php
// Copyright 2008-2010 SQLFusion LLC           info@sqlfusion.com
/**COPYRIGHTS**/

  /**
   * This is a configuration file for the Translation.
   * It loads class and set hooks 
   *
   * @package Translation
   * @author SQLFusion's Dream Team <info@sqlfusion.com>
   * @license ##License##
   * @version 0.6.2
   * @date 2010-12-06
   */

   //$GLOBALS['cfg_plugin_contact_file_upload_url'] = true;

   // We include here our Block Object
   include_once("plugin/Translation/GoogleTranslateAPI.class.php");
   include_once("plugin/Translation/Translation.class.php");
   include_once("plugin/Translation/GoogleTranslatorMessage.class.php");
   include_once("plugin/Translation/GoogleTranslatorEmailtemplate.class.php");

   // Hook for the block object
   $GLOBALS['cfg_block_placement']['Message'][] = "Translation";
   $GLOBALS['cfg_block_placement']['EmailTemplate'][] = "Translation";

   $GLOBALS['cfg_tab_placement']->append(new Tab("Translation"));
   $GLOBALS['cfg_tab_placement']->next();
   $GLOBALS['cfg_tab_placement']->current()
                    ->setTabName("i18n")
                    ->setPages(Array (
                      "EmailTemplate",
                      "Message"))
           ->setDefaultPage("Message");


?>
