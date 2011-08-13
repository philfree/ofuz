<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

/**
 * Class DijitDateTextBox RegistryField class
 *
 * This is the Dojo / Dijit DateTextBox for Radria
 *
  <rfield name="due_date">
    <rdata type="databasetype"></rdata>
    <rdata type="label">Due Date</rdata>
    <rdata type="datetype">dd-MM-y</rdata>
    <rdata type="errormessage"></rdata>
    <rdata type="css_form_class"></rdata>
    <rdata type="fieldtype">DijitDateTextBox</rdata>
  </rfield>
 * 
 * @package PASClass
 */ 
Class DijitDateTextBox extends RegistryFieldBase {
    function default_Form($field_value="") {
        
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $date_text_box_name = $this->field_name;
            $field_class = "adformfield";
            if (strlen($this->getRData("css_form_class")) > 0) {
               $field_class = $this->getRData("css_form_class");
            }
            if ($this->getRData("required") == "1") {
               $required_param = "required=\"true\"";
            } else {
                $required_param = "";
            }
            if($field_value == ""){
                $field_value = date("Y-m-d");
            }

            $date_text_box_name = "dijit.form.DateTextBox";
            include_once("includes/dojo.dijit.datetextbox.js.inc.php");
            $fval .= '<input class="'.$field_class.'" name="datedojofieldname[]" 
                        id = "'.$this->field_name.'"
                        value="'.htmlentities($field_value).'"
	                dojoType ="'.$date_text_box_name.'"
                        invalidMessage="'.$this->getRData("errormessage").'"
                        '.$required_param.' />';
             $fval .= "<input type=\"hidden\" name=\"mydb_events[4]\" value=\"mydb.formatDojoDateSQLField\"/>" ;
             $fval .= "<input type=\"hidden\" name=\"dformat[]\" value=\"".$this->getRData("datetype")."\"/>";
             $fval .= "<input type=\"hidden\" name=\"fieldnamefordate[]\" value=\"$this->field_name\"/>" ;

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
                $date_from_db = $field_value;
                if($date_from_db != ''){
                  $date_from_db = explode("-", $date_from_db) ;
                  $day = $date_from_db[2] ;
                  $mon = $date_from_db[1] ;
                  $year = $date_from_db[0] ;
                  if($this->getRData("datetype") == 'dd-MM-y'){
                    $field_value = $day."/".$mon."/".$year; 
                  }
                  if($this->getRData("datetype") == 'y-MM-dd'){
                      $field_value = $year."/".$mon."/".$day;
                  }
                }else{$field_value = '';}
            }
            $this->processed = $field_value;
        }
    }

}
?>
