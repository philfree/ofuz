<?php
namespace RadriaCore\Radria\FieldType;

/**
 * Class strFBFieldTypeTimeSQL RegistryField class
 *
 * In the Form context display a text line field and trigger the EventAction: mydb.formatTimeField reformat and validate the content.
 * @package PASClass
 */
class FieldTypeTimeSQL extends FieldType
{
    function default_Form($field_value="") {
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            list($now, $hidden) = explode(":", $this->getRData('timef')) ;
            list($hour, $min, $sec) = explode(":", $field_value) ;

            if ($now == "now" && (($hour == "00" && $min == "00" && $sec == "00") || (strlen($field_value) < 3))) {
                $field_value = date("H:i:s") ;
            }

            if ($hidden) {
                $fval .="<input type=\"hidden\" name=\"fields[".$this->getFieldName()."]\" value=\"$field_value\" size=\"8\"/>";
            } else {
                $fval .="<input type=\"text\" name=\"fields[".$this->getFieldName()."]\" value=\"$field_value\" size=\"8\"/>";
            }

            $fval .= "<input type=\"hidden\" name=\"timefieldname[]\" value=\"".$this->getFieldName()."\"/>";
            $fval .= "<input type=\"hidden\" name=\"mydb_events[35]\" value=\"mydb.formatTimeField\"/>" ;
            $this->processed .= $fval;
        }
    }

    function default_Disp($field_value="") {
        if (!$this->getRData('hidden')) {
            $this->processed .= $field_value;
        }
    }
}