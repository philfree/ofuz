<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

  $pageTitle = 'Ofuz :: List Public Projects';
  $Author = 'SQLFusion LLC';
  $Keywords = 'Keywords for search engine';
  $Description = 'Description for search engine';
  $background_color = 'white';
  include_once('config.php');
  include_once('includes/ofuz_check_access.script.inc.php');
  include_once('includes/header.inc.php');
     
?>

 
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="contentfull">
	<div class="mainheader">
        <div class="pad20">
            <span class="headline14">Public Projects</span>
        </div>
    </div>
	<table id="report_user_usage">
	<tbody>
	<?php
		$do_prj = new Project();
		$total_public_prjs = $do_prj->getTotalPublicProjects();
	?>
		<tr class="total_users"><td colspan="2">Total Public Projects: <?php echo $total_public_prjs; ?></td></tr>
		<tr class="report_heading">
   <td>Project Name</td>
			<td>Status</td>
		</tr>
<?php
  $count = 1;
  $do_prj->getAllPublicProjects();
  while($do_prj->next()) {
    $class = ($count%2 == 0) ? "even" : "odd";
?>

    <tr class="<?php echo $class; ?>">
      <td><a href="/PublicProject/<?php echo $do_prj->getData("idproject"); ?>"><?php echo $do_prj->getData("name") ; ?></a></td>
      <td><?php echo $do_prj->getData("status")?></td>
    </tr>

	<?php $count++; } ?>
	</tbody>
	</table>

        <div class="spacerblock_80"></div>
    </div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>
