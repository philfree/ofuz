<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * Contact class
     * Using the DataObject
     */

class ContactAddress extends MultiRecord {
    
    public $table = "contact_address";
    protected $primary_key = "idcontact_address";
    protected $prefix = "ContactAddress";  // Should be the same as the class name 
    protected $dropdown_options = Array( "Work", "Home", "Other");
 
    protected function getNewFormFields($new_address_count) {

        $form ='  <select name="mfields['.$this->getTable().'_new]['.$new_address_count.'][address_type]">';
              foreach ($this->dropdown_options as $address_option) {
                  $form .= "<option>".$address_option."</option>";
              }
        $form .='</select><br />';
        //$form  .='<table><tr><td>
        //  Address : </td><td>';
		$form .= '<textarea name="mfields['.$this->getTable().'_new]['.$new_address_count.'][address]" rows="6" cols="50"></textarea>';
		//$form .= '</td></tr>';
       // $form  .='
        //  <tr><td>Street :</td><td><input type="text" name="mfields['.$this->getTable().'_new]['.$new_address_count.'][street]" value=""></td></tr>';
        //$form  .='
       //   <tr><td>City :</td><td><input type="text" name="mfields['.$this->getTable().'_new]['.$new_address_count.'][city]" value=""></td></tr>';
       // $form  .='
       //   <tr><td>State: </td><td><input type="text" name="mfields['.$this->getTable().'_new]['.$new_address_count.'][state]" value=""></td></tr>';
       // $form  .='
       //   <tr><td>Country :</td><td> <input type="text" name="mfields['.$this->getTable().'_new]['.$new_address_count.'][country]" value=""></td></tr>';
       //  $form  .='
       //   <tr><td>Zipcode :</td><td><input type="text" name="mfields['.$this->getTable().'_new]['.$new_address_count.'][zipcode]" value=""></td></tr>';
        // $form .='</table>';
        return $form;
  
    }
  
    protected function getUpdateFormFields() {
          $form .= '<select name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][address_type]">';
          foreach ($this->dropdown_options as $address_option) {
              if ($address_option == $this->address_type) { $selected = " SELECTED"; } else { $selected = ""; }
              $form .= "<option ".$selected.">".$address_option."</option>";
          }
          $form .= '</select><br />';
          //$form .= '<table>';
          //$form .= '<tr><td></td><td>';
		  $form .= '<textarea name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][address]" rows="6" cols="50">'.$this->address.'</textarea>';
		  //$form .= '</td></tr>';

          //$form .= '<tr><td>Street :</td><td><input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][street]" value="'.$this->street.'"></td></tr>';
          //$form .= '<tr><td>City:</td><td> <input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][city]" value="'.$this->city.'"></td></tr>';
          //$form .= '<tr><td>State: </td><td><input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][state]" value="'.$this->state.'"></td></tr>';
          //$form .= '<tr><td>Country: </td><td><input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][country]" value="'.$this->country.'"></td></tr>';
          //$form .= '<tr><td>Zipcode: </td><td><input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][zipcode]" value="'.$this->zipcode.'"></td></tr>';
          //$form .= '</table>';
          return $form;
    }     
    function eventSaveContactAddress(EventControler $evctl)  {
        $mfields = $evctl->mfields;
        $this->setLog("\n ".$this->getPrefix().": Saving multiple addresses");
        $this->idcontact = $_SESSION['ContactEditSave']->idcontact;
        if (is_array($mfields['contact_address'])) { 
                foreach($mfields['contact_address'] as $primary_key_value=>$fields) {
                  $this->getId($primary_key_value);
                  $this->address = $fields['address'];
                  $this->address_type = $fields['address_type'];
                  $this->street = $fields['street'];
                  $this->city = $fields['city'];
                  $this->state = $fields['state'];
                  $this->country = $fields['country'];
                  $this->zipcode = $fields['zipcode'];
                  //$this->setPrimaryKeyValue($primary_key_value);
                  $this->setLog("\n ".$this->getPrefix() .": Updating Contact Address:".$this->address);
                  $this->update();
                }
        }
        if (is_array($mfields['contact_address_new'])) {
                $this->idcontact_address = '';
                foreach($mfields['contact_address_new'] as $fields) {
                  $this->addNew();
                  $this->idcontact = $_SESSION['ContactEditSave']->idcontact;
                  $this->address = $fields['address'];
                  $this->address_type = $fields['address_type'];
                  $this->street = $fields['street'];
                  $this->city = $fields['city'];
                  $this->state = $fields['state'];
                  $this->country = $fields['country'];
                  $this->zipcode = $fields['zipcode'];
                  $this->setLog("\n ContactAddress: Adding Address:".$this->address." ".$fields['address']." Type:".$this->address_type.", for contact:".$this->idcontact);
                  if (strlen($this->address) > 0) {
                      $this->add();
                  }
                }
        }
    
    }
	function setType($type) { 
		$this->address_type = $type;
	}
	function getType() {
		return $this->address_type;
	}

}
?>
