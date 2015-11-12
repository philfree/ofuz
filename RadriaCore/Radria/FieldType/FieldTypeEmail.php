<?php
namespace RadriaCore\Radria\FieldType;

/**
 * Class strFBFieldTypeEmail RegistryField class
 *
 * In the Form context trigger the EventAction: mydb.checkEmail to check the value field in is a real  email address.
 * In the Disp context display the email content around a mailto: link.
 * @package PASClass
 */
class FieldTypeEmail extends FieldTypeChar
{
    function default_Form($field_value="")
    {
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $this->processed .= "<input type=\"hidden\" name=\"mydb_events[7]\" value=\"mydb.checkEmail\"/>" ;
            $this->processed .="<input name=\"emailfield[]\" type=\"hidden\" value=\"".$this->field_name."\"/>" ;
        }
    }

    function default_Disp($field_value="")
    {
        if (!$this->getRData('hidden')) {
            if (!$this->getRdata('execute')) {
                $field_value = $this->no_PhpCode($field_value);
            }
            $this->processed .= "<a class=\"mailtolink\" href=\"mailto:".$field_value."\">".$field_value."</a>" ;
        }
    }
}