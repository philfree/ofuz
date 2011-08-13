<?php  
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

   /**
     * Page list all the Project
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzPage
     * @license ##License##
     * @version 0.6
     * @date 2010-09-06
     * @since 0.2
     */


    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    $pageTitle = _('Projects'). ' :: Ofuz';
    include_once('includes/header.inc.php');

    $do_project = new Project();

    if(!is_object($_SESSION['do_project'])){
        $do_project = new Project();
        $do_project->sessionPersistent("do_project", "index.php", OFUZ_TTL);
    }

	$ContactEditSave = new Contact();
?>
<script type="text/javascript">
    //<![CDATA[
	<?php include_once('includes/ofuz_js.inc.php'); ?>
    //]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = _('Projects'); include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns">
      <tr>
	<td class="layout_lcolumn">
	  <?php include_once('plugin_block.php'); ?>
	</td>
	<td class="layout_rcolumn">
	   <?php
            $msg = new Message(); 
			if ($msg->getMessageFromContext("project list")) {
				echo $msg->displayMessage();
			}
       ?>
        <div class="mainheader">
            <div class="pad20">
                <span class="headline14"><?php echo _('Projects'); ?></span>
                <?php
                    if($_SESSION['do_project']->getProjectStatus() == 'closed'){
                        $link = _('Open Projects');
                        $status = 'closed';
                        $set_status = 'open';
                    }else{ 
                        $status = 'open'; 
                        $set_status = 'closed';
                        $link = _('Closed Projects');
                    }
                    $e_filter_project = new Event("do_project->eventSetProjectStatus");
                    $e_filter_project->addParam("status",$set_status);
                ?>
                <span class="ptask_edit"><?php echo $e_filter_project->getLink($link);?></span>
            </div>
        </div>
        <div class="contentfull">
	        <?php
	            $_SESSION['do_project']->getAllProjects($status);
	            if ($_SESSION['do_project']->getNumRows()) {
	                echo $_SESSION['do_project']->viewProjects();
	            }
	         ?>
         </div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
