<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/*
 *      contact_sendemail_save_tempatle.php
 *      
 *      Copyright 2009 SQLFusion LLC, Philippe Lewicki <philippe@sqlfusion.com>
 *      
 */


    include_once('config.php');
    $pageTitle = _('Ofuz :: Contacts Send Email Save Template');
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
		
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

    $do_message = new EmailTemplateUser("blank");
 
     if ($_GET['idtemplate']) {
          $do_message->getId( (int)$_GET['idtemplate'] ); 
     }

?>


<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">


<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); 

 $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11"><?php echo _('Save Template');?></span>
        </div>
    </div>
    <div class="contentfull">
		<div class="messageshadow">
            <div class="messages">
            <?php
				$msg = new Message(); 
				echo $msg->getMessage('save as template');
				echo "<br>".$_SESSION['in_page_message'] ; 
				$_SESSION['in_page_message'] = '';
            ?>
            </div>
        </div>
<?php 
  
	if (strlen(trim($_SESSION['do_message']->name)) > 2) {
		$_SESSION['do_message']->newUpdateForm();
	} else {
		$_SESSION['do_message']->newAddForm();
	}
	$_SESSION['do_message']->setLogRun(true);
	//$_SESSION['do_message']->form->addEventAction("mydb.gotoPage", 1200);
	$_SESSION['do_message']->form->goto = 'contacts.php';
	$_SESSION['do_message']->setFields(New Fields('emailtemplate_user'));

    echo $_SESSION['do_message']->form->getFormHeader();
	echo $_SESSION['do_message']->form->getFormEvent();
	
	//echo $_SESSION['do_message']->getPrimaryKeyValue();
	
	echo _('Name for this email template:  ').$_SESSION['do_message']->name;

    //echo "--".$_SESSION['do_message']->name."--";
	//print_r($_SESSION['do_message']->getValues());
	
    $_SESSION['do_message']->setApplyFieldFormating(false);
    if (strlen(trim($_SESSION['do_message']->name)) > 2) {
		echo $_SESSION['do_message']->form->getFormFooter(_('Update'));
		echo '<div align="right"><a href="/contacts.php">'._('or click here to continue').'</a></div>';
	} else {
		echo $_SESSION['do_message']->form->getFormFooter(_('Save'));?>
		  <div align="right">
    <?php 
        $e_clean_template = new Event('do_message->eventDelete');
		$e_clean_template->addEventAction("mydb.gotoPage", 1200);
		$e_clean_template->goto = "contacts.php";
        echo $e_clean_template->getLink(_(' or click here to continue without saving'));
	
	?>
  </div>
    <?php
		
	}
?> 



    </div>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
