<?php
namespace RadriaCore\Radria\FieldType;


/**
 * Class FieldTypeText
 *
 * Display a textarea box in Form context
 * @package PASClass
 */

Class FieldTypeText extends RegistryFieldStyle
{
    function default_Form($field_value="") {

        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $fieldname = "fields[".$this->getFieldName()."]" ;
            $fval = "<textarea ";
            if (strlen($this->getStyleParam()) > 0) {
                $fval .= $this->getStyleParam();
            } else {
                $fval .= " id=\"".$this->getFieldName()."\"  class=\"adformfield\"";
            }
            $fval .= " name=\"".$fieldname."\"";
            if (strlen($this->getRData('textarea')) > 0) {
                $regparams = explode(":", $this->getRData('textarea')) ;
                $fval .= " rows=\"".$regparams[1]."\" cols=\"".$regparams[0]."\" " ;
            } elseif ($this->rows || $this->cols) {
                $fval .= ' rows="'.$this->rows.'" cols="'.$this->cols.'" ';

            }
            if (strlen($this->getRData('wrap')) > 0 ) { $fval .= " wrap=\"".$this->getRData('wrap')."\"" ;}
            if ($this->getRdata('disabled')) {
                $fval .= " disabled";
            }
            $fval .= ">".htmlentities($this->getFieldValue())."</textarea>\n";
            $this->processed .= $fval;

        }
    }

    function rdataDisp_substring($field_value="") {
        $field_value = substr( $this->getFieldValue(), 0, $this->getRData("substring")) ;
        $this->setFieldValue($field_value);
    }

    function default_Disp($field_value="") {
        $field_value = $this->getFieldValue();
        if (!$this->getRData('hidden')) {
            $val = "";
            if ($this->getRData('html')) {
                $val .= htmlspecialchars($field_value);
            } else {
                if (!$this->getRdata('execute')) {
                    $field_value = $this->no_PhpCode($field_value);
                }
                $val .= $field_value;
            }
            if (strlen($this->getStyleParam()) > 0) {
                $this->processed .= "<span ".$this->getStyleParam().">".$val."</span>";
            } else {
                $this->processed .= $val;
            }
        }
    }
}