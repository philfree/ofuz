<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

/**
 * Class DijitComboBox RegistryField class
 *
 * This is the Dojo / Dijit ComboBox for Radria
 * 
 *  This fields uses the following registry options:
  <rfield name="mycombobox">
    <rdata type="databasetype">varchar</rdata>
    <rdata type="default"></rdata>
    <rdata type="label">My suggestions.</rdata>
    <rdata type="table_name">Name of the database table to get data from</rdata>
    <rdata type="field_to_display">name of the field with data to display</rdata>
    <rdata type="object_method">Object.method call to extract data from</rdata>
    <rdata type="saved_query">Name of a serialized query object</rdata>
    <rdata type="css_form_class"></rdata>
    <rdata type="addevent">Add a custom event to process this fields input</rdata>
    <rdata type="fieldtype">DijitComboBox</rdata>
  </rfield>
 *
 *
 * Display a drop down (SELECT) in the Form context.
 * The content of the drop down are from a separate table.
 * Display the value of the displayfield from the other table in the Disp context.
 * @package PASClass
 */
Class DijitComboBox extends RegistryFieldBase {
    function default_Form($field_value="") {
        $rdata = $this->getRData('list');
        $dbc = $this->getDbCon();
        include_once("includes/dojo.dijit.combobox.js.inc.php");
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {                
            $tablename = $this->getRData("table_name");
            $fielddisplay = $this->getRData("field_to_display");
            $defaultvalue = $this->getRData("default");
            $query = $this->getRData("saved_query");
            $object_method = $this->getRData("object_method");
            if (substr($defaultvalue, 0, 1) == "[" && substr($defaultvalue, strlen($defaultvalue) -1, 1) == "]") {
                $defaultvar = substr($defaultvalue, 1, strlen($defaultvalue) -2 ) ;
                if (ereg(";", $defaultvar)) {
                    $a_paramdefaultvar = explode(";", $defaultvar);
                    if (function_exists($a_paramdefaultvar[0])) {
                        $defaultvalue = $a_paramdefaultvar[0]($a_paramdefaultvar);
                    }
                } else {
                    global $$defaultvar ;
                    $defaultvalue = $$defaultvar ;
                }
            }
            $fval ='
                 <script type="text/javascript"> 
                     function set'.$this->field_name.'(value) { 
                         console.debug("Selected "+value); 
                     }
                 </script>';
            $field_class = "adformfield";
            if (strlen($this->getRData("css_form_class")) > 0) {
               $field_class = $this->getRData("css_form_class");
            }
            
            if (strlen($field_value) > 0) { $defaultvalue = $field_value;  }
            $fval .= '<select class="'.$field_class.'" name="fields['.$this->field_name.']"
	            dojoType="dijit.form.ComboBox"
	                autocomplete="false"
	                value="'.$defaultvalue.'"
	                onChange="set'.$this->field_name.'">';
            
            if ($this->getRData('emptydefault') != "no") {
                $fval .= "<option value=\"\"></option>";
            }
            
            if (strlen($object_method) > 0) {
            	list($object_name, $method_name) = explode("->", $object_method) ;
                $method_name = str_replace("()", "", $method_name);
                if (is_object($object_name)) {
                    if (is_subclass_of($_SESSION[$object_name], "DataObject")) {
                            $_SESSION[$object_name]->{$method_name}();
                            while ($_SESSION[$object_name]->next()) {
                                $tmp_selected = "" ;
                                if (trim($_SESSION[$object_name]->{$fielddisplay}) == trim($defaultvalue)) { 
                                $tmp_selected = " selected" ; 
                                }
                                $fval .= "<option ".$tmp_selected.">" ;
                                $fval .= htmlentities($_SESSION[$object_name]->{$fielddisplay})." " ;
                                $fval .= "</option>\n" ;
                            }
                    } else {
                            $values = $_SESSION[$object_name]->{$method_name}();
                            foreach($values as $value) {
                                $tmp_selected = "" ;
                                if (trim($value) == trim($defaultvalue)) { 
                                $tmp_selected = " selected" ; 
                                }
                                $fval .= "<option ".$tmp_selected.">" ;
                                $fval .= htmlentities($value)." " ;
                                $fval .= "</option>\n" ;
                            }                   
                    }
                } else {
                    $do = new $object_name();
                    $do->{$method_name}();
                        while ($do->next()) {
                            $tmp_selected = "" ;
                            if (trim($do->{$fielddisplay}) == trim($defaultvalue)) { 
                            $tmp_selected = " selected" ; 
                            }
                            $fval .= "<option ".$tmp_selected.">" ;
                            $fval .= htmlentities($do->{$fielddisplay})." " ;
                            $fval .= "</option>\n" ;
                        }

                }
            }
            
            if (strlen($query) > 0) {
                $qlist = new sqlSavedQuery($dbc, $query) ;
                $qlist->query() ;
            } elseif (strlen($tablename) > 0 && strlen($fielddisplay) > 0) {
                $qlist = new sqlQuery($dbc) ;
                $qlist->query("select $fielddisplay from $tablename order by $fielddisplay") ;
            } 
            
            if (is_object($qlist)) {
                while ($alistcontent = $qlist->fetchArray()) {
                    $tmp_selected = "" ;
                    if (trim($alistcontent[0]) == trim($defaultvalue)) { $tmp_selected = " selected" ; }
                    $fval .= "<option ".$tmp_selected.">" ;
                    for ($i=0; $i<count($alistcontent) ; $i++) {
                        $fval .= htmlentities($alistcontent[$i])." " ;
                    }
                    $fval .= "</option>\n" ;
                }
            }
            $fval .= "</select>";
            $this->processed .= $fval;
        }
    }

    function default_Disp($field_value="") {
        if (!$this->getRData('hidden')) {
            $dbc = $this->getDbCon();
            list($tablename, $fielduniqid, $fielddisplay, $defaultvalue, $query) = explode (":", $this->getRData('list')) ;
            if ($fielduniqid != $fielddisplay) {
                if (!empty($field_value)) {
                    $qFieldDisplay = new sqlQuery($dbc) ;
                    $qFieldDisplay->query("select  $fielduniqid, $fielddisplay from $tablename where $fielduniqid='".$field_value."'") ;
                    $avfielddisplay = $qFieldDisplay->fetchArray() ;
                    $fval = "" ;
                    for ($i=1; $i<count($avfielddisplay) ; $i++) {
                        $fval .= $avfielddisplay[$i]." " ;
                    }
                    $fval = substr($fval, 0, strlen($fval)-2);
                    $qFieldDisplay->free() ;
                } else { $fval = ""; }
            } else {
              $fval =  $field_value;
            }
            if (!$this->getRdata('execute')) {
                    $fval = $this->no_PhpCode($fval);
            }
            $this->processed .= $fval;
        }
    }
}
  
  
  

?>