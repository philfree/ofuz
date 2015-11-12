<?php
namespace RadriaCore\Radria\FieldType;

/**
 * Class strFBFieldTypeLogin RegistryField class
 *
 * In the Form context display a text line feild. It works with the password field.
 * @package PASClass
 */
class FieldTypeLogin extends FieldTypeChar
{
    function default_Form($field_value="") {
        parent::default_Form($field_value="");
        $this->processed .= "<input name=\"accessfield[login]\" type=\"hidden\" value=\"".$this->getFieldName()."\"/>";
    }
}