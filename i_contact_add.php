<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: Add a new contact';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/i_header.inc.php');
?>
<?php $thistab = 'Contacts'; include_once('i_ofuz_navtabs.php'); ?>
<div class="mobile_main">
                <div class="mainheader">
                    <div class="mobile_head_pad5">
                        <h1>Add a new contact</h1>
                    </div>
                </div>
                        <div class="mobile_head_pad5">
                        <?php 
                            $ContactEdit  = new Contact($GLOBALS['conx']);
                            $ContactEdit->sessionPersistent("ContactEditSave", "index.php", 3600);
                            $contact_edit_page = "i_contact_edit.php";
                            $contactAddForm = $_SESSION['ContactEditSave']->prepareSavedForm("i_ofuz_add_contact");
                            $contactAddForm->setFormEvent("ContactEditSave->eventAdd",300);
                            //$contactAddForm->setFormEvent("ContactEditSave->eventSetCompany",120);
                            $contactAddForm->addEventAction("mydb.gotoPage", 453);
                            $contactAddForm->addParam("goto", $contact_edit_page);
                            $contactAddForm->setRegistry("i_ofuz_add_contact");
                            $contactAddForm->setTable("contact");
                            $contactAddForm->setAddRecord();
                            $contactAddForm->setUrlNext($contact_edit_page);
                            $contactAddForm->setForm();
                            $contactAddForm->execute();
                        ?>
                    </div>
                    <div class="bottompad40"></div>
<?php include_once('i_ofuz_logout.php'); ?>
 </div>
</body>
</html>