<?php
namespace RadriaCore\Radria\FieldType;

/**
 * Class strFBFieldTypeFloat
 *
 * Inherit everything from strFBFieldTypeChar
 * @package PASClass
 */
class FieldTypeFloat extends FieldTypeChar {
    function default_disp($field_value="") {
        if (!$this->getRData('hidden')) {
            if (strlen($this->getRData('numberformat'))>0) {
                list($prestr, $dec_num,  $dec_sep, $thousands, $poststr) = explode(":", $this->getRData('numberformat'));
                $this->processed .= $prestr.number_format($field_value, $dec_num,  $dec_sep, $thousands).$poststr;
            } else {
                $this->processed .= $field_value;
            }
        }
    }
}