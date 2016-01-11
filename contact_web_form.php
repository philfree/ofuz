<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/*
 *      Web form creator
 *      This will let the user selec the fields that will display 
 *      in the web form. He will also add initial default tags.
 *      
 *      Copyright 2009 SQLFusion LLC, Philippe Lewicki <philippe@sqlfusion.com>
 *      
 */


    include_once('config.php');
    $pageTitle = _('Ofuz :: Contacts Web Form Creator');
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
		
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

?>


<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">


<?php $thistab = 'Contacts'; include_once('includes/ofuz_navtabs.php'); 

 $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <div class="mainheader">
        <div class="pad20">
            <span class="headline11"><?php echo _('Web Form Builder'); ?></span>
        </div>
    </div>
    <div class="contentfull">
		<div class="messageshadow">
            <div class="messages">
            <?php
				$msg = new Message(); 
				echo $msg->getMessage('web form creator instruction');
            ?>
            </div>
        </div>
     <?php
	     $do_userform = new WebFormUser();
		 $do_userform->sessionPersistent('do_userform', 'contacts.php', OFUZ_TTL);
		 $do_userform->iduser = $_SESSION['do_User']->iduser;
		 $do_userform->newAddForm('do_userform');
		 $do_userform->form->addEventAction('do_userform->eventAddFormFields', 2103);
		 $do_userform->form->goto = 'contact_web_form_url.php';
		 echo $do_userform->form->getFormHeader();
		 echo $do_userform->form->getFormEvent();
		 
		 echo '<b>'._('Form Title').':</b>'. $do_userform->title;
		 
		 echo '<br/><b>'._('Form Description').'</b><br/>'.$do_userform->description;
                 $do_webform = new WebFormField();
		 $do_webform->getAll('display_order');
                echo $do_webform->displayFields();
		 echo '<br/><b>'._('Tags for those contacts').':</b></b> '.$do_userform->tags;
		 echo '<br/><b>'._('Web address to take the user after submitting the form').':</b> '.$do_userform->urlnext;
		 echo '<br/><b>'._('Receive an email alert when someone submit the form').':</b> '.$do_userform->email_alert;
		 echo '<div align="right">'.$do_userform->form->getFormFooter(_('Create')).'</div>';
	 ?>
	   
    </div>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>
