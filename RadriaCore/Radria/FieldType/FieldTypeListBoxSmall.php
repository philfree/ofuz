<?php
namespace RadriaCore\Radria\FieldType;

/**
 * Class strFBFieldTypeListBoxSmall RegistryField class
 *
 * Display a drop down (SELECT) in the Form context.
 * The content of the drop down are from the rdata listvalues and listkeys
 * @package PASClass
 */
Class FieldTypeListBoxSmall extends FieldType {
    function default_Form($field_value="") {
        if (strlen($this->getRData('listvalues'))>0) {
            $values = explode(":", $this->getRData('listvalues'));
        } else {
            $values = explode(":", $this->getRData('listlabels'));
        }
        $labels = explode(":", $this->getRData('listlabels'));
        if (strlen($field_value) > 0) {
            $defaultvalue = $field_value;
        } else {
            $defaultvalue = $this->default_value;
        }
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $fval = "<select class=\"adformfield\" name=\"fields[".$this->field_name."]\">\n" ;
            if ($this->getRData('emptydefault') != "no") {
                $fval .= "<option value=\"\"></option>";
            }
            for($i=0; $i<count($labels); $i++) {
                $tmp_selected = "";
                if (trim($values[$i]) == trim($defaultvalue)) { $tmp_selected = " selected" ; }
                $fval .= "\n<option value=\"".htmlentities($values[$i])."\"".$tmp_selected.">".$labels[$i]."</option>" ;
            }
            $fval .= "</select>";
            $this->processed .= $this->no_PhpCode($fval);
        }
    }

    function default_Disp($field_value="") {
        if (!$this->getRData('hidden')) {
            if (strlen( $this->getRData('listvalues'))>0) {
                $values = explode(":", $this->getRData('listvalues'));
            } else {
                $values = explode(":", $this->getRData('listlabels'));
            }
            $labels = explode(":", $this->getRData('listlabels'));
            for($i=0; $i<count($labels); $i++) {
                if (trim($values[$i]) == trim($field_value)) {  $fval = $labels[$i] ; }
            }
            if (!$this->getRdata('execute')) {
                $fval = $this->no_PhpCode($fval);
            }
            $this->processed .= $fval;
        }
    }
}