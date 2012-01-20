<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2012 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/*
 *      contact_sendemail_save_tempatle.php
 *      
 *      Copyright 2009-2012 SQLFusion LLC, Philippe Lewicki <philippe@sqlfusion.com>
 *      
 */

    $do_message = new EmailTemplateUser("blank");
 
     if ($_GET['idtemplate']) {
          $do_message->getId( (int)$_GET['idtemplate'] ); 
     }

?>

		<div class="messageshadow">
            <div class="messages">
            <?php
                $message = new Message();
                $message->getMessage("save as template");
                $message->displayMessage();
                if (!empty($_SESSION['in_page_message'])) {
						$in_page_message = new Message();
						$in_page_message->setContent($_SESSION['in_page_message'] );
						$in_page_message->displayMessage();
						$_SESSION['in_page_message'] = '';
				}
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
