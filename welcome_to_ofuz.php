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

		<br/><br/>
        <div class="indent30" style="line-height:20px;">
            <div class="text34 text_bold">
            <?php echo _('Welcome To Ofuz'); ?>
            </div>
            <br/>
            <hr style="height:1px;border:none;background-color:#ccc;margin-bottom:10px"/>
            
            <p style="margin-left:5px;">The full business cycle in one application.<br/>Created for teams, freelancers, and service providers.</p>
        </div>

	<br/>
    <div class="indent30 headline_fuscia">Hi <?php echo $_SESSION['do_User']->firstname; ?>. &nbsp;<?php echo _('Get started here:');?></div>
    <table><tr>
        <td class="layout_col180"><div class="center_text">
            <a href="/contact_add.php"><img src="/images/icon_contact_150.png" width="150" height="150" alt="" /></a><br />
            <a href="/contact_add.php"><?php echo _('Add A Contact'); ?></a>
        </div></td>
        <td class="layout_col50">&nbsp;</td>
        <td class="layout_col180"><div class="center_text">
            <a href="/projects.php"><img src="/images/icon_project_150.png" width="150" height="150" alt="" /></a><br />
            <a href="/projects.php"><?php echo _('Create A Project'); ?></a>
        </div></td>
        <td class="layout_col50">&nbsp;</td>
        <td class="layout_col180"><div class="center_text">
            <a href="/invoice_add.php"><img src="/images/icon_invoice_150.png" width="150" height="150" alt="" /></a><br />
            <a href="/invoice_add.php"><?php echo _('Send An Invoice'); ?></a>
        </div></td>
    </tr></table>
    <div class="indent30">
        <div class="spacerblock_40"></div>
      
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
