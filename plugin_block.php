<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Plugin Blocks';
    include_once('config.php');
    
    if (isset($GLOBALS['page_name'])) {
      $page_name = $GLOBALS['page_name'];
    } else {
      $currentFile = $_SERVER["PHP_SELF"];
      $parts = Explode('/', $currentFile);
      $page_name = $parts[count($parts) - 1];
    
      list($page_name, $file_extention) = explode('.',$page_name);
    }
    // Disable for 0.6.2 will be released on 0.6.3
    //$do_plugin_enable = new PluginEnable();
    if(is_array($cfg_block_placement) && count($cfg_block_placement) > 0 ){
        foreach($cfg_block_placement as $key=> $val ){
            if(strtolower($key) == strtolower($page_name)){
                foreach($val as $block_class_name){
                    $do_blocks = new $block_class_name();
                    //$idplugin_enable = $do_plugin_enable->isEnabled($block_class_name);
                    $idplugin_enable = true;
                        if($do_blocks->isActive() === true && $idplugin_enable !== false ){
                            $do_blocks->processBlock();
                        }
                }
            }
        }
    }
?>