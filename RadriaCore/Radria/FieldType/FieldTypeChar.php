<?php
namespace RadriaCore\Radria\FieldType;

/**
 * Class FieldTypeChar
 *
 * This is the default field type.
 * Its used when no other field type are set and it extends fields type that
 * doesn't need more than a textline field.
 * Also if no textline rdata are set the default will fallback to textline,
 * @package PASClass
 */

class FieldTypeChar extends RegistryFieldStyle
{
    var $style_param = "";
    function rdataForm_textline($field_value="") {
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $regparams = explode(":", $this->getRData('textline')) ;
            $this->addStyleParam(" size=\"$regparams[0]\"  maxlength=\"$regparams[1]\"");
            if (!$this->getRData('execute'))  {
                $field_value = $this->no_PhpCode($field_value);
            }
            $this->processed  .= "<input  type=\"text\" name=\"fields[".$this->getFieldName()."]\" value=\"".htmlentities($field_value)."\"";
            if (strlen($this->getStyleParam()) > 0) {
                $this->processed .= $this->getStyleParam();
            } else {
                $this->processed .= " id=\"".$this->getFieldName()."\"  class=\"adformfield\"";
            }
            if ($this->getRdata('disabled')) {
                $this->processed .= " disabled";
            }
            $this->processed .= "/>";
        }
    }

    function default_Form($field_value="") {
        $field_value = $this->getFieldValue();
        if (!$this->getRData('hidden') && !$this->getRData('readonly') && !$this->getRData('textline')) {
            if (!$this->getRData('execute'))  {
                $field_value = $this->no_PhpCode($field_value);
            }
            if (strlen($this->getRData("size")) > 0) {
                $this->addStyleParam(" size=\"".$this->getRData("size")."\"");
            }
            if (strlen($this->getRData("maxlength")) > 0) {
                $this->addStyleParam(" maxlength=\"".$this->getRData("maxlength")."\"");
            }
            $this->processed  .= "<input  type=\"text\" name=\"fields[".$this->getFieldName()."]\" value=\"".htmlentities($field_value)."\"";
            if (strlen($this->getStyleParam()) > 0) {
                $this->processed .= $this->getStyleParam();
            } else {
                $this->processed .= " id=\"".$this->getFieldName()."\"  class=\"adformfield\"";
            }
            if ($this->getRdata('disabled')) {
                $this->processed .= " disabled";
            }
            $this->processed .= "/>";

        }
    }

    function rdataDisp_substring($field_value="") {
        $field_value = substr( $this->getFieldValue(), 0, $this->getRData("substring")) ;
        $this->setFieldValue($field_value);
    }

    function default_Disp($field_value="") {
        $field_value = $this->getFieldValue();
        if (!$this->getRData('hidden')) {
            if (!$this->getRData('execute'))  {
                $field_value = $this->no_PhpCode($field_value);
            }
            if (strlen($this->getStyleParam()) > 0) {
                $this->processed .= "<span ".$this->getStyleParam().">".$field_value."</span>";
            } else {
                $this->processed .= $field_value;
            }
        }
    }
}
