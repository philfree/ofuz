<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Contact class
     * Using the DataObject
     */

class ContactWebsite extends MultiRecord {
    
    public $table = "contact_website";
    protected $primary_key = "idcontact_website";
    protected $prefix = "ContactWebsite";  // Should be the same as the class name 
    protected $dropdown_options = Array( "Company", "Personal", "Blog", "Twitter", "Facebook", "LinkedIn", "Youtube", "RSS Feed", "Other");

   protected function getNewFormFields($new_website_count) {
      $form  ='
        <input type="text" name="mfields['.$this->getTable().'_new]['.$new_website_count.'][website]" value="">
        <select name="mfields['.$this->getTable().'_new]['.$new_website_count.'][website_type]">';
            foreach ($this->dropdown_options as $new_website_count) {
                $form .= "<option>".$new_website_count."</option>";
            }
       $form .='</select>';
       return $form;

   }

   protected function getUpdateFormFields() {
        $form .= '<input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][website]" value="'.$this->website.'">';
        $form .= '<select name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][website_type]">';
        foreach ($this->dropdown_options as $website_type) {
            if ($website_type == $this->website_type) { $selected = " SELECTED"; } else { $selected = ""; }
            $form .= "<option ".$selected.">".$website_type."</option>";
        }
        $form .= '</select>';
        return $form;
   }     

   public function getDisplayLink() {
        if ($this->website_type == "Facebook") {
            $link = "\n".'<a href="'.$this->website.'" title="Facebook Profile" target="_blank"><img src="/images/facebook_small.gif" class="fb_profile_img" alt="Facebook Profile" /> </a>';
        } elseif ($this->website_type == "Twitter") {
            if (ereg("www", $this->website)) { 
                $name = str_replace("http://www.twitter.com/", "", $this->website);
            } else {
                $name = str_replace("http://twitter.com/", "", $this->website);
            }
            $link = "\n".'<a href="'.$this->website.'" title="Twitter" target="_blank"><img src="/images/twitter_small.png" width="86" height="20" alt="Twitter" />@'.$name.'</a>';
        } elseif ($this->website_type == "LinkedIn") {
            $link = "\n".'<a href="'.$this->website.'" title="LinkedIn Profile" target="_blank"><img src="/images/linkedin_small.png" width="74" height="20" alt="LinkedIn Profile" /></a>';
        } else {
            if (ereg("http", $this->website)) { 
                $name = str_replace("http://", "", $this->website);
                $name = str_replace("/", "", $name);
            } 
            $link = '<img src="'.$this->website.'/favicon.ico" height="16" width="16" alt="" /> <a href="'.$this->website.'" title="'.$this->website_type.'" target="_blank">'.$name.'</a> ';
        }
        return $link;
   }

   /**
    * Used for public_profile.php
    */
   public function getProfileLink() {
        if ($this->website_type == 'Facebook') {
        	$name = $this->website;
            $name = str_replace('http://www.facebook.com/', 'facebook.com/', $name);
            $name = str_replace('http://facebook.com/', 'facebook.com/', $name);
            $link = '<img class="profile_icon" src="/images/profile_icon_facebook.png" width="16" height="16" alt="" /><a href="'.$this->website.'" title="Facebook Profile">'.$name.'</a>';
        } elseif ($this->website_type == 'Twitter') {
            $name = $this->website;
            $name = str_replace('http://www.twitter.com/', '', $name);
            $name = str_replace('http://twitter.com/', '', $name);
            $link = '<img class="profile_icon" src="/images/profile_icon_twitter.png" width="16" height="14" alt="" /><a href="'.$this->website.'" title="Twitter">@'.$name.'</a>';
        } elseif ($this->website_type == 'LinkedIn') {
            $name = $this->website;
            $name = str_replace('http://www.linkedin.com/', '', $name);
            $name = str_replace('http://linkedin.com/', '', $name);
            $link = '<img class="profile_icon" src="/images/profile_icon_linkedin.png" width="16" height="15" alt="" /><a href="'.$this->website.'" title="LinkedIn Profile">'.$name.'</a>';
        } else {
        	$name = $this->website;
            $name = str_replace('http://', '', $name);
            $name = preg_replace('/^www\./', '', $name);
            $link = '<img class="profile_icon" src="/images/profile_icon_website.png" width="16" height="21" alt="" /><a href="'.$this->website.'" title="'.$this->website_type.'">'.$name.'</a>';
        }
        return $link;
   }

   /**
    * This method is used when editing or adding a web site
    * to try its best to rebuilt a working url of the website.
    */
   function rebuiltUrl($website_url, $type="") {
      if (!ereg("^http", $website_url) && !empty($website_url)) {
        if (!empty($type)) {
            if (!ereg("twitter\.com", $website_url) && $type=="Twitter") { 
                $website_url = "twitter.com/".$website_url;
            }
        }
        $website_url = "http://".$website_url;
      }
      return $website_url;
   }

   function eventSaveWebsites(EventControler $evctl)  {
      $mfields = $evctl->mfields;
      $this->setLog("\n ".$this->getPrefix().": Saving multiple Websites");
      $this->idcontact = $_SESSION['ContactEditSave']->idcontact;
      if (is_array($mfields['contact_website'])) { 
	      foreach($mfields['contact_website'] as $primary_key_value=>$fields) {
	        $this->getId($primary_key_value);
	      	$this->website = $this->rebuiltUrl($fields['website'], $fields['website_type']);
	      	$this->website_type = $fields['website_type'];   	
	      	//$this->setPrimaryKeyValue($primary_key_value);
	      	$this->setLog("\n ".$this->getPrefix() .": Updating Website:".$this->website);
	      	$this->update();
	      }
      }
      if (is_array($mfields['contact_website_new'])) {
              $this->idcontact_website = '';
	      foreach($mfields['contact_website_new'] as $fields) {
	        $this->addNew();
	        $this->idcontact = $_SESSION['ContactEditSave']->idcontact;
	      	$this->website = $this->rebuiltUrl($fields['website'], $fields['website_type']);
	      	$this->website_type = $fields['website_type'];
	      	$this->setLog("\n ContactWebsite: Adding Website:".$this->website." ".$fields['website']." Type:".$this->website_type.", for contact:".$this->idcontact);
	      	if (strlen($this->website) > 0) {
	      	    $this->add();
	      	}
	      }
      }
  
   }

