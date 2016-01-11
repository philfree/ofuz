<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Block that display a form to add a project
     *
     * calls the method: Project->getProjectAddForm()
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzCore
     * @license GNU Affero General Public License
     * @version 0.6
     * @date 2010-09-06
     * @since 0.3
     * @see OfuzCore.Project#getProjectAddForm()
     */

class ProjectsAddProjectBlock extends BaseBlock{

  public $short_description = 'Project Add Block';
  public $long_description = 'Add new projects';
    
      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extent BaseBlock
      */

      function processBlock(){
	  $this->setTitle(_('Add a project'));
	  $this->setContent($this->generateAddProjectBlock());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
       * @return string : HTML form
       * @see class/Project.class.php
      */

      function generateAddProjectBlock(){

	    $output = '';

	    $output .= '<div class="percent95">';
	    $do_project_add = new Project();
	    $output .= $do_project_add->getProjectAddForm();
	    $output .= '</div>';

	    return $output;

      }

      

      
}

?>
