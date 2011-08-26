<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

   /** 
    * Radria_Emailer Class
    * Used to Send Emails using email templates
    * with merge capabilities.
    * 
    * Its compatible with the older Emailer.class.php but its based 
    * on the Zend_Mail
    * http://framework.zend.com/
    *
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @version 2.0.3
    * @package OfuzCore
    * @access public
    */

class Ofuz_Emailer extends Radria_Emailer {
    var $cfgEmailField = "email";
    var $cfgTemplateTable = "emailtemplate_user";

   
   
    function getMessageFooterText($do_Contact) {
        $unsubscribe_url = $GLOBALS['cfg_ofuz_site_http_base'].'unsub/'.$do_Contact->idcontact.'/'.$_SESSION['do_User']->iduser;
        $msg = new Message();
        return MergeString::withArray($msg->getMessage("email footer text"), 
                                      Array("sender_name" => $_SESSION['do_User']->getFullName(),
                                            "receiver_name" => $do_Contact->firstname." ".$do_Contact->lastname,
                                            "unsubscribe_url" => $unsubscribe_url)
                                      );
    }
    function getMessageFooterHTML($do_Contact) {
        $unsubscribe_url = $GLOBALS['cfg_ofuz_site_http_base'].'unsub/'.$do_Contact->idcontact.'/'.$_SESSION['do_User']->iduser;
        return MergeString::withArray($msg->getMessage("email footer text"), 
                                      Array("sender_name" => $_SESSION['do_User']->getFullName(),
                                            "receiver_name" => $do_Contact->firstname." ".$do_Contact->lastname,
                                            "unsubscribe_url" => $unsubscribe_url)
                                      );		
    }
  
    /**
     * mergeArrayWithFooter()
     * like the default merge array but add the footer accordingly.
     * It requires in the fields values: idcontact, firstname and lastname of the receiver
     * Merge an Array with a currently loaded email template
     * @param array $fields_values fields in format $fields['fieldname']=value;
     */
    function mergeArrayWithFooter($fields_values) {
        $bodytext = $this->getTemplateBodyText();
        $bodyhtml = $this->getTemplateBodyHtml();
        $msg = new Message();
        $unsubscribe_url = $GLOBALS['cfg_ofuz_site_http_base'].'unsub/'.$fields_values['idcontact'].'/'.$_SESSION['do_User']->iduser;
        $fields_values['unsubscribe_url'] = $unsubscribe_url;
        $fields_values['sender_name'] = $_SESSION['do_User']->getFullName();
        $fields_values['receiver_name'] = $fields_values['firstname']." ".$fields_values['lastname'];
        if($fields_values["flag"]){
            if($fields_values["flag"] == "unsubscribe_autoresponder") {
                 //$unsubsribe_auto_responder = $this->getUnsubscribeAutoResponderLink($fields_values);
                //$bodytext .= $unsubsribe_auto_responder;
                //$bodyhtml .= $unsubsribe_auto_responder;
				  $bodyhtml .= $this->getUnsubscribeAutoResponderLinkHTML($fields_values);
				  $bodytext .= $this->getUnsubscribeAutoResponderLinkText($fields_values);
            }
        }
        $bodytext .= $msg->getMessage("email footer text");
        $bodyhtml .= $msg->getMessage("email footer html");
        
        $this->setBodyText(MergeString::withArray($bodytext, $fields_values)) ;
        if (strlen($this->getTemplateBodyHtml()) > 5) {
            $this->setBodyHtml(MergeString::withArray($bodyhtml, $fields_values));
        }
        $this->setSubject(MergeString::withArray($this->getTemplateSubject(), $fields_values));
    }


    
    function getUnsubscribeAutoResponderLinkHTML($fields_values) {
        $unsubscribe_url = $GLOBALS['cfg_ofuz_site_http_base'].'arunsub/'.$fields_values['idcontact'].'/'.$_SESSION['do_User']->iduser.'/'.$fields_values['idtag'];
        $text = '<p><b>UNSUBSCRIBE:</b><a href="'.$unsubscribe_url.'">'._('Click here').'</a>'.' '. _('to unsubscribe from this email list').' '.$fields_values["resp_name"].'.</p>';
        return $text;
    }
    
    function getUnsubscribeAutoResponderLinkText($fields_values) {
        $unsubscribe_url = $GLOBALS['cfg_ofuz_site_http_base'].'arunsub/'.$fields_values['idcontact'].'/'.$_SESSION['do_User']->iduser.'/'.$fields_values['idtag'];
        $text =  "\n\n"._('To unsubscribe from this email list').' '.$fields_values["resp_name"].' '._('follow the link')."\n".' '.$unsubscribe_url."\n";
        return $text;
    }
  
}
?>