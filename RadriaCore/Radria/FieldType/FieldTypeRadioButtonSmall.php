<?php
namespace RadriaCore\Radria\FieldType;

/**
 * Class strFBFieldTypeRadioButtonSmall RegistryField class
 *
 * Display a list of radio buttons in the Form context,
 * retrive the list or radio button from rdata radiovalues and radiolabels
 * @package PASClass
 * @see strFBFieldTypeListBoxSmall
 */
class FieldTypeRadioButtonSmall extends RegistryFieldStyle
{
    function default_Form($field_value="") {
        if (strlen( $this->getRData('radiovalues'))>0) {
            $values = explode(":", $this->getRData('radiovalues'));
        } else {
            $values = explode(":", $this->getRData('radiolabels'));
        }
        $labels = explode(":", $this->getRData('radiolabels'));

        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            for($i=0; $i<count($labels); $i++) {
                $tmp_selected = "";
                if (trim($values[$i]) == trim($field_value)) { $tmp_selected = " checked" ; }
                $this->processed .= "\n<input type=\"radio\" ";
                if (strlen($this->getStyleParam()) > 0) {
                    $this->processed .= $this->getStyleParam();
                } else {
                    $this->processed .= " id=\"".$this->getFieldName()."\"  class=\"adformfield\"";
                }
                $this->processed .= "name=\"fields[".$this->field_name."]\" value=\"".htmlentities($values[$i])."\"".$tmp_selected." />".$labels[$i];
                if ($this->getRData("vertical") != "no") { $fval.="<br/>"; } else { $fval.="&nbsp;&nbsp;"; }
            }
            $this->processed .= $this->no_PhpCode($this->processed);
        }
    }

    function default_Disp($field_value="") {
        if (!$this->getRData('hidden')) {
            if (strlen( $this->getRData('radiovalues'))>0) {
                $values = explode(":", $this->getRData('radiovalues'));
            } else {
                $values = explode(":", $this->getRData('radiolabels'));
            }
            $labels = explode(":", $this->getRData('radiolabels'));
            for($i=0; $i<count($labels); $i++) {
                if (trim($values[$i]) == trim($field_value)) {  $fval = $labels[$i] ; }
            }
            $this->processed .= $this->no_PhpCode($fval);
        }
    }
}