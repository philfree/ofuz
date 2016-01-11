<?php
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

Class OfuzFieldTypePassword  extends RegistryFieldBase {
    function default_Form($field_value="") {
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            if (!$this->getRdata('execute')) {
               $field_value = $this->no_PhpCode($field_value);
            }
            $fval = "<input name=\"accessfield[".$this->getRData('access')."]\" type=\"hidden\" value=\"".$this->getFieldName()."\"/>";
           // JL Sept-9-2008 // $fval .= "<span><input type=\"password\" name=\"fields[".$this->getFieldName()."]\" value= \"".$this->getRData('label')."\" onfocus=\"fnClearDefault(this);\" onblur=\"fnRestoreDefault(this);\"/></span>" ;
           // $fval .= "<span><input type=\"text\" name=\"passtext\" value= \"\"  />";
            $fval .= "<input type=\"password\" name=\"fields[".$this->getFieldName()."]\" value=\"\" class=\"formfield\" /></span>";
            $this->processed .= $fval;
        }
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