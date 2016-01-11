<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/**
 * Class DijitTextBox RegistryField class
 *
 * This is the Dojo / Dijit TextBox for Radria
 *  
 * @package PASClass
 */
Class DijitNumberSpinner extends RegistryFieldBase {
    function default_Form($field_value="") {
        include_once("includes/dojo.dijit.numberspinner.js.inc.php");
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $field_class = "adformfield";
            if (strlen($this->getRData("css_form_class")) > 0) {
               $field_class = $this->getRData("css_form_class");
            }
            $minvalue = $this->getRData("minvalue");
            $maxvalue = $this->getRData("maxvalue");
            $interval = $this->getRData("interval");
            if($field_value == 0 || $field_value==''){$field_value=0;}
            $fval .= '<input class="'.$field_class.'" name="fields['.$this->field_name.']" value="'.htmlentities($field_value).'"
	                dojoType="dijit.form.NumberSpinner"
                        constraints="{min:'.$minvalue.',max:'.$maxvalue.',places:0}"
                        smallDelta="'.$interval.'"
                        invalidMessage="'.$this->getRData("errormessage").'"
                        required="true" />';

             $this->processed .= $fval;
        }
    }

     function rdataDisp_substring($field_value="") {
        $field_value = substr( $this->getFieldValue(), 0, $this->getRData("substring")) ;
        $this->setFieldValue($field_value);
    }

    function default_Disp($field_value="") {
    if (!$this->getRData('hidden')) {
            if (!$this->getRdata('execute')) {
                $field_value = $this->no_PhpCode($field_value);
            }
            $this->processed .= $field_value;
        }
    }

}
?>