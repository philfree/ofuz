<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Add a new contact';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
?>
<script type="text/javascript">
    //<![CDATA[
    //]]>
</script>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
        <div class="left_text_links">
            <div class="headbar"><?php echo _('Other ways to add people'); ?></div>
            <!--
            <span class="linkbluebg"><a href="#">Import from Basecamp</a></span><br />
            <span class="linkbluebg"><a href="#">Import from Outlook</a></span><br />
            <span class="linkbluebg"><a href="#">Import from ACT!</a></span><br />-->
			<span class="linkbluebg"><a href="vcard_import.php"><?php echo _('Import from vCard'); ?></a></span><br />
            <span class="linkbluebg"><a href="contact_import.php"><?php echo _('Import from CSV');?></a></span><br />
            <span class="linkbluebg"><a href="sync.php"><?php echo _('Import from Google'); ?></a></span><br />
            <span class="linkbluebg"><a href="fb_import_friends.php"><?php echo _('Import from Facebook');?></a></span><br />
            <span class="linkbluebg"><a href="contact_web_form.php"><?php echo _('Create a Web Form');?></a></span><br />			
        </div>
    </td><td class="layout_rcolumn">
        <div class="mainheader">
            <div class="pad20">
                <h1><?php echo _('Add a new contact');?></h1>
            </div>
        </div>
        <div class="contentfull">
                        <?php 
                            $ContactEdit  = new Contact($GLOBALS['conx']);
                            $ContactEdit->sessionPersistent("ContactEditSave", "index.php", 3600);
                            $contact_edit_page = "contact_edit.php";
                            $contactAddForm = $_SESSION['ContactEditSave']->prepareSavedForm("ofuz_add_contact");
                            $contactAddForm->setFormEvent("ContactEditSave->eventAdd",300);
                            $contactAddForm->event->setGotFile(true);
                            //$contactAddForm->setFormEvent("ContactEditSave->eventSetCompany",120);
							$contactAddForm->addEventAction("ContactEditSave->eventAddWebView", 351);
							$contactAddForm->addEventAction("ContactTeam->eventAddContactToTeamCW", 380);							
                            $contactAddForm->addEventAction("mydb.gotoPage", 453);
                            $contactAddForm->addParam("goto", $contact_edit_page);
                            $contactAddForm->setRegistry("ofuz_add_contact");
                            $contactAddForm->setTable("contact");
                            $contactAddForm->setAddRecord();
                            $contactAddForm->setUrlNext($contact_edit_page);
                            $contactAddForm->setForm();
                            $contactAddForm->execute();
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
