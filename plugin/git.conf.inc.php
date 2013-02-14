<?php
// Copyright 2008-2010 SQLFusion LLC           info@sqlfusion.com
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

// Plug-in Definition
// status : devel, alpha, beta, rc, stable

$plugins_info['Git'] = 
		Array ('name' => 'Ofuz Git Repository Integration',
			'description' => 'This Plug-in integrates Git with Ofuz.',
			'version' => '0.0.1',
			'status' => 'beta',
			'settings' => Array('Git Repository'),                          
			'blocks' => Array('ProjectGitRepositoryAddBlock','TaskGitBranchDetailsBlock')
			);  



include_once("plugin/Git/class/UserGitrepo.class.php");
include_once("plugin/Git/GitRepositoryAdd/GitRepoAdd.class.php");
include_once("plugin/Git/class/ProjectGitRepositoryAddBlock.class.php");
include_once("plugin/Git/class/TaskGitBranchDetailsBlock.class.php");

$GLOBALS['cfg_block_placement']['project'][] = "ProjectGitRepositoryAddBlock";
$GLOBALS['cfg_block_placement']['task'][] = "TaskGitBranchDetailsBlock";

 /*  $GLOBALS['cfg_setting_tab_placement']->append(new TabSetting("Git"));
   $GLOBALS['cfg_setting_tab_placement']->next();
   $GLOBALS['cfg_setting_tab_placement']->current()
                                        ->setTabName(_("Git Repo"))
                                        ->setTitle("Git Integration")
                                        ->setPages(Array ("git_repo"))
                                        ->setDefaultPage("git_repo");



   // Hook for the block object
   $GLOBALS['cfg_block_placement']['os_git_repo'][] = "GitRepoAdd";
*/
$GLOBALS['cfg_setting_tab_placement']->append(new TabSetting("Git"));
$GLOBALS['cfg_setting_tab_placement']->next();
$GLOBALS['cfg_setting_tab_placement']->current()
				    ->setTabName("Git Repository")
				    ->setTitle("Set up your Git Repository:")
				    ->setPages(Array ("git_repo","git_commitlog"))
				    ->setDefaultPage("git_repo");
?>
