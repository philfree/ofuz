<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Block object to display an AddProject Form it 
     * calls the method: ProjectTask->getProjectTaskAddForm()
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzCore
     * @license GNU Affero General Public License
     * @version 0.6
     * @date 2010-09-06
     * @since 0.6
     * @see OfuzCore.ProjectTask#getProjectTaskAddForm()
     */
 

class ProjectAddProjectTaskBlock extends BaseBlock{
      public $short_description = 'Add project task block';
      public $long_description = 'Add task for the project';
    
      /**
	    * processBlock() , This method must be added  
	    * Required to set the Block Title and The Block Content Followed by displayBlock()
	    * Must extend BaseBlock
        */

      function processBlock(){

          $this->setTitle(_('Add a Project Task'));
          $this->setContent($this->generateAddTaskBlock());
          $this->displayBlock();

      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML form
       * @see class/ProjectTask.class.php
      */

      function generateAddTaskBlock(){

          $output = '';

          $do_add_project_task = new ProjectTask();
          $do_add_project_task->sessionPersistent("do_add_project_task", "projects.php", OFUZ_TTL);
          $output .= $_SESSION['do_add_project_task']->getProjectTaskAddForm();

          return $output;

      }

}

?>
