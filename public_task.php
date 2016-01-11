<?php 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/


    /**
	 * task.php
	 * Display the task discussion of a project 
	 * It uses the object: Task, Project, ProjectTask, NoteDraft, WorkFeedProjectDiscuss, Message, Feedback, Breadcrumb
	 * Copyright 2001 - 2010 All rights reserved SQLFusion LLC, info@sqlfusion.com 
	 */

	include_once('config.php');
    $pageTitle = 'Ofuz :: '._('Project Task');
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    
    //include_once('includes/header.inc.php');

    $do_user = new User();
    if(is_object($_SESSION['ProjectDiscussCount'])) {
            $_SESSION['ProjectDiscussCount']->sql_qry_start = 0;
    }

    if (isset($_GET['idprojecttask'])) {
        $idproject_task = $_GET['idprojecttask'];
        $do_project_task = new ProjectTask();
        $do_project_task->getProjectTaskDetails( $idproject_task );
        $do_project = $do_project_task->getParentProject();
        if ($_SESSION['do_project']->idproject != $do_project_task->idproject) {
            $do_project->sessionPersistent("do_project", "project.php", OFUZ_TTL);
        }
    } 

    $do_task_name = new ProjectTask();
    $task_name = $do_task_name->getTaskName($idproject_task);
    $pageTitle = 'Ofuz :: '.$task_name;

    include_once('includes/header.inc.php');

    //check if the user can access the task or not
    $task_access = false;
    $task_operation_access = false;
    
    if($do_project_task->isPublicAccess($idproject_task)){
        $task_access = true;
        $task_operation_access = false;
    }
   
    if($task_access){
        $do_project_task->sessionPersistent('do_project_task', 'task.php', OFUZ_TTL);
        $do_count_discussion = $_SESSION['do_project_task']->getChildProjectDiscuss("ORDER BY priority DESC, date_added DESC,idproject_discuss DESC");
		$count_discussion = $do_count_discussion->getNumRows();

        $ProjectDiscuss = new ProjectDiscuss();
        $ProjectDiscuss->sessionPersistent('ProjectDiscussEditSave', 'project.php', OFUZ_TTL);
       
         $_SESSION['ProjectDiscussEditSave']->idproject_task = $idproject_task;

        $do_discuss = $_SESSION['do_project_task']->getChildProjectDiscuss("ORDER BY priority DESC, date_added DESC,idproject_discuss DESC  limit {$_SESSION['ProjectDiscussEditSave']->sql_qry_start},{$_SESSION['ProjectDiscussEditSave']->sql_view_limit}");
        $return_page = $_SERVER['PHP_SELF'];
		//$ProjectDiscuss->prj_discussion_count = $count_discussion;

		if(!is_object($_SESSION['ProjectDiscussCount'])) {
			$ProjectDiscuss->sessionPersistent('ProjectDiscussCount', 'project.php', OFUZ_TTL);
			$_SESSION['ProjectDiscussCount']->prj_discussion_count = $count_discussion;
			//$_SESSION['ProjectDiscussCount']->sql_qry_start = 0;
		}
    }

    $DiscussNoteExpend  = new ProjectDiscuss($GLOBALS['conx']);
    $DiscussNoteExpend->sessionPersistent("DiscussNoteExpend", "contacts.php", OFUZ_TTL);

?>
<script type="text/javascript">
//<![CDATA[
function showFullNote(idproject_discuss){
    $.ajax({
        type: "GET",
<?php
$e_ProjectDiscuss = new Event("DiscussNoteExpend->eventAjaxProjectTaskDiscussion");
$e_ProjectDiscuss->setEventControler("ajax_evctl.php");
$e_ProjectDiscuss->setSecure(false);
?>
        url: "<?php echo $e_ProjectDiscuss->getUrl(); ?>",
        data: "idnote="+idproject_discuss,
        success: function(notetext){
            $("#notepreview"+idproject_discuss)[0].innerHTML = notetext;
        }
    });
}
//]]>
</script>

