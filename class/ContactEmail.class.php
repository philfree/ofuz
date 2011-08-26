<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * ContactEmail class
     * Using the DataObject and MultiRecord
     * The form uses a xfields for the radrio button
     * To set one of the emails as default.
     * @note it sets y for Yes and n for No
     */
	
	/** 
	 * table structure:
+-----------------+--------------+------+-----+---------+----------------+
| Field           | Type         | Null | Key | Default | Extra          |
+-----------------+--------------+------+-----+---------+----------------+
| idcontact_email | int(10)      | NO   | PRI | NULL    | auto_increment | 
| idcontact       | int(10)      | NO   | MUL |         |                | 
| email_address   | varchar(180) | NO   |     |         |                | 
| email_type      | varchar(50)  | NO   |     |         |                | 
| email_isdefault | char(1)      | NO   |     | n       |                | 
+-----------------+--------------+------+-----+---------+----------------+
      */


class ContactEmail extends MultiRecord {
    
    public $table = "contact_email";
    protected $primary_key = "idcontact_email";
    protected $prefix = "ContactEmail";  // Should be the same as the class name 
    protected $dropdown_options = Array( "Work", "Home", "Other");
 
   protected function getNewFormFields($new_count) {
      $form  ='
        <input type="text" name="mfields['.$this->getTable().'_new]['.$new_count.'][email_address]" value="">
        <select name="mfields['.$this->getTable().'_new]['.$new_count.'][email_type]">';
            foreach ($this->dropdown_options as $email_option) {
                $form .= "<option>".$email_option."</option>";
            }
       $form .='</select>';
       $form .='<input type="radio" name="xfields['.$this->getTable().'][email_default]" value="'.$new_count.'" title="Default">';
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
        if ($this->email_isdefault == 'y') { $default_select = " checked"; } else { $default_select = ""; }
        $form .='<input type="radio" name="xfields['.$this->getTable().'][email_default]" value="'.$this->getPrimaryKeyValue().'" title="Default"'.$default_select.'>';
        return $form;
   }  

   
   function eventSaveEmails(EventControler $evctl)  {
        $mfields = $evctl->mfields;
        $default_email = $evctl->xfields['contact_email']['email_default'];
        $this->setLog("\n ".$this->getPrefix().": Saving multiple emails");
        $this->idcontact = $_SESSION['ContactEditSave']->idcontact;
        if (is_array($mfields['contact_email'])) { 
            foreach($mfields['contact_email'] as $primary_key_value=>$fields) {
                $this->getId($primary_key_value);
                $this->email_address = $fields['email_address'];
                $this->email_type = $fields['email_type'];   	
                if ($primary_key_value == $default_email) {
                    $this->email_isdefault = 'y';
                } else {
                    $this->email_isdefault = 'n';
                }
                //$this->setPrimaryKeyValue($primary_key_value);
                $this->setLog("\n ".$this->getPrefix() .": Updating email:".$this->email_address);
                $this->update();
            }
        }
        if (is_array($mfields['contact_email_new'])) {
            $this->idcontact_email = '';
            foreach($mfields['contact_email_new'] as $new_count => $fields) {
                $this->addNew();
                $this->idcontact = $_SESSION['ContactEditSave']->idcontact;
                $this->email_address = $fields['email_address'];
                $this->email_type = $fields['email_type'];
                if ($new_count == $default_email) {
                    $this->email_isdefault = 'y';
                } else {
                    $this->email_isdefault = 'n';
                }
                $this->setLog("\n ContactEmail: Adding Email:".$this->email_address." ".$fields['email_address']." Type:".$this->email_type.", for contact:".$this->idcontact);
                if (strlen($this->email_address) > 0) {
                    $this->add();
                }
            }
        }
		
   }
   /**
    * getDefaultEmail()
    * Return the default email from a list of emails
	* from a contact.
	*/
   function getDefaultEmail() {
		if ($this->getNumRows() > 1) {
			while ($this->next()) {
			   if ($this->email_isdefault == 'y') {
				   return $this->email_address;
			   }
			}
			$this->first();
			return $this->email_address;
		} else { return $this->email_address; }
	}
	
	function setType($type) { 
		$this->email_type = $type;
	}
	function getType() {
		return $this->email_type;
	}

	function getContactEmails($idcontact) {
		$sql = "SELECT email_address
				FROM {$this->table}
				WHERE
				idcontact = {$idcontact}
			";
		$this->query($sql);
		$arr_contact_emails = array();
		if($this->getNumRows()){
			while($this->next()) {
				$arr_contact_emails[] = $this->getData("email_address");
			}
		}
		return $arr_contact_emails;
	}

	
}
?>
