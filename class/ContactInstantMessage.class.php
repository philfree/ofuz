<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Contact class
     * Using the MultiRecord based DataObject
     */

class ContactInstantMessage extends MultiRecord {
    
    public $table = "contact_instant_message";
    protected $primary_key = "idcontact_instant_message";
    protected $prefix = "ContactInstantMessage";  // Should be the same as the class name 
    protected $dropdown_options = Array( "Work", "Personal", "Other");
    protected $dropdown_type = Array("AIM", "MSN", "ICQ", "Jabber", "Yahoo", "Skype", "Google Talk", "Other"); 

   protected function getNewFormFields($new_im_count) {
      $form  ='
        <input type="text" name="mfields['.$this->getTable().'_new]['.$new_im_count.'][im_username]" value="">
        <select name="mfields['.$this->getTable().'_new]['.$new_im_count.'][im_options]">';
            foreach ($this->dropdown_options as $im_options) {
                $form .= "<option>".$im_options."</option>";
            }
       $form .='</select>';
       $form .= '<select name="mfields['.$this->getTable().'_new]['.$new_im_count.'][im_type]">';
                  foreach ($this->dropdown_type as $im_type) {
                      $form .= "<option>".$im_type."</option>";
                  }
       $form .='</select>';
       return $form;

   }

   protected function getUpdateFormFields() {
        $form .= '<input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][im_username]" value="'.$this->im_username.'">';
        $form .= '<select name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][im_options]">';
        foreach ($this->dropdown_options as $im_options) {
            if ($im_options == $this->im_options) { $selected = " SELECTED"; } else { $selected = ""; }
            $form .= "<option ".$selected.">".$im_options."</option>";
        }
        $form .= '</select>';
        $form .= '<select name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][im_type]">';
        foreach ($this->dropdown_type as $im_type) {
            if ($im_type == $this->im_type) { $selected = " SELECTED"; } else { $selected = ""; }
            $form .= "<option ".$selected.">".$im_type."</option>";
        }
        $form .= '</select>';
        return $form;
   }     
   function eventSaveIM(EventControler $evctl)  {
      $mfields = $evctl->mfields;
      $this->setLog("\n ".$this->getPrefix().": Saving multiple IMS");
      $this->idcontact = $_SESSION['ContactEditSave']->idcontact;
      if (is_array($mfields['contact_instant_message'])) { 
	      foreach($mfields['contact_instant_message'] as $primary_key_value=>$fields) {
	        $this->getId($primary_key_value);
	      	$this->im_options = $fields['im_options'];
	      	$this->im_type = $fields['im_type'];   	
                $this->im_username = $fields['im_username'];
	      	//$this->setPrimaryKeyValue($primary_key_value);
	      	$this->setLog("\n ".$this->getPrefix() .": Updating IM:".$this->im_username);
	      	$this->update();
	      }
      }
      if (is_array($mfields['contact_instant_message_new'])) {
              $this->idcontact_instant_message = '';
	      foreach($mfields['contact_instant_message_new'] as $fields) {
	        $this->addNew();
	        $this->idcontact = $_SESSION['ContactEditSave']->idcontact;
	      	$this->im_options = $fields['im_options'];
	      	$this->im_type = $fields['im_type'];
                $this->im_username = $fields['im_username'];
	      	$this->setLog("\n ContactPhone: Adding Phone:".$this->im_username." ".$fields['im_username']." Type:".$this->im_type.", for contact:".$this->idcontact);
	      	if (strlen($this->im_username) > 0) {
	      	    $this->add();
	      	}
	      }
      }
  
   } 
   	function setType($type) { 
		$this->im_type = $type;
	}
	function getType() {
		return $this->im_type;
	}

}
?>
