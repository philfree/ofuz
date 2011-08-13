<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Contact Import';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    $do_sync = new Sync($GLOBALS['conx']);
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
            <span class="headline11"><?php echo _('Import Contacts');?></span>
        </div>
    </div>
    <div class="contentfull">
        <div class="import_head2"><?php echo _('Step 1 of 2: ');?><span class="import_head3"><?php echo _('Select the .CSV File');?></span></div>
        <div class="import_cont1">
            <?php echo _('Ofuz supports importing records from .csv');?>(<span class="import_cont2"><?php echo _('Comma Separated Values');?></span>) <?php echo _('files'); ?>.
            <?php echo _('To start import, browse to locate file .csv file and click on the Next button to continue.');?>
        </div>
<?php

        //$upload_csv = $_SESSION['do_contact_import']->newForm('do_contact_import->eventCsvUpload');
        if($_SESSION['csv_file']){
            unset ($_SESSION['csv_file']);
        }
        $upload_csv = new Event("ContactImport->eventCsvUpload"); 
        $upload_csv->addParam("goto","contact_import_parse.php");
        if($_SESSION["page_from"] == 'reg'){
            $upload_csv->addParam("fromReg","Yes");
        }else{
            $upload_csv->addParam("fromReg","No");
        }
        $upload_csv->setGotFile(true);
        $upload_csv->setSecure(true);

        $htmlform = $upload_csv->getFormHeader();
        $htmlform .= $upload_csv->getFormEvent();
        $htmlform .= '<div class="import_cont3"><b>File Location: &nbsp; </b>';
        $htmlform .= '<input type="file" name="fields[contact_csv]"></div>';
        $htmlform .= '<div class="import_cont3"><b>Set Tag <input type="text" name="fields[import_tag]" value="import_'.date("Y-m-d").'"> on the imported contacts.</b></div>';
        $htmlform .= '<div class="import_cont3">'.$upload_csv->getFormFooter("Next").'</div>';
        $htmlform .= "\n";
        echo $htmlform;

?>
<!--
        <form action="contact_import_parse.php" method="post" enctype="multipart/form-data">
            <div class="import_cont3"> <span class="import_cont4">File Location: </span><input name="uploadedfile" type="file" /></div>
            <div class="import_next"><input type="submit" value="Next" /></div>
        </form>
-->
    <?php if($_GET['message']){ echo '<div class="import_msg1">'.htmlentities($_GET['message']).'</div>';}?>
    </div>

    <div class="spacerblock_80"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>