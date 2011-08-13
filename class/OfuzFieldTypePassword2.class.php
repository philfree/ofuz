<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

Class OfuzFieldTypePassword2  extends RegistryFieldBase {
    function default_Form($field_value="") {
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            if (!$this->getRdata('execute')) {
               $field_value = $this->no_PhpCode($field_value);
            }
            if ($this->getRdata("loginform")) {
            	$fval .= "<input type=\"password\" name=\"fields[".$this->getFieldName()."]\" value=\"".$field_value."\" class=\"loginfield\"/>" ;
            } else {
	            $fval = "<input name=\"accessfield[".$this->getRData('access')."]\" type=\"hidden\" value=\"".$this->getFieldName()."\"/>";
    	        $fval .= "<input type=\"hidden\" name=\"mydb_events[20]\" value=\"mydb.checkUsernamePassword\">" ;
        	    $fval .= "<input type=\"password\" name=\"fields[".$this->getFieldName()."]\" value=\"".$field_value."\" class=\"formfield\"/>" ;
            	$fval .=  "\n<br/><input type=\"password\" name=\"fieldrepeatpass[".$this->getFieldName()."]\" value=\"".$field_value."\" class = \"formfield\"/>"  ;
            }
            $this->processed .= $fval;
        }
    }
    function default_Disp($field_value="") {
    if (!$this->getRData('hidden')) {
            if (!$this->getRdata('execute')) {
                $field_value = $this->no_PhpCode($field_value);
            }
            $this->processed .= $field_value;
        }
    }

}
?>