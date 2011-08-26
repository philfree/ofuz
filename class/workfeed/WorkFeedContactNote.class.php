<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com

   /**
    * WorkFeedContactNote class
    * @author SQLFusion's Dream Team <info@sqlfusion.com>
    * @package WorkFeed
    * @license GNU Affero General Public License
    * @version 0.6
    * @date 2010-09-04
    * @since 0.6
    *
    */

class WorkFeedContactNote extends WorkFeedItem {
    private $note;
    private $iduser;
    private $idcontact; 
	private $idcontact_note;
	private $user_full_name;
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
		$html .= ' '._('has a note from').' '.'<b>'.$this->user_full_name.'</b>';
		$html .= '<div id="notepreview'.$this->idcontact_note.'">';
		$html .= stripslashes($this->note);
		//$html .= htmlentities($this->note);
		if ($this->more) {
			$html .= '<a onclick="showFullNote('.$this->idcontact_note.'); return false;" href="#">'._('more ...').'</a>';
		}
		$htnl .='</div>';
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
	 * eventAddFeed
	 * This event is triggered when the note is added in the 
	 * contact.php page.
	 * Its the last event and assume that the ContactNoteEditSave has
	 * a primary key from the database table.
	 * This event action prepare all the data so no additional query is needed
	 * in the display table.
	 * @param EventControler
	 */
	
    function eventAddFeed(EventControler $evtcl) {
		$this->note = $_SESSION['ContactNoteEditSave']->note;
        $this->iduser = $_SESSION['ContactNoteEditSave']->iduser;
        $this->idcontact = $_SESSION['ContactNoteEditSave']->idcontact;
		$this->idcontact_note = $_SESSION['ContactNoteEditSave']->idcontact_note;
		$user = new User();
		$user->getId($this->iduser);
		$this->user_full_name = $user->getFullName();
		$do_contact = new Contact();
		$do_contact->getId($this->idcontact);
		$this->contact_full_name = $do_contact->getContactFullName();		
		$this->contact_image_url = $do_contact->getContactPicture();		
		if(strlen($this->note) > 200 ){ 
			$this->note = substr($this->note, 0, 200);
			$this->more = True;
		} else { $this->more = false; }
					
		$user_relation = new ContactSharing();
        $this->addFeed($user_relation->getCoWorkerByContact($this->idcontact));
    }
}
?>
