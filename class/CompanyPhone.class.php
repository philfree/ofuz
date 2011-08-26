<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Contact class
     * Using the DataObject
     */

class CompanyPhone extends MultiRecord {
    
    public $table = "company_phone";
    protected $primary_key = "idcompany_phone";
    protected $prefix = "CompanyPhone";  // Should be the same as the class name 
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
      $this->idcompany = $_SESSION['CompanyEditSave']->idcompany;
      if (is_array($mfields['company_phone'])) { 
	      foreach($mfields['company_phone'] as $primary_key_value=>$fields) {
	        $this->getId($primary_key_value);
	      	$this->phone_number = $fields['phone_number'];
	      	$this->phone_type = $fields['phone_type'];
	      	$this->setLog("\n ".$this->getPrefix() .": Updating phone:".$this->phone_number);
	      	$this->update();
	      }
      }
      if (is_array($mfields['company_phone_new'])) {
              $this->idcompany_phone = '';
	      foreach($mfields['company_phone_new'] as $fields) {
	        $this->addNew();
	        $this->idcompany = $_SESSION['CompanyEditSave']->idcompany;
	      	$this->phone_number = $fields['phone_number'];
	      	$this->phone_type = $fields['phone_type'];
	      	$this->setLog("\n CompanyPhone: Adding Phone:".$this->phone_number." ".$fields['phone_number']." Type:".$this->phone_type.", for contact:".$this->idcompany);
	      	if (strlen($this->phone_number) > 0) {
	      	    $this->add();
	      	}
	      }
      }
  
   } 
}
?>