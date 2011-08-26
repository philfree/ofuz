<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Welcome To Ofuz';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
    $_SESSION["page_from"] = '';
?>
<script type="text/javascript">
    //<![CDATA[
      function doWait(){
        document.getElementById('waitimage').style.display = 'block';
      }
    //]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Welcome'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>

    <div class="welcome_banner">
        <div class="indent30">
            <div class="banner80_mid text34 text_bold">
            <?php echo _('Welcome To Ofuz'); ?>
            </div>
            The full business cycle in one application. Created for teams, freelancers, and service providers.
        </div>
    </div>

    <div class="indent30 headline_fuscia">Hi <?php echo $_SESSION['do_User']->firstname; ?>. &nbsp;<?php echo _('Get started here:');?></div>
    <table><tr>
        <td class="layout_col180"><div class="center_text">
            <a href="/contacts.php"><img src="/images/icon_contact_150.png" width="150" height="150" alt="" /></a><br />
            <a href="/contacts.php"><?php echo _('Add A Contact'); ?></a>
        </div></td>
        <td class="layout_col50">&nbsp;</td>
        <td class="layout_col180"><div class="center_text">
            <a href="/projects.php"><img src="/images/icon_project_150.png" width="150" height="150" alt="" /></a><br />
            <a href="/projects.php"><?php echo _('Create A Project'); ?></a>
        </div></td>
        <td class="layout_col50">&nbsp;</td>
        <td class="layout_col180"><div class="center_text">
            <a href="/invoices.php"><img src="/images/icon_invoice_150.png" width="150" height="150" alt="" /></a><br />
            <a href="/invoices.php"><?php echo _('Send An Invoice'); ?></a>
        </div></td>
    </tr></table>
    <div class="indent30">
        <div class="spacerblock_40"></div>
        <div class="headline_fuscia"><?php echo _('Make it easy by importing your contacts:');?></div>
            <a href="/gSync.php?ref=reg"><img src="images/Google_Logo.gif" width="80" height="30" alt="" class="mright_30" /></a>
            <a href="/fb_connect.php?ref=reg" onclick="doWait();" ><img src="images/Facebook_Logo.jpg" width="80" height="30" alt="" class="mright_30" /></a>
			<a href="/settings_twitter.php?ref=reg"><img src="images/twitter.png" width="150" alt="" class="mright_30" /></a>			          
            <a href="/contact_import.php?ref=reg"><img src="images/Csv_Logo.gif" width="48" height="48" alt="" /></a>
         <div class="spacerblock_40"></div>
         <div class="headline_fuscia"><?php echo _('Or, jump straight in:');?></div>
         <div class="spacerblock_10"></div>
         <a href="/index.php">&#171; <?php echo _('Skip Importing Contacts.'),' &nbsp;',_('Get Started Now.'); ?> &#187;</a>
         <div class="spacerblock_20"></div>
          
        <div class="dottedline"></div>
		<!--
        <?php $footer_note = 'emailstream'; include_once('includes/footer_notes.php'); ?>
         //-->
        <!-- Add ofuz to Browser Search option begins -->
        
        <script src="/browser_search/browser_detect.js" type="text/javascript"></script>

        
        <div style="text-align:center;cursor:pointer">
            <script src="/browser_search/browser_functions.js" type="text/javascript"></script>
        </div>

        <!-- Add ofuz to Browser Search option ends -->
    </div>
    <div class="spacerblock_20"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>
