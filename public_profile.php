<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

  $pageTitle = 'Ofuz :: User Profile';
  $Author = 'SQLFusion LLC';
  include_once('config.php');

  if (is_object($_SESSION['do_User'])) {
    try {
      if (!$_SESSION['do_User']->iduser) {
        unset($_SESSION['do_User']);
        header("Location: ".$GLOBALS['cfg_ofuz_site_http_base'].$_SERVER['REQUEST_URI']);
      }
    } catch (Exception $e) { 
      unset($_SESSION['do_User']);
      header("Location: ".$GLOBALS['cfg_ofuz_site_http_base'].$_SERVER['REQUEST_URI']);
    }
  }

  $_SESSION["from_page"] = "public_profile";

  if ($_GET['u']) {
    $do_contact = new Contact();
    $idcontact = $do_contact->getContactByUsername($_GET['u']);
    if ($idcontact === false) { echo "No public profiles have been found."; exit;  }
    $idcontact = $do_contact->idcontact;
    $do_contact->sessionPersistent("do_contact", "index.php", OFUZ_TTL);
  } elseif (isset($_SESSION['do_contact'])) {
    $idcontact = $_SESSION['do_contact'];
  } else {
    exit;
  }
  $_SESSION['public_profile_name'] = $_GET['u'];

    //if(isset($_POST['add_cont'])){
     if($_POST['hd_add_cont']){
	    $_SESSION['hcard_idcontact'] = $idcontact;
	    if (!is_object($_SESSION['do_User'])) {
	      $disp = new Display("user_login.php");
	      $disp->addParam("message", "Your session has expired, please sign-in again");
	      //$disp->addParam("entry", $_SERVER['REQUEST_URI']);
		      $_SESSION['entry'] = $_SERVER['REQUEST_URI'];
	      header("Location: /".$disp->getUrl());
	      exit;
	  } 
	  if (is_object($_SESSION['do_User'])) {
	  try {
	    if (!$_SESSION['do_User']->iduser) {
	      $disp = new Display("user_login.php");
	      $disp->addParam("message", "Error with your user record, please sign-in again");
	      //$disp->addParam("entry", $_SERVER['REQUEST_URI']);
		      $_SESSION['entry'] = $_SERVER['REQUEST_URI'];
	      header("Location: /".$disp->getUrl());
	      
	    }
	  } catch (Exception $e) { 
	      $disp = new Display("user_login.php");
	      $disp->addParam("message", "Error with your user record, please sign-in again");
	      //$disp->addParam("entry", $_SERVER['REQUEST_URI']);
		      $_SESSION['entry'] = $_SERVER['REQUEST_URI'];
	      header("Location: /".$disp->getUrl());
	  }
	}      
		      
 }

