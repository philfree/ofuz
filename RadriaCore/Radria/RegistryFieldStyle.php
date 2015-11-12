<?php
namespace RadriaCore\Radria;

use RadriaCore\Radria\FieldType;

/**
 * Class RegistryFieldStyle
 *
 * This extends the RegistryFieldBase to add
 * id, style and class param to the fields in form and display mode.
 * In display mode it will add a span tag.
 * Its a private class not supposed to be used in registry field type.
 * @note id in the display context is currently disabled.
 * @package PASClass
 */

Class RegistryFieldStyle extends FieldType {
    var $style_param = "";
    var $param_set = false;

    function rdataForm_id($field_value="") {
        $this->addStyleParam(" id=\"".$this->getRData('id')."\"");
    }
//     function rdataDisp_disp_id($field_value="") {
//         $this->addStyleParam(" id=\"".$this->getRData('id')."\"");
//     }
    function rdataForm_css_form_style($field_value="") {
        $this->addStyleParam(" style=\"".$this->getRData('css_form_style')."\"");
        //$this->debug_count++;
    }
    function rdataDisp_css_disp_style($field_value="") {
        $this->addStyleParam(" style=\"".$this->getRData('css_disp_style')."\"");
    }
    function rdataForm_css_form_class($field_value="") {
        $this->addStyleParam(" class=\"".$this->getRData('css_form_class')."\"");
    }
    function rdataDisp_css_disp_class($field_value="") {
        $this->addStyleParam(" class=\"".$this->getRData('css_disp_class')."\"");
    }

    function addStyleParam($newparam) {
        if (!$this->param_set) {
            $this->style_param .= $newparam;
        }
    }
    function getStyleParam() {
        $this->param_set = true;
        return $this->style_param;
    }
}