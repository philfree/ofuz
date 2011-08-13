<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

/**
 * Class DijitCheckBox RegistryField class
 *
 * This is the Dojo / Dijit DijitRadioButton for Radria
 * 
 * @package PASClass
 */ 
Class DijitRadioButton extends RegistryFieldBase {
     function default_Form($field_value="") {
        include_once("includes/dojo.dijit.checkbox.js.inc.php");
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $field_class = "adformfield";
            if (strlen($this->getRData("css_form_class")) > 0) {
               $field_class = $this->getRData("css_form_class");
            }
            $using_checked_value = '';
            if (strlen($this->getRData("checked_value")) > 0) {
                $checkbox_value = $this->getRData("checked_value");
                if($checkbox_value == $this->getRData("default_value")){
                     $using_checked_value ='checked ="checked"';
                }
            } else {
                $checkbox_value = $this->default_value;
            }
            if($field_value == $this->getRData("checked_value")){$using_checked_value ='checked ="checked"';}
            $fval .= '<input class="'.$field_class.'" name="fields['.$this->field_name.']" value="'.htmlentities($checkbox_value).'"
	                dojoType="dijit.form.RadioButton"
                        '.$using_checked_value.'
                        type="checkbox"
                        />';
             $this->processed .= $fval;
        }
    }

     function rdataDisp_substring($field_value="") {
        $field_value = substr( $this->getFieldValue(), 0, $this->getRData("substring")) ;
        $this->setFieldValue($field_value);
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