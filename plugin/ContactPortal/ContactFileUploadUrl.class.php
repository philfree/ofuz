<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/ 

  /**
    * @author SQLFusion's Dream Team <info@sqlfusion.com>
    * @package OfuzContactPortal
    * @license ##License##
    * @version 0.6.2
    * @date 2010-11-13
    * @since 0.6.2
    */

class ContactFileUploadUrl extends BaseBlock{

  public $short_description = 'Contact Portal block';
  public $long_description = 'Lists the options for files and note sharing with Contacts';

    
  /**
   * processBlock() , This method must be added  
   * Required to set the Block Title and The Block Content Followed by displayBlock()
   * Must extend BaseBlock
   */

  function processBlock(){
  
    $idcontact = $_SESSION['do_cont']->idcontact;
    
    if(!is_object($_SESSION['do_contact_portal'])){
      $do_contact_portal = new ContactPortal(); 
      $do_contact_portal->getId($_SESSION['do_cont']->idcontact);
      $do_contact_portal->sessionPersistent("do_contact_portal", "index.php", OFUZ_TTL);
    }
    
    $portal_code = $_SESSION['do_contact_portal']->checkIfNotesShared($idcontact);  
    if($portal_code) {
      $this->setTitle(_('Contact Portal'), '/PlugIn/ContactPortal/cp_settings',_('Settings'));
    } else {
      $this->setButtonOnClickDisplayBlock(_('share files and notes'),'','/PlugIn/ContactPortal/cp_settings','','','dyn_button_share_this');
    }
    $this->setContent($this->generateBlockContent());    
    $this->displayBlock();
  }

  /**
   * A custom method within the Plugin to generate the content
   * @return string : html
   */

  function generateBlockContent(){
    $output = '';
    $idcontact = $_SESSION['do_cont']->idcontact;
    $goto = 'Contact/'.$idcontact;
    $portal_code = $_SESSION['do_contact_portal']->checkIfNotesShared($idcontact);
    $contact_file_upload_url = "";
    if($portal_code) {
      $script = "<script type='text/javascript'>
      function showMsgBox() {
      $(\"#portal_msg\").slideToggle(\"slow\");
      }
      </script>";
      $output .= $script;
      //$do_contact = new Contact();
      //$do_contact->getContactDetails($idcontact);

      $output .= '<p>'._('The link bellow is a place where you and '). "<b>".$_SESSION['do_contact_portal']->firstname." ".$_SESSION['do_contact_portal']->lastname."</b>". _(' can share files, documents and notes.')."</p>";
      $output .= "<a href='".$GLOBALS['cfg_ofuz_site_http_base']."cp/".$portal_code."'>".$GLOBALS['cfg_ofuz_site_http_base']."cp/".$portal_code."</a><br /><br />";
      
     /**
      //generating an Event to create an unique URL link to Share Notes & Files
      $e_generate_url = new Event("do_contact_portal->eventGenerateSharedUrl");
      $e_generate_url->addParam("idcontact",$idcontact);
      $e_generate_url->addParam("goto", $goto);
      //generating an Event to create an new unique URL link to Share Notes & Files
      $e_generate_new_url = new Event("do_cont->eventGenerateNewSharedUrl");
      $e_generate_new_url->addParam("idcontact", $idcontact);
      $e_generate_new_url->addParam("goto", $goto);
      //generating an Event to stop sharing the Notes & Files
      $e_stop_sharing_notes = new Event("do_cont->eventStopSharingNotes");
      $e_stop_sharing_notes->addParam("idcontact", $idcontact);
      $e_stop_sharing_notes->addParam("goto", $goto);

      $output .= "<b> * </b>".$e_generate_url->getLink("Send the address link by email")."<br />";
      $output .= "<b> * </b>".$e_generate_new_url->getLink(_('Generate a new address link'))."<br />";
      $output .= "<b> * </b>".$e_stop_sharing_notes->getLink(_('Stop sharing'))."<br />";

      $do_contact_msg = new ContactMessage();
      $pers_msg = $do_contact_msg->getPersonalizedMessage($idcontact,$_SESSION['do_User']->iduser);

      $output .= "<b> * </b>".'<a href="#" onclick="showMsgBox(); return false;">'. _('Set a personalized message').'</a>';
      $output .= '<div id="portal_msg" style="display:none;">';

      $e_portal_msg = new Event("ContactMessage->eventSetPersonalizedMessage");
      $e_portal_msg->addParam("idcontact", $idcontact);

      $output .= $e_portal_msg->getFormHeader();
      $output .= $e_portal_msg->getFormEvent();

      $pers_msg = ($pers_msg) ? $pers_msg : '';

      $output .= '<textarea name="per_msg" rows="3" cols="30">'.$pers_msg.'</textarea>';
      $output .= $e_portal_msg->getFormFooter("Submit");
      $output .= '</div>';
      */
    } else {
/**
      $e_generate_url = new Event("do_contact_portal->eventGenerateSharedUrl");
      $e_generate_url->addParam("idcontact",$idcontact);
      $e_generate_url->addParam("goto", $goto);

      $share_btn .= '<div class="dyn_button" style="margin: 0pt 0pt 10px 25px;">';
      $share_btn .= '<div class="dyn_button_c">';
      $share_btn .= '<div class="dyn_button_share_this"></div>';
      $share_btn .= '<div class="dyn_button_text_icon">share files and notes</div>';
      $share_btn .= '<div class="dyn_button_r"></div>';
      $share_btn .= '</div></div>';
      $output .= $e_generate_url->getLink($share_btn);
      **/
    }

    return $output;
  }  
      
}

?>
