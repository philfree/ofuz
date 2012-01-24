<?php 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/


    /**
	 * tasks.php
	 * Display the tasks of a user group by time.
	 * It uses the object: Task
	 * Copyright 2001 - 2010 All rights reserved SQLFusion LLC, info@sqlfusion.com 
	 * Authors: Philippe Lewicki, Abhik Chakraborty, Jay Link, Ravi Rokkam  
	 */

    $pageTitle = 'Ofuz :: Tasks';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory();
    $do_contact_task = new Contact();


   /* $do_list_project_task = new ProjectTask();                    
    $do_list_project_task->viewProjectTasks();*/


    if(!is_object($_SESSION['do_list_project_task'])){
        $do_project_task = new ProjectTask();
        $do_project_task->sessionPersistent("do_list_project_task", "projects.php", OFUZ_TTL);
    }



?>
<script type="text/javascript">
    //<![CDATA[
<?php include_once('includes/ofuz_js.inc.php'); ?>
       //]]>
</script>

<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>

  <table class="layout_columns">
      <tr><td class="layout_lmargin"></td>
          <td>
              <div class="layout_content">
                    <?php $thistab = _('Tasks'); include_once('includes/ofuz_navtabs.php'); ?>
                    <?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
                  <div class="grayline1"></div>
                  <div class="spacerblock_20"></div>
                   <table class="layout_columns">
                      <tr>
                        <td class="layout_lcolumn">
                            <?php $page_name='tasks'; include_once('plugin_block.php');?>
                        </td>
                        <td class="layout_rcolumn">
                          <?php
                              $msg = new Message(); 
                              if ($msg->getMessageFromContext("tasks")) {
                                  echo $msg->displayMessage();
                              }
                            ?>
                            <!--<div class="tasktop">-->
        
                            <div class="mainheader pad20">
                                <span class="headline14">Your tasks</span>
                                <?php
								if (is_object($GLOBALS['cfg_submenu_placement']['tasks'] ) ) {
									echo  $GLOBALS['cfg_submenu_placement']['tasks']->getMenu();
								}
								?>
                            </div>

        <div class="contentfull">
          <?php

                    $_SESSION['remember_task_on'] = 'Project';
                    $do_task->getAllTaskProjectRelated();
                    $last_project = 0;
                    echo '<div class="tasks">';
                    while($do_task->next()){
                          if($last_project != $do_task->idproject ){                              
                              echo '<div ><br /><b><a href="/Project/'.$do_task->idproject.'">'.$do_task->name.'</a></b></div>';
                          }
                          $last_project = $do_task->idproject;
                          echo '<br /><span class="task_item">&nbsp;&nbsp;';
                          if ($do_task->task_category != '') { 
                              echo  '<span class="task_category">'.$do_task->task_category.'</span>&nbsp;'; 
                          }
                          echo '<span class="task_desc"><a href="/Task/'.$do_task->idproject_task.'">'.$do_task->task_description.'</a>';
                          echo '</span></span>';
                          //echo '<div></div>';
                          //echo '<span></span>';
                    }
                    echo '</div>';
            
            ?>
        </div>

            <div class="dottedline"></div>
            <?php $footer_note = 'dropboxtask'; include_once('includes/footer_notes.php'); ?>
        </div>
       </div>
      </div>
    </td></tr></table>
    <div class="spacerblock_20"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>


