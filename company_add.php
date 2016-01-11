<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: Add a new company';
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
    $(document).ready(function() {
        $("#addinfo").click(function() {
            $("#addinfo").hide(0);
            $("#contactinfo").show(0);
        });
    });
    //]]>
</script>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn">
        &nbsp;
    </td><td class="layout_rcolumn">
        <div class="mainheader">
            <div class="pad20">
                <h1><?php echo _('Add a new company'); ?></h1>
            </div>
        </div>
        <div class="contentfull">
            <?php
                $CompanyEdit  = new Company($GLOBALS['conx']);
                $CompanyEdit->sessionPersistent("CompanyEditSave", "index.php", 300);
                $company_edit_page = "company_edit.php";
                $companyAddForm = new ReportForm($conx,"ofuz_add_company");
                $companyAddForm->setFormEvent("CompanyEditSave->eventAdd",123);
                $companyAddForm->addEventAction("CompanyEditSave->eventValuesFromForm", 117);
                $companyAddForm->addEventAction("CompanyEditSave->eventCheckDuplicateCompanyInAdd",100);
                $companyAddForm->addEventAction("mydb.gotoPage", 90);
                $companyAddForm->addParam("goto", $company_edit_page);
                $companyAddForm->addParam("errPage", $_SERVER['PHP_SELF']);
                $companyAddForm->setRegistry("ofuz_add_company");
                $companyAddForm->setTable("company");
                $companyAddForm->setAddRecord();
                $companyAddForm->setUrlNext($company_edit_page);
                $companyAddForm->setForm();
                $companyAddForm->execute();
            ?>
            <div class="section20">
               <!-- <input type="submit" value="Add this company" />-->
            </div>
            <?php //echo $do_contact->getFormFooter(); ?>
        </div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>