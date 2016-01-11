<?php
namespace RadriaCore\Radria\FieldType;

use RadriaCore\Radria\EventControler;

/**
 * Class FieldTypeCheckBox RegistryField class
 * Possibility:
 * Default:
 *   - checked
 *   - unchecked
 * Want it in the ass: [ ]
 *  1- default: unchecked
user check    = Yes in the DB.
user uncheck  = '' in the DB
 * Serialized sample:
<rfield name="foreColor">
<rdata type="checked_value">Yes</rdata>
<rdata type="unchecked_value">No</rdata>
<rdata type="default">Yes</rdata>
<rdata type="label">Text color</rdata>
<rdata type="fieldtype">FieldTypeCheckBox</rdata>
<rdata type="checkbox">1</rdata>
<rdata type="databasetype">varchar</rdata>
</rfield>
 * Display a checkbox in the Form Context.
 * In Disp context if the box is checked the content of the default value is displayed.
 * @package PASClass
 */
class FieldTypeCheckBox extends FieldType
{
    function default_Form($field_value="") {
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $using_checked_value = false;
            if (strlen($this->getRData("checked_value")) > 0) {
                $checkbox_value = $this->getRData("checked_value");
                $using_checked_value = true;
            } else {
                $checkbox_value = $this->default_value;
            }

            $fval .= "<input type=\"hidden\" name=\"mydb_events[24]\" value=\"FieldTypeCheckBox::eventFormatCheckBoxValue\">";
            $fval .= "<input type=\"hidden\" name=\"checkbox_fields[]\" value=\"".$this->getFieldName()."\">";
            $fval .= "<input type=\"hidden\" name=\"checkbox_uncheck[".$this->getFieldName()."]\" value=\"".$this->getRData("unchecked_value")."\"/>" ;
            $fval .= "\n<input type=\"checkbox\" class=\"adformfield\" id=\"fields_".$this->getFieldName()."\"  name=\"fields[".$this->getFieldName()."]\" value=\"".htmlentities($checkbox_value)."\" ";
            // Old logic, just stay for compatibility reasons
            if ($this->originalval == $this->default_value && !$using_checked_value) { $fval .= " checked=\"yes\" "; }
            // New logic that requires the checked_value rdata to be set.
            if ($field_value == $this->getRData("checked_value")) { $fval .= " checked=\"yes\" "; }
            $fval .= ">";
            $this->setLog("\n".$this->getFieldName()." ".$checkbox_value." == ".$this->default_value." - current value:".$field_value);
            $this->processed .= $fval;
        }
    }
    function default_Disp($field_value="") {
        if (!$this->getRData('hidden')) {
            $this->processed .= $field_value;
        }
    }
    static function eventFormatCheckBoxValue(EventControler $event_controler) {
        $checkbox_fields = $event_controler->checkbox_fields;
        $checkbox_uncheck = $event_controler->checkbox_uncheck;
        $fields = $event_controler->fields;
        foreach ($checkbox_fields as $field_name) {
            if ((strlen($checkbox_uncheck[$field_name]) > 0) && strlen($fields[$field_name]) == 0) {
                $fields[$field_name] = $checkbox_uncheck[$field_name];
            }
        }
        $event_controler->fields = $fields;
    }
}