    function eventAjaxToggleAutoFetch(EventControler $evctl){
        $idcontact_website = $evctl->idcontact_website;
        $this->getId($idcontact_website);
        if ($this->feed_auto_fetch == 'Yes') {
            $this->feed_auto_fetch = 'No';
            $icon = "/images/feed-icon-12x12-orange.gif";
        } else {
            $idcontact = $this->idcontact;
            $website = $this->website;

            $this->insertNotes($idcontact, $website, $idcontact_website);

            $this->feed_auto_fetch = 'Yes';
            $icon = "/images/feed-icon-12x12-green.png";
        }
        $this->update();
        $evctl->addOutputValue($icon);
    }

    function insertNotes($idcontact, $website, $idcontact_website){
        $f_feed = new Feed();
        $arr_item = $f_feed->retrieveFirstItem($website, $idcontact_website);

		//$arr_item = $arr_items[0];
        //if item in the feed is published after the existing one then insert
        if($this->feed_last_fetch < $arr_item[1]){
            $do_contact_note = new ContactNotes();
            $do_contact_note->idcontact = $idcontact;
            $link = "<br /><a href='".$website."'>"._('Back to the Source of the Article')."</a><br />";

            $search = array('<br />', '<br>','<br >', '<br/>');
            $replace = "\n";
            //Do strip_tag then use nl2br and then remove the extra <br /> tags
            $note_content = $arr_item[0];
            $note_content = nl2br(strip_tags($note_content));
            $note_content = preg_replace('/(<br[^>]*>\s*){2,}/', '<br/>', $note_content);
            $do_contact_note->note = $note_content.$link;
            //$do_contact_note->note = nl2br(strip_tags(str_replace($search, $replace, $arr_item[0]))).$link;
            $do_contact_note->date_added = date('Y-m-d');
            $do_contact_note->iduser = $_SESSION['do_User']->iduser;
            $do_contact_note->type = 'RSS';
            $do_contact_note->add();
			
            $this->getId($idcontact_website);
            $this->feed_last_fetch = $arr_item[1];
			$this->update();
        }
    }

    //cron job method : inserts feed item for the contact websites whose auto fetch is turned on.
    function insertNoteForAutoFetchOn(){
        $q = new sqlQuery($this->getDbCon());
        $q->query("SELECT * FROM contact_website WHERE feed_auto_fetch = 'Yes' AND website_type <> 'Twitter'");
        if($q->getNumRows()){
            $f_feed = new Feed();
           
            while($q->fetch()){
                $do_contact_note = new ContactNotes($this->getDbCon());
				$this->getId($q->getData("idcontact_website"));
				$do_contact = new Contact();
				$do_contact->getId($this->idcontact);
				//print_r($do_contact);
				//exit;
				if (!$do_contact->hasData()) { continue; }
				$do_user = $do_contact->getParentUser();
				if (!$do_user->hasData()) { continue; }
				//print_r($do_user);
				//exit;
                $website = $q->getData("website");
                $website = (substr(ltrim($website), 0, 7) != 'http://' ? 'http://' : '') . $website;
                $arr_item = array();
				
				//try {
					$arr_items = $f_feed->retrieveSinceLastFetch($website, $q->getData("idcontact_website"));
				//}catch(Exception $ex){
				//  $f_feed->turnFeedOff($q->getData("idcontact_website"));
				//}
                if (is_array($arr_items)) {
					foreach ($arr_items as $arr_item){
						if($q->getData("feed_last_fetch") < $arr_item[1]){

							$do_contact_note->idcontact = $q->getData("idcontact");
							$link = "<br /><a href='".$website."' target='_blank'>"._('Back to the Source of the Article')."</a><br />";
							$search = array('<br />', '<br>','<br >', '<br/>');
							$replace = "\n";
							$note_content = $arr_item[0];
							$note_content = nl2br(strip_tags($note_content));
							$note_content = preg_replace('/(<br[^>]*>\s*){2,}/', '<br/>', $note_content);
							$do_contact_note->note = $note_content.$link;
							//$do_contact_note->note = nl2br(strip_tags(str_replace($search, $replace, $arr_item[0]))).$link;
							$do_contact_note->date_added = date('Y-m-d');
							//$do_contact_note->iduser = $do_contact->getIdUser($q->getData("idcontact"));
							$do_contact_note->iduser = $do_user->iduser;
       $do_contact_note->type = 'RSS';
							//$do_contact_note->iduser = 20;
							//$do_contact_note->iduser = $_SESSION['do_User']->iduser;

							  $do_contact_note->add();
												  
							  $do_wf_rss_feed_import = new WorkFeedRssFeedImport();
							  $do_wf_rss_feed_import->addRssFeed($do_contact_note, $website, $note_content);
						}
					}
				}
				
                $this->feed_last_fetch = time();
                $this->update();

                //$do_contact_note->free();
            }
        }
    }

	function setType($type) { 
		$this->website_type = $type;
	}
	function getType() {
		return $this->website_type;
	}


}
?>
