<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Tasks Category';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/header.inc.php');
    $do_task = new Task($GLOBALS['conx']);
    $do_task_category = new TaskCategory();
    $do_contact_task = new Contact();
?>
<?php $thistab = 'Tasks'; include_once('i_ofuz_navtabs.php'); ?>
<div class="content">
 <script type="text/javascript">
    //<![CDATA[
    function fnEditTaskCategory(task) {
        $.ajax({
            type: "GET",
<?php
$e_editForm = new Event("TaskCategory->eventAjaxEditTaskCategoryForm");
$e_editForm->setEventControler("ajax_evctl.php");
$e_editForm->setSecure(false);
?>
            url: "<?php echo $e_editForm->getUrl(); ?>",
            data: "id="+task,
            success: function(html){
            	$("#t"+task).hide(0);
            	$("#e"+task)[0].innerHTML = html;
                $("#e"+task).show(0);
            }
        });
    };
    function fnCancelEdit(task) {
        $("#e"+task).hide(0);
        $("#t"+task).show(0);
    };
    function fnDeleteTaskCategory(task) {
        $.ajax({
            type: "GET",
<?php
$e_editForm = new Event("TaskCategory->eventAjaxTaskCategoryDelete");
$e_editForm->setEventControler("ajax_evctl.php");
$e_editForm->setSecure(false);
?>
            url: "<?php echo $e_editForm->getUrl(); ?>",
            data: "id="+task,
            success: function(){
            	$("#t"+task).css("text-decoration", "line-through");
            	$("#t"+task).fadeOut("slow", function() {
            	    $("#e"+task).remove();
            	    $("#b"+task).remove();
                });
            }
        });
    };
    //]]>
    </script>
    <table class="main">
        <tr>
            <td class="main_right">
                <!--<div class="tasktop">
                    <span class="headline11">Your Task Categories</span>
                    <span class="headerlinks">Upcoming<span class="headerlinksI">|</span><a href="tasks_completed.php">Completed</a><span class="headerlinksI">|</span><a href="ofuz_demo_tasks3.php">Assigned</a></span>
                </div>-->
                <div class="content730">
                    <div class="task">
                        <span id="t" class="task_item">
                          <?php
                                $do_task_category_add = new TaskCategory();
                                $do_task_category_add->getiTaskCategoryAddForm();
                                $do_task_category->getUsersTaskCategory();
                                if($do_task_category->getNumRows()){
                                  while($do_task_category->next()){ ?>
                                      <span id="t<?php echo $do_task_category->idtask_category; ?>" class="task_item">
                                      <input type="checkbox" name="c<?php echo $do_task_category->idtask_category; ?>" class="task_checkbox" onclick="fnDeleteTaskCategory('<?php echo $do_task_category->idtask_category; ?>')" />
                                      <span class="task_desc"><a href="#" onclick="fnEditTaskCategory('<?php echo  $do_task_category->idtask_category; ?>')"><?php echo $do_task_category->name; ?></a>
                                     </span>
                                      </span>
                                      <div id="e<?php echo $do_task_category->idtask_category; ?>" style="display: none;"></div>
                                      <span id="b<?php echo $do_task_category->idtask_category; ?>"><br /></span>
                                  <?php }
                                }else{
                                }
                          ?>
                            <span class="task_desc">
                          </span>
                        </span>
                    </div>
                    
                    <div class="dottedline"></div>
                    <?php $footer_note = 'dropboxtask'; include_once('includes/footer_notes.php'); ?>
                    <div class="bottompad40"></div>
                </div>
            </td>
        </tr>
    </table>
<?php include_once('i_ofuz_logout.php'); ?>
</div>
</body>
</html>