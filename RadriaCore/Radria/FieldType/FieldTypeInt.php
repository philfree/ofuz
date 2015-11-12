<?php
namespace RadriaCore\Radria\FieldType;
/**
 * Class strFBFieldTypeInt
 *
 * Inherit everything from strFBFieldTypeChar
 * @package PASClass
 */
Class FieldTypeInt extends  FieldTypeChar {
    function default_disp($field_value="") {
        if (!$this->getRData('hidden')) {
            $val ="";
            if (strlen($this->getRData('numberformat'))>0) {
                list($prestr, $dec_num, $dec_sep, $thousands,  $poststr) = explode(":", $this->getRData('numberformat'));
                $val = $prestr.number_format($this->getFieldValue(), $dec_num, $dec_sep, $thousands).$poststr;
            } else {
                $val = $this->getFieldValue();
            }
            if (!$this->getRdata('execute')) {
                $val = $this->no_PhpCode($val);
            }
            if (strlen($this->getStyleParam()) > 0) {
                $this->processed .= "<span ".$this->getStyleParam().">".$val."</span>";
            } else {
                $this->processed .= $val;
            }
        }
    }
}