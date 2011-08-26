<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  /**
    * Ofuz Lost post conf file.
    * Post configuration files are loaded after the session 
    * is initialised.
    * and Session variables loaded.
    * @author SQLFusion's Dream Team <info@sqlfusion.com>
    * @package OfuzCore
    * @license GNU Affero General Public License
    * @version 0.6
    * @date 2010-09-03
    * @since 0.6
    */
 
    $d2 = dir($GLOBALS['cfg_project_directory']."plugin/");
    while($entry = $d2->read()) {
        if (preg_match("/\.postconf\.inc\.php$/i", $entry) && !preg_match("/^\./", $entry)) {
            $postconfig_files_plugin[] = $entry;
        }
    }
    $d2->close();

    if (is_array($postconfig_files_plugin)) {
        sort($postconfig_files_plugin) ;
		foreach($postconfig_files_plugin as $postconfig_file_plugin) {
		  include_once($cfg_project_directory."plugin/".$postconfig_file_plugin);       
		}  
    }
 
