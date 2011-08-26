<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/**
 * Class OfuzHorizontalSlider
 *
 * Uses customized onchange() for Ofuz
 * 
 */
class OfuzHorizontalSlider extends DijitHorizontalSlider {
    function default_Form($field_value="") {
        include_once("includes/dojo.dijit.horizontalslider.js.inc.php");
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $field_class = "adformfield";
            if (strlen($this->getRData("css_form_class")) > 0) {
               $field_class = $this->getRData("css_form_class");
            }
            $minvalue = $this->getRData("minvalue");
            $maxvalue = $this->getRData("maxvalue");
            $interval = $this->getRData("interval");
            if($minvalue == 0 || $minvalue == ''){$minvalue = 0;}
            if($maxvalue == 0 || $maxvalue == ''){$maxvalue = 100;}
            if($interval == 0 || $interval == ''){$interval = 11;}

            if($field_value ==''){$field_value= 0;}
            if($this->getRData("allowcallback")){
              $callbackmethodname = $this->getRData("callbackmethodname");
              if($callbackmethodname !=''){$callbackmethodname=$callbackmethodname."(arguments)";}
            }
            $fval .='<input type="hidden" name="fields['.$this->getFieldName().']" id="'.$this->getFieldName().'" />';
            $fval .='<div dojoType="dijit.form.HorizontalSlider"
            value="'.$field_value.'" minimum="0" maximum="100" discreteValues="11"
            intermediateChanges="true"
            onchange="dojo.byId(\''.$this->field_name.'\').value = arguments[0];'.$callbackmethodname.';fnSetProgress(arguments[0]);"
            showButtons="true">';
            $fval .='</div>';

            $this->processed .= $fval;
        }
    }
}