<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  $pageTitle = 'Ofuz :: Your first project';
  $Author = 'SQLFusion LLC';
  $Keywords = 'Keywords for search engine';
  $Description = 'Description for search engine';
  $background_color = 'white';
  include_once('config.php');
  include_once('includes/ofuz_check_access.script.inc.php');
  include_once('includes/header.inc.php');
     
?>
<style type="text/css">
#simplemodal-overlay {background-color:#000;}
#simplemodal-container {background-color:#333; height:auto; border:8px solid #444; padding:12px;}
</style>

<script type="text/javascript">

$(document).ready(function () {
  $('#create').click(function (){
    var project = $.trim($('#project').val());
    var task = $.trim($('#task').val());
    if(project == "" || task == "") {
      $('#pYourFirstProject').append(" <br />requires <br />Project Name and Task Name");
      return false;
    } else {
      $('#Project__eventAddProjectAndTask').submit();
    }
  });
});
</script>

<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11"><?php echo _('Your first project');?></span>
        </div>
    </div>
    <div class="contentfull">        
      <div class="messageshadow">
	<div class="messages" style="font-size:1.8em;">Ofuz Getting started wizard</div>
      </div>
<?php
$e_project = new Event("Project->eventAddProjectAndTask");
$e_project->addParam("goto", "ww_s3.php");
echo $e_project->getFormHeader();
echo $e_project->getFormEvent();
?>
      <div align="center" style="font-size:1.4em;">
      <p id="pYourFirstProject">Your first project</p>

	<div id="your_first_project">
	  <div class="spacerblock_20"></div>
	  <div>Project Name</div>
	  <div><input class="txtboxStyle1" type="text" id="project" name="project" value="" /></div>
	  <div class="spacerblock_10"></div>
	  <div>Task Name</div>
	  <div><input class="txtboxStyle1" type="text" id="task" name="task" value="" /></div>
	</div>
      <div class="spacerblock_40"></div>
      <div>
	<a id="create" href="javascript:;"><img src="/images/create.jpg" border="0" /></a> <br />
	<a href="index.php" title="">Skip >></a>
      </div>
</form>
      <div class="spacerblock_80"></div>

      <div class="layout_footer"></div>

     </div>
</td><td class="layout_rmargin"></td></tr></table>
</body>
</html>