<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
    <div class="layout_header">
        <div class="layout_logo">
            <a href="/index.php"><img src="/images/ofuz_logo.jpg" width="188" height="90" alt="" /></a>
        </div>
    </div>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <?php
          if(!$task_access){
              
              echo '<div class="messageshadow_unauthorized">';
              echo '<div class="messages_unauthorized">';
              echo 'unauthorized';
              echo '</div></div><br /><br />';
              exit;
          }
    ?> 
    <table class="layout_columns"><tr>
     <td class="layout_rcolumn">
	    
        <div class="mainheader">
            <div class="ptask_detail_name">
                <span class="headline14">
                <?php
                    echo '<a href="/PublicProject/'.$_SESSION['do_project']->idproject.'">  '.$_SESSION['do_project']->name.'</a> >> ';
                ?>
                <?php echo $_SESSION['do_project_task']->task_category.": ".$_SESSION['do_project_task']->task_description; ?></span>
                
            </div>
        </div>
        <div id="ptask_ctlbar" style="display: none;">
            
           
        </div>
        <div class="contentfull">
        <?php
          if($do_project_task->status=='open'){
        ?>
            <div class="headline_fuscia"></div>
            
            <div class="percent95">
            
                
            </div>

            <?php } else{ ?>
                <div class="headline_fuscia"><?php echo _('This task is closed'); ?></div>
            <?php }
                $do_discuss->sessionPersistent('ProjectDiscussEditSave', "project.php", OFUZ_TTL);
                if ($do_discuss->getNumRows()) {
                    echo '<div class="spacerblock_24"></div>', "\n";
                    echo '<div class="headline_fuscia">', $_SESSION['do_project_task']->task_description, ' '._('discussion').'</div>', "\n";
                    $item_count = 0;
                    while ($do_discuss->next()) {
                        $file = '';
                        $preview_item = '';
                        if($do_discuss->document != ''){
                            $doc_name = $do_discuss->document;
                            //$doc_name = str_replace("  ","%20%20",$do_discuss->document);
                            //$doc_name = str_replace(" ","%20",$doc_name);
                            $file_url = "/files/".$doc_name;
                            //$file_url = '/files/'.$do_discuss->document;
                            $file = '<br /><a href="'.$file_url.'" target="_blank">'.$do_discuss->document.'</a>';
                               //$file = '<br /><a href="/files_download.php?e=ProjectTask&id='.$idproject_task.'&file='.$do_discuss->document.'" target="_blank">'.$do_discuss->document.'</a>';
                        }
                        
                        $item_text = $do_discuss->formatDiscussionItemDisplay($do_discuss->discuss);
                        if (substr_count($item_text, '<br />') > 4) {
                        	//$item_text = preg_replace('/(.*?<br \/>.*?<br \/>.*?<br \/>.*?<br \/>)(.*)/','$1',str_replace("\n",'',$item_text)).' ';
                        } /*else if (strlen($item_text) > 500) {
                            $preview_item = substr($item_text, 0, 500);
                        }*/
                        if($do_discuss->iduser){
                          $added_by = $do_user->getFullName($do_discuss->iduser);
                        }else{
                          $added_by = $do_discuss->drop_box_sender;
                        }
                        $e_gen_dropboxid = new Event('do_project_task->eventGenerateDropBoxIdTask');
                        $e_PrioritySort = new Event('ProjectDiscuss->eventPrioritySortNotes');
                        $e_PrioritySort->addParam('goto', 'task.php');
                        $e_PrioritySort->addParam('idnote', $do_discuss->idproject_discuss);
                        $star_img_url = '<img src="/images/'.($do_discuss->priority > 0?'star_priority.gif':'star_normal.gif').'" class="star_icon" width="14" height="14" alt="'._('Star this note to move it on top').'" />';
                        if (is_object($_SESSION['ProjectDiscussEditSave'])) {
                            $e_discuss_del = new Event('ProjectDiscussEditSave->eventTempDelNoteById');
                        }
                        $e_discuss_del->addParam('goto', 'task.php');
                        $e_discuss_del->addParam('id', $do_discuss->idproject_discuss);
                        $e_discuss_del->addParam('context', 'ProjectDiscuss');
                        $del_img_url = 'delete <img src="/images/delete.gif" width="14px" height="14px" alt="" />';
                        echo '<div id="notetext',$do_discuss->idproject_discuss,'" class="vpad10">';
                        echo '<div style="height:24px;position:relative;"><div class="percent95"><img src="/images/discussion.png" class="note_icon" width="16" height="16" alt='._('Task Discussion').'" />';
                        if($task_operation_access === true){
                          echo $e_PrioritySort->getLink($star_img_url, ' title="'._('Star this note to move it on top').'"');
                        }
                        echo '<b>'.date('l, F j', strtotime($do_discuss->date_added)).'</b>&nbsp;(Added By :&nbsp;'.$added_by.')</div>'; 

                        if($task_operation_access === true){
                          echo '<div id="trashcan', $item_count++, '" class="deletenote" style="right:0;">'.'<a href="#"  onclick="fnEditNote(\'notetext'.$do_discuss->idproject_discuss.'\','.$do_discuss->idproject_discuss.');return false;">',_('edit'),'</a>&nbsp;|&nbsp;'.$e_discuss_del->getLink($del_img_url, ' title="'._('Delete this note').'"').'</div>';
                        }
                        echo '</div>';
                        if ($preview_item != '') {
                            echo '<div id="notepreview',$do_discuss->idproject_discuss,'">',$preview_item,'<a href="#" onclick="showFullNote(',$do_discuss->idproject_discuss,'); return false;">',_('more...'),'</a><br /></div>';
                        } else {
                            echo $item_text;
                        }
                        //echo '<div id="e'.$do_discuss->idproject_discuss.'" style="display: none;" class="note_edit_box"></div>';
                        echo $do_discuss->formatDocumentLink("ProjectTask").'</div>
                        <div id="e'.$do_discuss->idproject_discuss.'" style="display: none;" class="note_edit_box"></div>
                        <div id="'.$do_discuss->idproject_discuss.'" class="message_box"></div>';
                        
                    }
					
                }
            ?>
			<div id="last_prj_loader"></div>
            <div class="dottedline"></div>
            <div class="section20">
                &nbsp;
            </div>
            <div class="solidline"></div>
        </div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php  ?>
</body>
</html>
