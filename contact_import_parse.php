<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Contact Import';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    $do_sync = new Sync($GLOBALS['conx']);
?>
<script type="text/javascript">
    //<![CDATA[
    function fnEnterEmail(ref, act) {
        $.ajax({
            type: "GET",
<?php
$e_emailForm = new Event("Sync->eventAjaxEnterEmailForm");
$e_emailForm->setEventControler("ajax_evctl.php");
$e_emailForm->setSecure(false);
?>
            url: "<?php echo $e_emailForm->getUrl(); ?>",
            data: "referrer="+ref+"&act="+act,
            success: function(html){
                $("#"+ref+act)[0].innerHTML = html;
                $("#"+ref+act).toggle(0);
            }
        });
    }
    //]]>
</script>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11"><?php echo _('Match Fields');?></span>
        </div>
    </div>

    <div class="contentfull">
    <div class="import_head2"><?php echo _('Step 2 of 2: ');?><span class="import_head3"><?php echo _('Map your CSV fields with Database Fields.');?></span></div>
    <?php 
		$csv_file = $_SESSION['csv_file'];
		$target_path = 'files/' . $csv_file;
		$do_contact = new Contact($GLOBALS['conx']);
		$fields_arr = $do_contact->getDbFieldNames();
		$combo_box = "<option value=''>----Select----</option>";
		foreach($fields_arr as $key=>$val){
		  $combo_box .= "<option value='$val'>".$key."</option>";
		}

		$upload_csv = new Event("ContactImport->eventImportContactsFromCsv"); 
		$upload_csv->setLevel(20);
                if($_SESSION["page_from"] == 'reg'){//If importing while registration
                  $upload_csv->addParam("goto","import_contacts.php");
                }else{
		  $upload_csv->addParam("goto","contact_import.php");
                }
		$upload_csv->addParam("targetpath",$target_path);
		$upload_csv->addParam("iduser",$_SESSION['do_User']->iduser);
		//$upload_csv->addEventAction("ContactView->eventRebuildContactUserTable", 30);
		//$upload_csv->setGotFile(true);
		$upload_csv->setSecure(true);

		echo $upload_csv->getFormHeader();
		echo $upload_csv->getFormEvent();
    ?>
        <table class="import_table">
        <?php
            $handle = fopen($target_path, "r");
            $data = fgetcsv($handle);
            $num = count($data);
            for ($c=0; $c < $num; $c++) {
                echo '<tr><td>',$data[$c],'</td><td><select name="fields[',$c,']">',$combo_box,'</select></td></tr>',"\n";
            }
            fclose($handle);
        ?>
        </table>
        <?php echo $upload_csv->getFormFooter(_('Import'));?>
<!--
        <input type="submit" name="submitaction" value="Import" />
        </form>
-->
    </div>
    <div class="spacerblock_80"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>