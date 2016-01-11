<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  $pageTitle = 'Ofuz :: User Metrics';
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
                <span class="headline14">User Metrics</span>
            </div>
        </div>
        <div class="spacerblock_20"></div>

        <?php
            $do_user = new User();
            $user_count = $do_user->getTotalUsers();
        ?>
        1. Total Users: <?php echo $user_count; ?><br /><br />

        <?php
            $user_count = $do_user->getUsersRegisteredThisPastWeek();
        ?>
        2. New Registrations In The Past Week: <?php echo $user_count; ?><br />
        <span class="adm_report_explanation">** Excluding today</span>
        <br /><br />

        <?php
            $user_count = $do_user->getUsersRegisteredYesterday();
        ?>
        3. New Registrations Yesterday: <?php echo $user_count; ?><br /><br />

        <?php
            $user_count = $do_user->getTotalActiveUsers();
        ?>
        4. Total Active Users: <?php echo $user_count; ?><br />
        <span class="adm_report_explanation">** Registered more than 30 days ago and had a login in the past 7 days</span>
        <br /><br />

        <?php
            $user_count = $do_user->getTotalNewActiveUsers();
        ?>
        5. Total New Active Users: <?php echo $user_count; ?><br />
        <span class="adm_report_explanation">** Registered more than 7 days ago and had a login in the past 7 days, excluding the registration day</span>
        <br /><br />

        <?php
            $user_count = $do_user->getUsersLoggedInYesterday();
        ?>
        6. Total Users That Logged In Yesterday: <?php echo $user_count; ?><br /><br />

        <div class="spacerblock_80"></div>
    </div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
</body>
</html>