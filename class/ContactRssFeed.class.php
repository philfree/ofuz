<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Contact class
     * Using the DataObject
     */

class ContactRssFeed extends MultiRecord {
    
    public $table = "contact_rss_feed";
    protected $primary_key = "idcontact_rss_feed";
    protected $prefix = "ContactRssFeed";  // Should be the same as the class name 
    protected $dropdown_options = Array( "blog", "twitter", "flicker", "youtube");

   protected function getNewFormFields($new_rssfeed_count) {
      $form  ='
        <input type="text" name="mfields['.$this->getTable().'_new]['.$new_rssfeed_count.'][rss_feed_url]" value="">
        <select name="mfields['.$this->getTable().'_new]['.$new_rssfeed_count.'][feed_type]">';
            foreach ($this->dropdown_options as $rssfeed_opt) {
                $form .= "<option>".$rssfeed_opt."</option>";
            }
       $form .='</select>';
       
       $form .='<input type="text" name="mfields['.$this->getTable().'_new]['.$new_rssfeed_count.'][username]" value="">';
       $form .='<input type="checkbox" name="mfields['.$this->getTable().'_new]['.$new_rssfeed_count.'][import_to_note]" value="Yes">';

       return $form;

   }

   protected function getUpdateFormFields() {
        $form .= '<input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][rss_feed_url]" value="'.$this->rss_feed_url.'">';
        $form .= '<select name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][feed_type]">';
        foreach ($this->dropdown_options as $feed_type) {
            if ($feed_type == $this->feed_type) { $selected = " SELECTED"; } else { $selected = ""; }
            $form .= "<option ".$selected.">".$feed_type."</option>";
        }
        $form .= '</select>';
        $form .= '<input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][username]" value="'.$this->username.'">';
        $cheked = '';
        if($this->import_to_note == 'Yes'){ $checked = "checked";}
        $form .= '<input type="checkbox" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][import_to_note]" value="'.$this->import_to_note.'"'.$checked.'>';
        
        return $form;
   }     
   function eventSaveRssFeed(EventControler $evctl)  {
      $mfields = $evctl->mfields;
      $this->setLog("\n ".$this->getPrefix().": Saving multiple Rss Feeds");
      $this->idcontact = $_SESSION['ContactEditSave']->idcontact;
      if (is_array($mfields['contact_rss_feed'])) { 
	      foreach($mfields['contact_rss_feed'] as $primary_key_value=>$fields) {
	        $this->getId($primary_key_value);
	      	$this->rss_feed_url = $fields['rss_feed_url'];
	      	$this->feed_type = $fields['feed_type'];   	
	      	$this->username = $fields['username'];   	
                $this->import_to_note = $fields['import_to_note'];   	
	      	$this->setLog("\n ".$this->getPrefix() .": Updating Rss Feed:".$this->rss_feed_url);
	      	$this->update();
	      }
      }
      if (is_array($mfields['contact_rss_feed_new'])) {
              $this->idcontact_rss_feed = '';
	      foreach($mfields['contact_rss_feed_new'] as $fields) {
	        $this->addNew();
	        $this->idcontact = $_SESSION['ContactEditSave']->idcontact;
	      	$this->rss_feed_url = $fields['rss_feed_url'];
	      	$this->feed_type = $fields['feed_type'];   	
	      	$this->username = $fields['username'];   	
                $this->import_to_note = $fields['import_to_note'];  
	      	$this->setLog("\n ContactRssFeed: Adding Rss Feed:".$this->rss_feed_url." ".$fields['rss_feed_url']." Type:".$this->feed_type.", for contact:".$this->idcontact);
	      	if (strlen($this->rss_feed_url) > 0) {
	      	    $this->add();
	      	}
	      }
      }
  
   } 
}
?>