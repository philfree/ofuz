<?php
namespace RadriaCore\Radria;
   /**
    * FieldsForm
    * simple class to apply a Registry 
    * to an array of values.
    */

class FieldsForm extends Fields {

    private $datafields=Array();
    
    function __construct($fields_xml="") {
        //if (is_object($fields_xml)) {
        //    $this->fields = $fields_xml->fields;
            //$this->
        //} else {
            parent::__construct($fields_xml, null);
        //}
    }
    
    function setValues($values) {
        $this->datafields = $values;
    }
    
    function __get($field_name) {
        return $this->applyRegToForm($field_name, $this->datafields[$field_name]);
    }
    function __set($field_name, $value) {
        $this->datafields[$field_name] = $value;
    }
}
