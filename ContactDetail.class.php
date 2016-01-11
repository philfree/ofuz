<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/**
  * A ContactDetail plugin class
  * contact.php has persistent session object as $_SESSION['ContactEditSave']
  * So for contact related data can be retrieve from this object
  * This is set a block on the left side of contact.php with contact details
  * Little complex than what we have on the other test Example Weather Object 
  * It also has 2 extra params in setContent() i.e. url_path and url_name
  * @author SqlFusion LLC info@sqlfusion.com 
  */

class ContactDetailBlock extends BaseBlock{
    
      /**
	* processBlock() , This method must be added  
	* Required to set the Block Title and The Block Content Followed by displayBlock()
	* Must extent BaseBlock
      */
      function processBlock(){
	  $this->setTitle(_('Contact Information'),'/contact_edit.php',_('edit'));
	  $this->setContent($this->generateContactDetails());
	  $this->displayBlock();
      }

      /**
       * A custom method within the Plugin to generate the content
       * 
      */
      function generateContactDetails(){
	    $output = '';
	    if(is_object($_SESSION['ContactEditSave'])) {
		$ContactPhone = $_SESSION['ContactEditSave']->getChildContactPhone();// Getting ContactPhone data, check documentation for more details
		if($ContactPhone->getNumRows()){
		    $output .= '<b>'._('Phone').'</b><br />';
		    while($ContactPhone->next()){
			$output .= $ContactPhone->phone_type.': ';
			$output .= $ContactPhone->phone_number;
			$output .= '<br />';
		    }
		}

		$ContactEmail = $_SESSION['ContactEditSave']->getChildContactEmail();//check documentation for more details 
		if($ContactEmail->getNumRows()){
		    $output .= '<b>'._('Email').'</b><br />';
		    while($ContactEmail->next()){
			$output .= '<a href="mailto:'.$ContactEmail->email_address.'" title="'.$ContactEmail->email_type.'">'.$ContactEmail->email_address.'</a>';
			$output .= '<br />';
		    }
		}

		$ContactInstantMessage = $_SESSION['ContactEditSave']->getChildContactInstantMessage(); //check documentation for more details
		if($ContactInstantMessage->getNumRows()){
		    $output .= '<b>'._('IM').'</b><br />';
		    while($ContactInstantMessage->next()){
			$output .= $ContactInstantMessage->im_type.': ';
			$output .= $ContactInstantMessage->im_username;
			$output .= '<br />';
		    }
		}
		/**
		  * $_SESSION['feeds_checked'] is confusing ?? Well yes even we are when we developed the application
		  * This is nothing but a session var set via Ajax so as to decide the color of the icon depending on
		  * if the feed is checked or not :)
		*/
		$ContactWebsite = $_SESSION['ContactEditSave']->getChildContactWebsite();
		if($ContactWebsite->getNumRows()){
		    $output .= '<b>'._('Website').'</b><br />';
		    while($ContactWebsite->next()){
			$output .= $ContactWebsite->getDisplayLink();
			if (!is_array($_SESSION['feeds_checked'])) $_SESSION['feeds_checked'] = array();
			if (in_array($ContactWebsite->idcontact_website, $_SESSION['feeds_checked'])) {
			    if ($ContactWebsite->feed_auto_fetch == 'Yes') {
				$output .= ' <input class="feedverified" id="feed'.$ContactWebsite->idcontact_website.'" type="image" src="/images/feed-icon-12x12-green.png" onclick="autoFetchToggle(this,'.$ContactWebsite->idcontact_website.');" />';
			    } else if ($ContactWebsite->feed_auto_fetch == 'No') {
				$output .= ' <input class="feedverified" id="feed'.$ContactWebsite->idcontact_website.'" type="image" src="/images/feed-icon-12x12-orange.gif" onclick="autoFetchToggle(this,'.$ContactWebsite->idcontact_website.');" />';
			    }
			} else {
			    $_SESSION['feeds_checked'][] = $ContactWebsite->idcontact_website;
			    $output .= ' <input class="feedcheck'.$ContactWebsite->feed_auto_fetch.'" id="feed'.$ContactWebsite->idcontact_website.'" type="image" src="/images/wait16g.gif" onclick="autoFetchToggle(this,'.$ContactWebsite->idcontact_website.');" />';
			}
			$output .= "<br/>";
		    }
		}
	    }
	 return $output;
      }

     
}

?>
