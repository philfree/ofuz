<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

/**
 * Class DijitEditor RegistryField class
 *
 * This is the Dojo / Dijit Editor for Radria
 *  
 * @package PASClass
 */
Class DijitHorizontalSlider extends RegistryFieldBase {
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
            onChange="dojo.byId(\''.$this->field_name.'\').value = arguments[0];'.$callbackmethodname.';"
            showButtons="true">';
            $fval .='</div>';

            

            $this->processed .= $fval;
        }
    }

    function original_default_Form($field_value="") {
        include_once("includes/dojo.dijit.horizontalslider.js.inc.php");
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $field_class = "adformfield";
            if (strlen($this->getRData("css_form_class")) > 0) {
               $field_class = $this->getRData("css_form_class");
            }
            $minvalue = $this->getRData("minvalue");
            $maxvalue = $this->getRData("maxvalue");
            $interval = $this->getRData("interval");
            if($minvalue == 0 || $minvalue == ''){$minvalue = -10;}
            if($maxvalue == 0 || $maxvalue == ''){$maxvalue = 10;}
            if($interval == 0 || $interval == ''){$interval = 11;}

            if($field_value ==''){$field_value= 0;}
            if($this->getRData("allowcallback")){
              $callbackmethodname = $this->getRData("callbackmethodname");
              if($callbackmethodname !=''){$callbackmethodname=$callbackmethodname."(arguments)";}
            }
            $fval .='<div dojoType="dijit.form.HorizontalSlider"
            value="'.$field_value.'" minimum="-10" maximum="10" discreteValues="100"
            intermediateChanges="true"
            onChange="dojo.byId('.$this->field_name.').value = arguments[0];'.$callbackmethodname.';"
            showButtons="true">';

            $fval .='<input type="hidden" name="fields['.$this->field_name.']" id="'.$this->field_name.'">';
            
            $fval .='<div    dojoType="dijit.form.HorizontalRuleLabels" container="topDecoration"
                        style="height:1.2em;font-size:75%;color:gray;"></div>
                            <ol dojoType="dijit.form.HorizontalRuleLabels" container="topDecoration"
                          style="height:1em;font-size:75%;color:gray;">
                          <li> </li>
                          <li>20%</li>
                          <li>40%</li>
                          <li>60%</li>
                          <li>80%</li>
                          <li> </li>
                      </ol>
                              <div dojoType="dijit.form.HorizontalRule" container="bottomDecoration"
                          count=5 style="height:5px;"></div>
                              <ol dojoType="dijit.form.HorizontalRuleLabels" container="bottomDecoration"
                          style="height:1em;font-size:75%;color:gray;">
                          <li>0%</li>
                          <li>50%</li>
                          <li>100%</li>
                      </ol>
                  </div>
                  </div>';
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