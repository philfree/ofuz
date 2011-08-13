<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

/**
 * Class DijitTextBox RegistryField class
 *
 * This is the Dojo / Dijit TextBox for Radria
 *  
 * @package PASClass
 */
Class DijitTimeTextBox extends RegistryFieldBase {
    function default_Form($field_value="") {
        include_once("includes/dojo.dijit.timetextbox.js.inc.php");
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $field_class = "adformfield";
            if (strlen($this->getRData("css_form_class")) > 0) {
               $field_class = $this->getRData("css_form_class");
            }
            if($field_value = '00:00:00' || $field_value ==''){
              $timenow = date("H:i");
              $field_value = $timenow.":00" ;
            }
            $fval .= '<input class="'.$field_class.'" name="timedojofieldname[]" value="T'.htmlentities($field_value).'"
	                dojoType="dijit.form.TimeTextBox"
                        constraints="{timePattern:\'HH:mm:ss\'}"
                        invalidMessage="'.$this->getRData("errormessage").'"
                        required="true" />';
             $fval .= "<input type=\"hidden\" name=\"mydb_events[3]\" value=\"mydb.formatDojoTimeSQLField\"/>" ;
             $fval .= "<input type=\"hidden\" name=\"fieldnamefortime[]\" value=\"$this->field_name\"/>" ;

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