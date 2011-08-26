<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/
Class OfuzFieldTypeEncryptedPassword  extends RegistryFieldBase {
    
    function default_Form($field_value="") {
        $do_user_rel = new UserRelations();
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            if (!$this->getRdata('execute')) {
               $field_value = $this->no_PhpCode($field_value);
               $field_value = $do_user_rel->decrypt($field_value);
            }
            if ($this->getRdata("loginform")) {
            	$fval .= "<input type=\"password\" name=\"fields[".$this->getFieldName()."]\" value=\"".$field_value."\"/>" ;
            } else {
	            $fval = "<input name=\"accessfield[".$this->getRData('access')."]\" type=\"hidden\" value=\"".$this->getFieldName()."\"/>";
             $fval .= "<input type=\"hidden\" name=\"mydb_events[22]\" value=\"ofuz.encryptDecryptPassword\">" ;
    	        $fval .= "<input type=\"hidden\" name=\"mydb_events[20]\" value=\"mydb.checkUsernamePassword\">" ;
        	$fval .= "<input type=\"password\" name=\"fields[".$this->getFieldName()."]\" value=\"".$field_value."\"/>" ;
            	$fval .=  "\n<br/><input type=\"password\" name=\"fieldrepeatpass[".$this->getFieldName()."]\" value=\"".$field_value."\"/>"  ;
            }
            $this->processed .= $fval;
        }
    }
    
    function default_Disp($field_value="") {
    if (!$this->getRData('hidden')) {
            if (!$this->getRdata('execute')) {
                $field_value = $this->no_PhpCode($field_value);
                $field_value = $do_user_rel->decrypt($field_value);
            }
            $this->processed .= $field_value;
        }
    }

}
?>