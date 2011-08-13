<?php 
/**COPYRIGHTS**/ 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

    /**
    * WorkFeedProjectDiscuss class
    *
    * @author SQLFusion's Dream Team <info@sqlfusion.com>
    * @package WorkFeed
    * @license ##License##
    * @version 0.6
    * @date 2010-09-04
    * @since 0.6
   */

class WorkFeedRssFeedImport extends WorkFeedItem {

    public $iduser;
	public $idcontact;
    public $feed_content; 
    public $task_event_type;
	public $website_url;

    function display() {
            $html = '<br />';
            $html .= '<div style="width:50px;float:left;">';
            $html .= '<img src="'.$this->cont_image_url.'" width="34" height="34" alt="" />';
            $html .= '</div>';
            $html .= '<div style="text-align:middle;">';
            $html .= '<a href ="/Contact/'.$this->idcontact.'">'.$this->full_contact_name.'</a>';
            $html .= ' '._('posted in').' '. '<a href="'.$this->website_url.'" target="_new">'.$this->website_url.'</a>';
            $html .='<br />'.stripslashes($this->feed_content); 
		    $html .= '<a href ="/Contact/'.$this->idcontact.'"> more...</a>';
            $html .= '</div>';
            $html .= '<div style = "color: #666666;font-size: 8pt; margin-left:50px;">';
            //$html .= date('l, F j,  g:i a ', $this->date_added);
	    $html .= OfuzUtilsi18n::formatDateLong(date("Y-m-d H:i:s",$this->date_added),true);
            $html .= '</div>';
            $html .='<br />';
            $html .= '<div class="dottedline"></div>';
			$html .= '<div id="'.$this->idworkfeed.'" class="message_box"></div>';
        return $html;
    }

    /**
	 * addRssFeed
	 * Prepare all the data for the RSS feed
	 * look for Co-Workers associated with the contact
	 * and add the users as a feed recipient.
	 * @note this is used in a cronjob so no session variables will works.
	 * @param ContactNote object
	 * @param website url
	 * @param note_content (without the link to source)
	 */

    function addRssFeed($do_contact_note, $website, $note_content) {
		$do_contact = new Contact();
		$this->idcontact = $do_contact_note->idcontact;
		$do_contact->getId($this->idcontact);
		$this->iduser = $do_contact->iduser;
		if(strlen($note_content) > 200 ) { 
			 $this->feed_content = substr($note_content, 0, 200); 
		} else {
			  $this->feed_content = $note_content;
		}
		
		$picture  = $do_contact->getContactPicture();
		
		$this->full_contact_name = $do_contact->getContactFullName($this->idcontact);
			
		//$this->task_event_type = "rss_feed_cron";
		$this->cont_image_url = $picture;
		$this->website_url = $website;
		$user_relation = new ContactSharing();
		$users = $user_relation->getCoWorkerByContact($this->idcontact);
		$users[] = $do_contact->iduser ;
        $this->addFeed($users);
    }
}
