<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

include_once('config.php');

$small_screen = $_POST["smallscreen"];
$profile_html = "";

// Mobile/Tablet version
if($small_screen == 'true') {
$profile_html .= '<div class="layout_full"><div class="layout_center">';
$profile_html .= '<div class="layout_main">';
$profile_html .= '<div style="float:left;height: 120px;margin: 18px;width: 120px;">';
if ($_SESSION['do_contact']->picture != '') {
$profile_html .= '<img src="'.$_SESSION['do_contact']->getContactPicture().'" height="80%" alt="" />';
}
$profile_html .= '</div>';
$profile_html .= '<div class="layout_headline">';
$profile_html .= '<br/>';
$profile_html .= '<b>'.$_SESSION['do_contact']->firstname. ' '. $_SESSION['do_contact']->lastname.'</b><br />';
$profile_html .= '<i>'.$_SESSION['do_contact']->position.' at '.$_SESSION['do_contact']->company.'</i>';
$profile_html .= '</div>';
$profile_html .= '<div class="layout_lineitems">';

$ContactEmail = $_SESSION['do_contact']->getChildContactEmail();
if ($ContactEmail->getNumRows()) {
    while ($ContactEmail->next()) {
	$profile_html .=  '<div class="layout_lineitem">';
	$profile_html .=  '<img class="profile_icon" src="/images/profile_icon_email.png" width="16" height="11" alt="" />';
	$profile_html .=  '<a href="mailto:'.$ContactEmail->email_address.'" title="'.$ContactEmail->email_type.'">'.$ContactEmail->email_address.'</a>';
	$profile_html .=  '</div>';
    }
}else{
    $profile_html .=  '<div class="layout_lineitem">';
    $profile_html .=  '<img class="profile_icon" src="/images/profile_icon_email.png" width="16" height="11" alt="" />';
    $profile_html .=  '</div>';
}

$ContactWebsite = $_SESSION['do_contact']->getChildContactWebsite();
if ($ContactWebsite->getNumRows()) {
    while ($ContactWebsite->next()) {
	$profile_html .=  '<div class="layout_lineitem">';
	$profile_html .=  $ContactWebsite->getProfileLink();
	$profile_html .=  '</div>';
    }
}else{
  $profile_html .=  '<div class="layout_lineitem">';
  $profile_html .=  '<img src="/images/profile_icon_website.png " alt=" " height="21" width="16">';
  $profile_html .=  '</div>';
}

$ContactInstantMessage = $_SESSION['do_contact']->getChildContactInstantMessage();
if ($ContactInstantMessage->getNumRows()) {
    while($ContactInstantMessage->next()){
	$profile_html .=  '<div class="layout_lineitem">';
	if ($ContactInstantMessage->im_type == 'AIM') {
	    $profile_html .=  '<img class="profile_icon" src="/images/profile_icon_aim.png" width="16" height="16" alt="" />';
	} else if ($ContactInstantMessage->im_type == 'Google Talk') {
	    $profile_html .=  '<img class="profile_icon" src="/images/profile_icon_gtalk.png" width="16" height="16" alt="" />';
	} else if ($ContactInstantMessage->im_type == 'Jabber') {
	    $profile_html .=  '<img class="profile_icon" src="/images/profile_icon_jabber.png" width="16" height="16" alt="" />';
	} else if ($ContactInstantMessage->im_type == 'MSN') {
	    $profile_html .=  '<img class="profile_icon" src="/images/profile_icon_msn.png" width="16" height="16" alt="" />';
	} else if ($ContactInstantMessage->im_type == 'Skype') {
	    $profile_html .=  '<img class="profile_icon" src="/images/profile_icon_skype.png" width="16" height="16" alt="" />';
	} else if ($ContactInstantMessage->im_type == 'Yahoo') {
	    $profile_html .=  '<img class="profile_icon" src="/images/profile_icon_yahoo.png" width="16" height="16" alt="" />';
	}
	echo $ContactInstantMessage->im_username;
	$profile_html .=  '</div>';
    }
}else{
      $profile_html .=  '<div class="layout_lineitem">';
      $profile_html .=  '<img class="profile_icon" src="/images/profile_icon_skype.png" width="16" height="16" alt="" />';
      $profile_html .=  '</div>';
}

$ContactPhone = $_SESSION['do_contact']->getChildContactPhone();
if ($ContactPhone->getNumRows()) {
    while($ContactPhone->next()){
	$profile_html .=  '<div class="layout_lineitem">';
	if ($ContactPhone->phone_type == 'Work') {
	    $profile_html .=  '<img class="profile_icon" src="/images/profile_icon_phonew.png" width="16" height="15" alt="" />';
	} else {
	    $profile_html .=  '<img class="profile_icon" src="/images/profile_icon_phonem.png" width="16" height="18" alt="" />';
	}
	$profile_html .=  '<a href="tel:'.$ContactPhone->phone_number.'">'.$ContactPhone->phone_number.'</a>';
	$profile_html .=  '</div>';
	$contact_no=$ContactPhone->phone_number;
    }
}else{
  $profile_html .=  '<div class="layout_lineitem">';
  $profile_html .=  '<img class="profile_icon" src="/images/profile_icon_phonew.png" width="16" height="15" alt="" />';
  $profile_html .=  '</div>';
}

$profile_html .= '</div>';
if(!empty($contact_no)) {
$profile_html .= '<div class="layout_add">';
$profile_html .= '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
$profile_html .= '<input class="profile_button" type="image" src="/images/profile_add_to_ofuz.png" alt="Add the contact to Ofuz" name="add_cont" />';
$profile_html .= '<a href="/public_profile_vcard.php"><img src="/images/profile_add_to_phone.png" width="91" height="26" alt="" /></a>';
$profile_html .= '<input type="hidden" name="hd_add_cont" value="1" />';
$profile_html .= '</form>';
$profile_html .= '</div>';
}
$profile_html .= '<div class="layout_clear"></div>';
$profile_html .= '</div>';

$ContactAddress = $_SESSION['do_contact']->getChildContactAddress();
if ($ContactAddress->getNumRows()) {
    $profile_html .=  '<div class="layout_address">';
	while($ContactAddress->next()) {
	$profile_html .=  nl2br($ContactAddress->address) . '<br />';
	}
	$profile_html .=  '</div>';
}

$profile_html .= '</div></div>';

} else {
// Web version

$profile_html .= '<div class="profile_web_layout_main">';
$profile_html .= '<div class="profile_web_layout_inner">';
$profile_html .= '<div class="profile_web_layout_photo_block">';
$profile_html .= '<div class="profile_web_layout_photo_left">';
if ($_SESSION['do_contact']->picture != '') {
$profile_html .= '<img src="'.$_SESSION['do_contact']->getContactPicture().'" height="100%"  width = 100% alt="" />';
} else {
$profile_html .= '<img src="/images/empty_avatar.gif" height="100%"  width = 100% alt="" />';
}
$profile_html .= '</div>';

$profile_html .= '<div class="profile_web_layout_photo_right">';
$profile_html .= '<div class="profile_name">';
$profile_html .= '<div style="position:relative;float:left;width:70%;">'.$_SESSION['do_contact']->firstname.' '.$_SESSION['do_contact']->lastname.'</div>';
$profile_html .= '<div style="position:relative;float:right;width:15%;"><a href="/public_profile_vcard.php"><img class="profile_button" src="/images/profile_add_to_phone.png" width="91" height="26" alt="" /></div>';
$profile_html .= '<div style="position:relative;float:right;width:15%;">';
$profile_html .= '<form method="post" action="/public_profile.php">';
$profile_html .= '<input class="profile_button" type="image" src="/images/profile_add_to_ofuz.png" alt="Add the contact to Ofuz" name="add_cont" />';
$profile_html .= '<input type="hidden" name="hd_add_cont" value="1" />';
$profile_html .= '</form>';
$profile_html .= '</div>';
$profile_html .= '</div>';

$profile_html .= '<div style="clear:both;"></div>';
$profile_html .= '<div class="profile_title">'.$_SESSION['do_contact']->position.' at <span class="profile_company">'.$_SESSION['do_contact']->company.'</span></div>';
//$profile_html .= '<div style="vertical-align:right;"><input class="profile_button" type="image" src="/images/profile_add_to_ofuz.png" alt="Add the contact to Ofuz" name="add_cont" /></div>';
$profile_html .= '</div>';
$profile_html .= '</div>';
$profile_html .= '<div class="spacerblock_40"></div>';
$profile_html .= '<div class="profile_text_subjectline">Bio</div>';
$profile_html .= '<div class="profile_line"></div>';
$profile_html .= '<div class="profile_text_content">';
$do_userprofile = new UserProfile();
$profile_information = $do_userprofile->getProfileInformation($_SESSION['do_User']->iduser);    
$profile_html .= '<p>'.$profile_information['job_description'].'</p>';
$profile_html .= '</div>';
$profile_html .= '<div class="profile_text_subjectline">Contact Information</div>';
$profile_html .= '<div class="profile_line"></div>';
$profile_html .= '<div class="spacerblock_20"></div>';

$profile_html .= '<div class="profile_ci_text">';
$profile_html .= '<div>';

$ContactEmail = $_SESSION['do_contact']->getChildContactEmail();
if($ContactEmail->getNumRows()){                       
  while($ContactEmail->next()){
      $profile_html .= '<div class="layout_lineitem">';
      $profile_html .= '<img class="profile_icon" src="/images/profile_icon_email.png" alt=""  height="11" width="16">';
      $profile_html .= '<a class="profile_ci_text"  href="mailto:'.$ContactEmail->email_address.'" title="'.$ContactEmail->email_type.'">'.$ContactEmail->email_address.'</a>';
      $profile_html .= '</div>';
    }
}

$profile_html .= '</div>';
$profile_html .= '<div>';

$ContactWebsite = $_SESSION['do_contact']->getChildContactWebsite();
    if($ContactWebsite->getNumRows()){                        
	while($ContactWebsite->next()){
	  $profile_html .= '<div class="layout_lineitem">';
	  $profile_html .= '<img src="/images/profile_icon_website.png " alt=" " height="21" width="16">';
	  $profile_html .= '<span class="profile_ci_text">'.$ContactWebsite->getDisplayLink().'</span>';
	  $profile_html .= '</div>';
	}
    }

$profile_html .= '</div>';
$profile_html .= '<div>';

$ContactInstantMessage = $_SESSION['do_contact']->getChildContactInstantMessage();
if ( $ContactInstantMessage->getNumRows()) {
    while($ContactInstantMessage->next()){
      $profile_html .= '<div class="layout_lineitem">';
      if ( $ContactInstantMessage->im_type == 'AIM') {
	  $profile_html .= '<img class="profile_icon" src="/images/profile_icon_aim.png" width="16" height="16" alt="" />';
	  $profile_html .= '<a class="profile_ci_text">'.$ContactInstantMessage->im_username.'</a>';
      } else if ( $ContactInstantMessage->im_type == 'Google Talk') {
	  $profile_html .= '<img class="profile_icon" src="/images/profile_icon_gtalk.png" width="16" height="16" alt="" />';
	  $profile_html .= '<a class="profile_ci_text">'.$ContactInstantMessage->im_username.'</a>';
      } else if ( $ContactInstantMessage->im_type == 'Jabber') {
	  $profile_html .= '<img class="profile_icon" src="/images/profile_icon_jabber.png" width="16" height="16" alt="" />';
	  $profile_html .= '<a class="profile_ci_text">'.$ContactInstantMessage->im_username.'</a>';
      } else if ( $ContactInstantMessage->im_type == 'MSN') {
	  $profile_html .= '<img class="profile_icon" src="/images/profile_icon_msn.png" width="16" height="16" alt="" />';
	  $profile_html .= '<a class="profile_ci_text">'.$ContactInstantMessage->im_username.'</a>';
      } else if ( $ContactInstantMessage->im_type == 'Skype') {
	  $profile_html .= '<img class="profile_icon" src="/images/profile_icon_skype.png" width="16" height="16" alt="" />';
	  $profile_html .= '<a class="profile_ci_text">'.$ContactInstantMessage->im_username.'</a>';
      } else if ( $ContactInstantMessage->im_type == 'Yahoo') {
	  $profile_html .= '<img class="profile_icon" src="/images/profile_icon_yahoo.png" width="16" height="16" alt="" />';
	  $profile_html .= '<a class="profile_ci_text">'.$ContactInstantMessage->im_username.'</a>';
      }
  }
}

$profile_html .= '</div>';
$profile_html .= '<div>';

$ContactPhone = $_SESSION['do_contact']->getChildContactPhone();
  if ($ContactPhone->getNumRows()) {
    while($ContactPhone->next()){
	$profile_html .= '<div class="layout_lineitem">';
	if ($ContactPhone->phone_type == 'Work') {
	    $profile_html .= '<img class="profile_icon" src="/images/profile_icon_phonew.png" width="16" height="15" alt="" />';
	} else {
	    $profile_html .= '<img class="profile_icon" src="/images/profile_icon_phonem.png" width="16" height="18" alt="" />';
	}
	$profile_html .= '<a class="profile_ci_text" href="tel:'.$ContactPhone->phone_number.'">'.$ContactPhone->phone_number.'</a>';
	$profile_html .= '</div>';
    }
}

$profile_html .= '</div>';
$profile_html .= '<div style="margin-left:36px;">';

$ContactAddress = $_SESSION['do_contact']->getChildContactAddress();
if ($ContactAddress->getNumRows()) {   
  while($ContactAddress->next()) {
    $profile_html .= nl2br($ContactAddress->address) . '<br>';
  }
}
$profile_html .= '</div>';

$profile_html .= '<div class="spacerblock_40"></div>';

$profile_html .= '</div>';
$profile_html .= '</div>';

}

echo $profile_html;
?>