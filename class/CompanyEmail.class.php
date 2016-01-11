<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Contact class
     * Using the DataObject
     */

class CompanyEmail extends MultiRecord {
    
    public $table = "company_email";
    protected $primary_key = "idcompany_email";
    protected $prefix = "CompanyEmail";  // Should be the same as the class name 
    protected $dropdown_options = Array( "Work", "Home", "Other");
 
   protected function getNewFormFields($new_count) {
      $form  ='
        <input type="text" name="mfields['.$this->getTable().'_new]['.$new_count.'][email_address]" value="">
        <select name="mfields['.$this->getTable().'_new]['.$new_count.'][email_type]">';
            foreach ($this->dropdown_options as $email_option) {
                $form .= "<option>".$email_option."</option>";
            }
       $form .='</select>';
       return $form;

   }

   protected function getUpdateFormFields() {
        $form .= '<input type="text" name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][email_address]" value="'.$this->email_address.'">';
        $form .= '<select name="mfields['.$this->getTable().']['.$this->getPrimaryKeyValue().'][email_type]">';
        foreach ($this->dropdown_options as $email_option) {
            if ($email_option == $this->email_type) { $selected = " SELECTED"; } else { $selected = ""; }
            $form .= "<option ".$selected.">".$email_option."</option>";
        }
        $form .= '</select>';
        return $form;
   }  

   
   function eventSaveEmails(EventControler $evctl)  {
        $mfields = $evctl->mfields;
        $this->setLog("\n ".$this->getPrefix().": Saving multiple emails");
        $this->idcompany = $_SESSION['CompanyEditSave']->idcompany;
        if (is_array($mfields['company_email'])) { 
        foreach($mfields['company_email'] as $primary_key_value=>$fields) {
            $this->getId($primary_key_value);
            $this->email_address = $fields['email_address'];
            $this->email_type = $fields['email_type'];   	
            //$this->setPrimaryKeyValue($primary_key_value);
            $this->setLog("\n ".$this->getPrefix() .": Updating email:".$this->email_address);
            $this->update();
}
      }
      if (is_array($mfields['company_email_new'])) {
            $this->idcompany_email = '';
            foreach($mfields['company_email_new'] as $fields) {
                $this->addNew();
                $this->idcompany = $_SESSION['CompanyEditSave']->idcompany;
                $this->email_address = $fields['email_address'];
                $this->email_type = $fields['email_type'];
                $this->setLog("\n ContactEmail: Adding Email:".$this->email_address." ".$fields['email_address']." Type:".$this->email_type.", for contact:".$this->idcompany);
                if (strlen($this->email_address) > 0) {
                    $this->add();
                }
            }
      }
  
   }
}
?>