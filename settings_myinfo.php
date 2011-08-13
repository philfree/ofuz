<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz :: My Information';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
?>
<?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = ''; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn settingsbg">
        <div class="settingsbar"><div class="spacerblock_16"></div>
            <?php
		$GLOBALS['thistabsetting'] = 'My Profile';
		include_once('includes/setting_tabs.php');
             ?>
        <div class="settingsbottom"></div></div>
    </td><td class="layout_rcolumn">
        <div class="banner60 pad020 text32"><?php echo _('Settings'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text"><?php echo _('My Profile'); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/contact_edit.php">edit</a></div>
        <div class="contentfull">
	<div class="messageshadow">
            <div class="messages">
            <?php
				$msg = new Message(); 
				echo $msg->getMessage('my profile instruction');
				//echo '<br /><br />';
				//echo _('Your Profile Page is : '). $GLOBALS['cfg_ofuz_site_http_base'].'profile/'.$_SESSION['do_User']->username;
            ?>
            </div>
        </div><br />
	<div class="instruction_copy_past">
	 <?php
	      echo '<a href="'.$GLOBALS['cfg_ofuz_site_http_base'].'profile/'.$_SESSION['do_User']->username.'" target="_blank">'.$GLOBALS['cfg_ofuz_site_http_base'].'profile/'.$_SESSION['do_User']->username.'</a>';
	      $url = $GLOBALS['cfg_ofuz_site_http_base'].'profile/'.$_SESSION['do_User']->username;
	      $url = urlencode($url);
	      echo '<div>';
	      echo '<img src="http://chart.apis.google.com/chart?chs=300x300&amp;cht=qr&amp;chl='.$url.'" alt="QR code for this URL" width="300" height="300" />';
	      echo '</div>';
	 ?>
	</div>
        <?php 
	   $do_contact = new Contact();
           if(!empty($_SESSION['do_User']->idcontact) && $_SESSION['do_User']->idcontact != 0){
		$do_contact->getId($_SESSION['do_User']->idcontact);
	   }else{
		// Add a new contact and update the idcontact to the user table for the user
		$do_contact->addNew();
		$do_contact->firstname = $_SESSION['do_User']->firstname ;
		$do_contact->lastname = $_SESSION['do_User']->lastname ;
		$do_contact->iduser = $_SESSION['do_User']->iduser ;
		$do_contact->add();
		$idcontact_inserted = $do_contact->getPrimaryKeyValue();
		$do_contact_view = new ContactView();
		$do_contact_view->setUser($_SESSION['do_User']->iduser);
		$do_contact_view->addFromContact($do_contact);
		$_SESSION['do_User']->idcontact = $idcontact_inserted;
		$_SESSION['do_User']->updateUserContact($idcontact_inserted);
		$do_contact->getId($idcontact_inserted);
	   }
	   $_SESSION['from_page'] = 'settings_myinfo.php';
	   $do_contact->sessionPersistent("ContactEditSave", "contact.php", OFUZ_TTL);
	    
         ?>
	  <div class="vpad10">

                <?php
		      
		    echo $_SESSION['ContactEditSave']->firstname.' '.$_SESSION['ContactEditSave']->lastname.'<br /><br />';
                    $ContactPhone = $_SESSION['ContactEditSave']->getChildContactPhone();
                    if($ContactPhone->getNumRows()){
                        echo '<b>'._('Phone').'</b><br />';
                        while($ContactPhone->next()){
                            echo $ContactPhone->phone_type.': ';
                            echo $ContactPhone->phone_number;
                            echo '<br />';
                        }
                    }

                    $ContactEmail = $_SESSION['ContactEditSave']->getChildContactEmail();
                    if($ContactEmail->getNumRows()){
                        echo '<b>'._('Email').'</b><br />';
                        while($ContactEmail->next()){
                            echo '<a href="mailto:'.$ContactEmail->email_address.'" title="'.$ContactEmail->email_type.'">'.$ContactEmail->email_address.'</a>';
                            //echo $ContactEmail->email_type.': ';
                            //echo $_SESSION['ContactEditSave']->formatTextDisplay($ContactEmail->email_address);
                            echo '<br />';
                        }
                    }

                    $ContactInstantMessage = $_SESSION['ContactEditSave']->getChildContactInstantMessage();
                    if($ContactInstantMessage->getNumRows()){
                        echo '<b>'._('IM').'</b><br />';
                        while($ContactInstantMessage->next()){
                            //echo $ContactInstantMessage->im_options.': ';
                            echo $ContactInstantMessage->im_type.': ';
                            echo $ContactInstantMessage->im_username;
                            echo '<br />';
                        }
                    }

                    $ContactWebsite = $_SESSION['ContactEditSave']->getChildContactWebsite();
                    if($ContactWebsite->getNumRows()){
                        echo '<b>'._('Website').'</b><br />';
                        while($ContactWebsite->next()){
                            echo $ContactWebsite->getDisplayLink();
                            echo "<br/>";
                        }
                    }

                    $ContactAddress = $_SESSION['ContactEditSave']->getChildContactAddress();
                    if ($ContactAddress->getNumRows()) {
			echo '<b>'._('Address').'</b>:<br/>';
			while($ContactAddress->next()) {
				echo $ContactAddress->address_type . "<br>";
                                echo nl2br($ContactAddress->address) . "<br>";
			}
		    }

                 ?>


        </div>
        <div class="dottedline"></div>

        </div>
        <div class="solidline"></div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php include_once('includes/ofuz_facebook.php'); ?>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>