if($_SESSION['hcard_idcontact'] != '' ){
      $do_add_cont = new Contact();
      $do_add_cont->addNew();
      $do_add_cont->firstname = $_SESSION['do_contact']->firstname;
      $do_add_cont->lastname = $_SESSION['do_contact']->lastname;
      $do_add_cont->iduser = $_SESSION['do_User']->iduser;
      $do_add_cont->add();
      $idcontact_inserted = $do_add_cont->getPrimaryKeyValue();

      $ContactPhone = $_SESSION['do_contact']->getChildContactPhone();
      $ContactPhoneAdd = new ContactPhone();
      if($ContactPhone->getNumRows()){
          while($ContactPhone->next()){
              $ContactPhoneAdd->addNew();
              $ContactPhoneAdd->idcontact = $idcontact_inserted;
              $ContactPhoneAdd->phone_type = $ContactPhone->phone_type;
              $ContactPhoneAdd->phone_number = $ContactPhone->phone_number;
              $ContactPhoneAdd->add();
          }
      }

      $ContactEmail = $_SESSION['do_contact']->getChildContactEmail();
      $ContactEmailAdd = new ContactEmail();
      if($ContactEmail->getNumRows()){
        while($ContactEmail->next()){
            $ContactEmailAdd->addNew();
            $ContactEmailAdd->idcontact = $idcontact_inserted;
            $ContactEmailAdd->email_type = $ContactEmail->email_type;
            $ContactEmailAdd->email_address = $ContactEmail->email_address;
            $ContactEmailAdd->add();
        }
      }


      $ContactInstantMessage = $_SESSION['do_contact']->getChildContactInstantMessage();
      $ContactInstantMessageAdd = new ContactInstantMessage();
      if($ContactInstantMessage->getNumRows()){
        while($ContactInstantMessage->next()){
            $ContactInstantMessageAdd->addNew();
            $ContactInstantMessageAdd->idcontact = $idcontact_inserted;
            $ContactInstantMessageAdd->im_type = $ContactInstantMessage->im_type;
            $ContactInstantMessageAdd->im_username = $ContactInstantMessage->im_username;
            $ContactInstantMessageAdd->add();
        }
      }

      $ContactWebsite = $_SESSION['do_contact']->getChildContactWebsite();
      $ContactWebsiteAdd = new ContactWebsite();
      if($ContactWebsite->getNumRows()){
        while($ContactWebsite->next()){
            $ContactWebsiteAdd->addNew();
            $ContactWebsiteAdd->idcontact = $idcontact_inserted;
            $ContactWebsiteAdd->website = $ContactWebsite->website;
            $ContactWebsiteAdd->website_type = $ContactWebsite->website_type;
            $ContactWebsiteAdd->add();
        }
      }

      $ContactAddress = $_SESSION['do_contact']->getChildContactAddress();
      $ContactAddressAdd = new ContactAddress();
      if($ContactAddress->getNumRows()){
        while($ContactAddress->next()){
            $ContactAddressAdd->addNew();
            $ContactAddressAdd->idcontact = $idcontact_inserted;
            $ContactAddressAdd->address_type = $ContactAddress->address_type;
            $ContactAddressAdd->address = $ContactAddress->address;
            $ContactAddressAdd->add();
        }
      }

      $do_cont_ref = new Contact();
      $do_cont_ref->getId($idcontact_inserted);
      $do_contact_view = new ContactView();
      $do_contact_view->setUser($_SESSION['do_User']->iduser);
      $do_contact_view->addFromContact($do_cont_ref);
      $do_contact_view->updateFromContact($do_cont_ref);
      $do_cont_ref->free();
      $_SESSION['hcard_idcontact'] = '';
      header("Location: /Contact/".$idcontact_inserted);
      exit;
}
	



    $Keywords = 'Ofuz Profile for '.$_SESSION['do_contact']->firstname.' '.$_SESSION['do_contact']->lastname;
    //$Description = 'Description for search engine';
    $background_color = 'white';
    include_once('includes/header_profile.inc.php');
?>
<div class="layout_center">
    <div class="layout_top">
        <a href="http://www.ofuz.com"><img src="/images/ofuz_logo_profile.png" width="157" height="100" alt="" /></a>
        <div class="profile_photo">
<?php if ($_SESSION['do_contact']->picture != '') { ?>
	<img src="<?php echo $_SESSION['do_contact']->getContactPicture(); ?>" height="100%" alt="" />
<?php } ?>
        </div>
    </div>
</div>
<div class="layout_full"><div class="layout_center">
    <div class="layout_main">
        <div class="layout_headline">
            <b><?php echo $_SESSION['do_contact']->firstname, ' ', $_SESSION['do_contact']->lastname; ?></b><br />
            <i><?php echo $_SESSION['do_contact']->position,' at ',$_SESSION['do_contact']->company; ?></i>
        </div>
        <div class="layout_lineitems">
