<?php
// Copyright 2008-2010 SQLFusion LLC           info@sqlfusion.com
/**COPYRIGHTS**/
  /**
   * SamplePlugIn configuration
   * This is a configuration file for the Sample plugin.
   * Its load class and set hooks 
   *
   * @package SamplePlugIn
   * @author Philippe Lewicki <phil@sqlfusion.com>
   * @license ##License##
   * @version 0.1
   * @date 2010-09-04  
   */

   // We include here our Block Object
   include_once("plugin/CoworkerAdd/CoworkerAdd.class.php");

   // Hook for the block object
   $GLOBALS['cfg_block_placement']['os_co_worker'][] = "CoworkerAdd";

   

?>
