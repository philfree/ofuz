<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

   /**
    * WorkFeedContactUnsubscibeEmails class
    * Copyright 2001 - 2008 SQLFusion LLC, Author: Philippe Lewicki, Abhik Chakraborty ,Jay Link info@sqlfusion.com 
    *
    * @author SQLFusion's Dream Team <info@sqlfusion.com>
    * @package WorkFeed
    * @license GNU Affero General Public License
    * @version 0.6
    * @date 2010-09-04
    * @since 0.6
   */

class WorkFeedContactUnsubscibeEmails extends WorkFeedItem {
    private $note;
    private $iduser;
    private $idcontact; 
    private $contact_full_name;
    private $contact_image_url;
    private $more = false;
   
	
    function display() {
      $html = '<br />';
      $html .= '<div style="width:50px;float:left;">';
      $html .= '<img src="'.$this->contact_image_url.'" width="34" height="34" alt="" />';
      $html .= '</div>';
      $html .= '<div style="text-align:middle;">';
      $html .= '<a href ="/Contact/'.$this->idcontact.'">'.$this->contact_full_name.'</a>';
      $html .= ' '.stripslashes($this->note);
      $htnl .='</div>';
      $html .= '</div>';
      $html .= '<div style = "color: #666666;font-size: 8pt; margin-left:50px;">';
      $html .= OfuzUtilsi18n::formatDateLong(date("Y-m-d H:i:s",$this->date_added),true);
      $html .= '</div>';
      $html .='<br />';
      $html .= '<div class="dottedline"></div>';
      $html .= '<div id="'.$this->idworkfeed.'" class="message_box"></div>';
      return $html;
    }

    /**
      * Function adds workfeed when a contact unsubscibe emails
      * @param object $obj, object containing the contact and user id
      * @param string $responder , name of the responder
    */

    function addUnsubscribeEmailWorkfeed($obj,$responder=""){
        if($responder == ""){
           $this->note = _('has unsubscribed from emails'); 
        }else{
            $this->note = _('has unsubscribed from the auto-responder series ').$responder;
        }
        $this->iduser = $obj->iduser;
        $this->idcontact = $obj->idcontact;
        $user = new User();
        $user->getId($this->iduser);
        $do_contact = new Contact();
        $do_contact->getId($this->idcontact);
        //$this->added_by = $do_contact->getContactFullName();  
        $this->contact_full_name = $do_contact->getContactFullName();  
        $this->contact_image_url = $do_contact->getContactPicture();    
        $user_relation = new ContactSharing();
        $user_array = $user_relation->getCoWorkerByContact($this->idcontact);
        @array_push($user_array, $this->iduser);
        $this->addFeed($user_array);

    }
}
