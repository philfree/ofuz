<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: vCard Import';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    //$do_sync = new Sync($GLOBALS['conx']);
    if (isset($_GET['ref']) && $_GET['ref'] == 'reg') {
      $ref = $_GET['ref'];
      $_SESSION["page_from"] = $ref;
  }
?>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11"><?php echo _('Import Contacts from vCard');?></span>
        </div>
    </div>
    <div class="contentfull">
        <div class="import_head2"><span class="import_head3"><?php echo _('Select your vCard File');?></span></div>
        <div class="import_cont1">
	    <?php 
		echo _('Ofuz supports importing contacts from vCard.');
		echo '<br />';
		echo _('To start import, browse to locate your file and click on the Import button.');
	    ?>
            
            
        </div>
<?php
        $import_vcard = new Event("VBook->eventVCardImport");
		$import_vcard->setLevel(20);
        $import_vcard->addParam("goto","vcard_import.php");
        if($_SESSION["page_from"] == 'reg'){
            $import_vcard->addParam("fromReg","Yes");
        }else{
            $import_vcard->addParam("fromReg","No");
        }
		$import_vcard->addParam("iduser",$_SESSION['do_User']->iduser);
		////$import_vcard->addEventAction("ContactView->eventRebuildContactUserTable", 30);
        $import_vcard->setGotFile(true);
        $import_vcard->setSecure(true);

        $htmlform = $import_vcard->getFormHeader();
        $htmlform .= $import_vcard->getFormEvent();
        $htmlform .= '<div class="import_cont3"><b>'._('File Location: ').'&nbsp; </b>';
        $htmlform .= '<input type="file" name="fields[contact_vcard]"></div>';
        $htmlform .= '<div class="import_cont3"><b>'._('Set Tag ').'<input type="text" name="fields[import_tag]" value="import_vCard_'.date("Y-m-d").'">'._(' on the imported contacts.').'</b></div>';
        $htmlform .= '<div class="import_cont3">'.$import_vcard->getFormFooter("Import").'</div>';
        $htmlform .= "\n";
        echo $htmlform;

?>
    <?php if($_GET['msg']){ echo '<div class="import_msg1">'.htmlentities($_GET['msg']).'</div>';}?>
    </div>

    <div class="spacerblock_80"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>