<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Twitter Setup';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
?>
<?php //$do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php //$thistab = ''; include_once('includes/ofuz_navtabs.php'); ?>
<?php //$do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>

    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr>
        <div class="banner50 pad020 text16 fuscia_text"><?php echo _('Twitter Setup'); ?></div>
        <div class="contentfull">
        <?php
            $do_twitter = new OfuzTwitter();
            $do_twitter->sessionPersistent('do_twitter', 'signout.php', 36000);
            $TokenExists = $do_twitter->checkTwitterIntegration();
            if ($TokenExists) {
                echo _('Your Ofuz account is integrated with your Twitter account: '),$TokenExists;
                echo '<div class="spacerblock_20"></div>',"\n";
                echo '<br />'._('Click').' <a href="tw_import_friends.php">'._('HERE').'</a> '._('to import your Twitter contacts.');
                echo '<br /><br />'._('Click').' <a href="tw_import_followers.php">'._('HERE').'</a> '._('to import the people following you on Twitter.');
            } else {
                echo _('Your account is not yet integrated with Twitter. ');
                echo _('Please click ');
                echo '<a href="tw_connect.php">',_('here'),'</a>';
                echo _(' to allow Ofuz to access your Twitter data.');
            }
         ?>
        </div>
        <div class="spacerblock_20"></div>
        <div class="solidline"></div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php //include_once('includes/ofuz_facebook.php'); ?>
<?php //include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
