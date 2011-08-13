<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

  $pageTitle = 'Ofuz :: User Management';
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
            <span class="headline14">User Management</span>
        </div>
    </div>
	<table id="report_user_usage">
	<tbody>
	<?php
		$do_user = new User();
		$total_users = $do_user->getTotalUsers();
	?>
		<tr class="total_users"><td colspan="9">Total Users: <?php echo $total_users; ?></td></tr>
		<tr class="report_heading">
   <td>Id User</td>
			<td>Name</td>
			<td>Email</td>
			<td>Status</td>
			<td>Plan</td>
			<td align="center">Action</td>
		</tr>
<?php
  $count = 1;
  $do_user->getALL();
  while($do_user->next()) {
    $class = ($count%2 == 0) ? "even" : "odd";
?>

    <tr class="<?php echo $class; ?>">
      <td><?php echo $do_user->getData("iduser") ; ?></td>
      <td><?php echo $do_user->getData("firstname")." ".$do_user->getData("middlename")." ".$do_user->getData("lastname") ; ?></td>
      <td><?php echo $do_user->getData("email") ; ?></td>
      <td><?php echo $do_user->getData("status") ; ?></td>
      <td><?php echo $do_user->getData("plan") ; ?></td>
      <td>
        <?php
          if($do_user->getData("status") == "suspend") {
            $e_suspend = new Event("UserInternalMarketing->eventUnsuspendUser");
            $e_suspend->addParam("goto", $_SERVER["PHP_SELF"]);
            $e_suspend->addParam("iduser", $do_user->getData("iduser"));
            echo $e_suspend->getLink("un-suspend",' title="'._('un-suspend this User').'" onclick="if (!confirm(\''._('Do you really want to un-suspend this User ?').'\')) return false;"');
            echo " | ";
          } else {
            $e_suspend = new Event("UserInternalMarketing->eventSuspendUser");
            $e_suspend->addParam("goto", $_SERVER["PHP_SELF"]);
            $e_suspend->addParam("iduser", $do_user->getData("iduser"));
            echo $e_suspend->getLink("Suspend",' title="'._('Suspend this User').'" onclick="if (!confirm(\''._('Do you really want to suspend this User ?').'\')) return false;"');
            echo " &nbsp;&nbsp;&nbsp;&nbsp;| ";
          }
        ?>

        <?php
          $e_delete = new Event("UserInternalMarketing->eventDeleteUser");
          $e_delete->addParam("goto", $_SERVER["PHP_SELF"]);
          $e_delete->addParam("iduser", $do_user->getData("iduser"));
          echo $e_delete->getLink("Delete", ' title="'._('Delete this User').'" onclick="if (!confirm(\''._('Do you really want to delete this User ?').'\')) return false;"');
        ?>
      </td>
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