<?php
    $ContactEmail = $_SESSION['do_contact']->getChildContactEmail();
    if ($ContactEmail->getNumRows()) {
        while ($ContactEmail->next()) {
            echo '<div class="layout_lineitem">';
            echo '<img class="profile_icon" src="/images/profile_icon_email.png" width="16" height="11" alt="" />';
            echo '<a href="mailto:'.$ContactEmail->email_address.'" title="'.$ContactEmail->email_type.'">'.$ContactEmail->email_address.'</a>';
            //echo $_SESSION['ContactEditSave']->formatTextDisplay($ContactEmail->email_address);
            echo '</div>',"\n";
        }
    }

    $ContactWebsite = $_SESSION['do_contact']->getChildContactWebsite();
    if ($ContactWebsite->getNumRows()) {
        while ($ContactWebsite->next()) {
            echo '<div class="layout_lineitem">';
            echo $ContactWebsite->getProfileLink();
            echo '</div>',"\n";
        }
    }

    $ContactInstantMessage = $_SESSION['do_contact']->getChildContactInstantMessage();
    if ($ContactInstantMessage->getNumRows()) {
        while($ContactInstantMessage->next()){
            echo '<div class="layout_lineitem">';
            if ($ContactInstantMessage->im_type == 'AIM') {
                echo '<img class="profile_icon" src="/images/profile_icon_aim.png" width="16" height="16" alt="" />';
            } else if ($ContactInstantMessage->im_type == 'Google Talk') {
                echo '<img class="profile_icon" src="/images/profile_icon_gtalk.png" width="16" height="16" alt="" />';
            } else if ($ContactInstantMessage->im_type == 'Jabber') {
                echo '<img class="profile_icon" src="/images/profile_icon_jabber.png" width="16" height="16" alt="" />';
            } else if ($ContactInstantMessage->im_type == 'MSN') {
                echo '<img class="profile_icon" src="/images/profile_icon_msn.png" width="16" height="16" alt="" />';
            } else if ($ContactInstantMessage->im_type == 'Skype') {
                echo '<img class="profile_icon" src="/images/profile_icon_skype.png" width="16" height="16" alt="" />';
            } else if ($ContactInstantMessage->im_type == 'Yahoo') {
                echo '<img class="profile_icon" src="/images/profile_icon_yahoo.png" width="16" height="16" alt="" />';
            }
            echo $ContactInstantMessage->im_username;
            echo '</div>',"\n";
        }
    }

    $ContactPhone = $_SESSION['do_contact']->getChildContactPhone();
    if ($ContactPhone->getNumRows()) {
        while($ContactPhone->next()){
            echo '<div class="layout_lineitem">';
            if ($ContactPhone->phone_type == 'Work') {
                echo '<img class="profile_icon" src="/images/profile_icon_phonew.png" width="16" height="15" alt="" />';
            } else {
                echo '<img class="profile_icon" src="/images/profile_icon_phonem.png" width="16" height="18" alt="" />';
            }
            echo '<a href="tel:'.$ContactPhone->phone_number.'">'.$ContactPhone->phone_number.'</a>';
            echo '</div>',"\n";
        }
    }
?>
        </div>
        <div class="layout_add">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input class="profile_button" type="image" src="/images/profile_add_to_ofuz.png" alt="Add the contact to Ofuz" name="add_cont" />
                <a href="/public_profile_vcard.php"><img src="/images/profile_add_to_phone.png" width="91" height="26" alt="" /></a>
                <input type="hidden" name="hd_add_cont" value="1" />
            </form>
        </div>
        <div class="layout_clear"></div>
    </div>
<?php
    $ContactAddress = $_SESSION['do_contact']->getChildContactAddress();
    if ($ContactAddress->getNumRows()) {
    	echo '<div class="layout_address">';
	    while($ContactAddress->next()) {
            echo nl2br($ContactAddress->address) . '<br />';
	    }
	    echo '</div>',"\n";
    }
?>
</div></div>
<div class="layout_center">
    <div class="layout_bottom"></div>
</div>
<?php include_once('includes/ofuz_analytics.inc.php'); ?>
</body>
</html>