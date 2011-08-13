<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    /**
     * Contact class
     * Using the DataObject
     */

class CompanyAddress extends MultiRecord {
    
    public $table = "company_address";
    protected $primary_key = "idcompany_address";
    protected $prefix = "CompanyAddress";  // Should be the same as the class name 
    protected $dropdown_options = Array( "Work", "Home", "Other");
 
    protected function getNewFormFields($new_address_count) {
        $form  ='
          Address:<textarea name="mfields['.$this->getTable().'_new]['.$new_address_count.'][address]" rows="6" cols="50"></textarea>&nbsp;
          <select name="mfields['.$this->getTable().'_new]['.$new_address_count.'][address_type]">';
              foreach ($this->dropdown_options as $address_option) {
                  $form .= "<option>".$address_option."</option>";
              }
        $form .='</select><br />';
        //$form  .='
        //  Street :<input type="text" name="mfields['.$this->getTable().'_new]['.$new_address_count.'][street]" value=""><br />';
        //$form  .='
        //  City: <input type="text" name="mfields['.$this->getTable().'_new]['.$new_address_count.'][city]" value=""><br />';
        //$form  .='
        //  State : <input type="text" name="mfields['.$this->getTable().'_new]['.$new_address_count.'][state]" value=""><br />';
        //$form  .='
        //  Country : <input type="text" name="mfields['.$this->getTable().'_new]['.$new_address_count.'][country]" value=""><br />';
        // $form  .='
        //  Zipcode : <input type="text" name="mfields['.$this->getTable().'_new]['.$new_address_count.'][zipcode]" value=""><br />';
        return $form;
  
    }
  
    protected function getUpdateFormFields() {
          
          $form .= '<select name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][address_type]" rows="6" cols="50">';
          foreach ($this->dropdown_options as $address_option) {
              if ($address_option == $this->address_type) { $selected = " SELECTED"; } else { $selected = ""; }
              $form .= "<option ".$selected.">".$address_option."</option>";
          }
          $form .= '</select><br />';
          $form .= '<table>';
          $form .= '<tr><td>'._('Address:').'</td><td><textarea name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][address]">'.$this->address.'</textarea></td></tr>';
          //$form .= '<tr><td>Street: </td><td><input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][street]" value="'.$this->street.'"></td></tr>';
          //$form .= '<tr><td>City: </td><td><input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][city]" value="'.$this->city.'"></td></tr>';
          //$form .= '<tr><td>State: </td><td><input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][state]" value="'.$this->state.'"></td></tr>';
          //$form .= '<tr><td>Country: </td><td><input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][country]" value="'.$this->country.'"></td></tr>';
          //$form .= '<tr><td>Zipcode: </td><td><input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][zipcode]" value="'.$this->zipcode.'"></td></tr>';
          $form .= '</table>';
          return $form;
    }     
    function eventSaveContactAddress(EventControler $evctl)  {
        $mfields = $evctl->mfields;
        $this->setLog("\n ".$this->getPrefix().": Saving multiple addresses");
        $this->idcompany = $_SESSION['CompanyEditSave']->idcompany;
        if (is_array($mfields['company_address'])) { 
                foreach($mfields['company_address'] as $primary_key_value=>$fields) {
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
        if (is_array($mfields['company_address_new'])) {
                $this->idcompany_address = '';
                foreach($mfields['company_address_new'] as $fields) {
                  $this->addNew();
                  $this->idcompany = $_SESSION['CompanyEditSave']->idcompany;
                  $this->address = $fields['address'];
                  $this->address_type = $fields['address_type'];
                  $this->street = $fields['street'];
                  $this->city = $fields['city'];
                  $this->state = $fields['state'];
                  $this->country = $fields['country'];
                  $this->zipcode = $fields['zipcode'];
                  $this->setLog("\n ContactAddress: Adding Address:".$this->address." ".$fields['address']." Type:".$this->address_type.", for contact:".$this->idcompany);
                  if (strlen($this->address) > 0) {
                      $this->add();
                  }
                }
        }
    
    }


}
?>
