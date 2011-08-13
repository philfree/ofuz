<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2011 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * Contact class
     * Using the DataObject
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzCore
     * @license ##License##
     * @version 0.6
     * @date 2010-09-03
     * @since 0.1
     */

class ContactPhone extends MultiRecord {
    
    public $table = "contact_phone";
    protected $primary_key = "idcontact_phone";
    protected $prefix = "ContactPhone";  // Should be the same as the class name 
    protected $dropdown_options = Array( "Work", "Home", "Mobile", "Fax", "Other");

   protected function getNewFormFields($new_phone_count) {
      $form  ='
        <input type="text" name="mfields['.$this->getTable().'_new]['.$new_phone_count.'][phone_number]" value="">
        <select name="mfields['.$this->getTable().'_new]['.$new_phone_count.'][phone_type]">';
            foreach ($this->dropdown_options as $phone_option) {
                $form .= "<option>".$phone_option."</option>";
            }
       $form .='</select>';
       return $form;

   }

   protected function getUpdateFormFields() {
        $form .= '<input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][phone_number]" value="'.$this->phone_number.'">';
        $form .= '<select name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][phone_type]">';
        foreach ($this->dropdown_options as $phone_option) {
            if ($phone_option == $this->phone_type) { $selected = " SELECTED"; } else { $selected = ""; }
            $form .= "<option ".$selected.">".$phone_option."</option>";
        }
        $form .= '</select>';
        return $form;
   }     
   function eventSavePhones(EventControler $evctl)  {
      $mfields = $evctl->mfields;
      $this->setLog("\n ".$this->getPrefix().": Saving multiple phones");
      $this->idcontact = $_SESSION['ContactEditSave']->idcontact;
      if (is_array($mfields['contact_phone'])) { 
	      foreach($mfields['contact_phone'] as $primary_key_value=>$fields) {
	        $this->getId($primary_key_value);
	      	$this->phone_number = $fields['phone_number'];
	      	$this->phone_type = $fields['phone_type'];   	
	      	//$this->setPrimaryKeyValue($primary_key_value);
	      	$this->setLog("\n ".$this->getPrefix() .": Updating phone:".$this->phone_number);
	      	$this->update();
	      }
      }
      if (is_array($mfields['contact_phone_new'])) {
              $this->idcontact_phone = '';
	      foreach($mfields['contact_phone_new'] as $fields) {
	        $this->addNew();
	        $this->idcontact = $_SESSION['ContactEditSave']->idcontact;
	      	$this->phone_number = $fields['phone_number'];
	      	$this->phone_type = $fields['phone_type'];
	      	$this->setLog("\n ContactPhone: Adding Phone:".$this->phone_number." ".$fields['phone_number']." Type:".$this->phone_type.", for contact:".$this->idcontact);
	      	if (strlen($this->phone_number) > 0) {
	      	    $this->add();
	      	}
	      }
      }
	  
   } 
   	function setType($type) { 
		$this->phone_type = $type;
	}
	function getType() {
		return $this->phone_type;
	}

}
?>
