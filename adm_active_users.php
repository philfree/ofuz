<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

  $pageTitle = 'Ofuz :: Active Users';
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
                <span class="headline14">Active User Report</span>
            </div>
        </div>
        <table id="report_user_usage">
        <tbody>
        <?php
            $do_user = new User();
            $active_users = $do_user->getTotalActiveUsers();
        ?>
            <tr class="total_users"><td colspan="9">Active Users: <?php echo $active_users; ?></td></tr>
            <tr class="report_heading">
                <td>Id User</td>
                <td>User</td>
                <td>Total Contacts</td>
                <td>Total Notes</td>
                <td>Total Projects</td>
                <td>Total Tasks</td>
                <td>Total Discussions</td>
                <td>Total Invoices</td>
                <td>Last Login</td>
            </tr>
        <?php  

        $do_report = new ReportUserUsage();
        $do_report->getActiveUsersReport();
        $count = 1;
        while($do_report->next()) {

            $class = ($count%2 == 0) ? 'even' : 'odd';
		
        ?>

            <tr class="<?php echo $class; ?>">
                <td><?php echo $do_report->getData('iduser') ; ?></td>
                <td><?php echo $do_report->getData('firstname').' '.$do_report->getData('middlename').' '.$do_report->getData('lastname') ; ?></td>
                <td><?php echo $do_report->getData('total_contacts') ; ?></td>
                <td><?php echo $do_report->getData('total_notes') ; ?></td>
                <td><?php echo $do_report->getData('total_projects') ; ?></td>
                <td><?php echo $do_report->getData('total_tasks') ; ?></td>
                <td><?php echo $do_report->getData('total_discussion') ; ?></td>
                <td><?php echo $do_report->getData('total_invoices') ; ?></td>
                <td><?php echo $do_report->getData('last_login') ; ?></td>
            </tr>

        <?php
            $count++;
        }
        ?>
        </tbody>
        </table>

        <div class="spacerblock_80"></div>
    </div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
</body>
</html>