<?php
namespace RadriaCore\Radria\FieldType;

/**
 * Class strFBFieldTypeListBox RegistryField class
 *
 * Display a drop down (SELECT) in the Form context.
 * The content of the drop down are from a separate table.
 * Display the value of the displayfield from the other table in the Disp context.
 * @package PASClass
 */
Class FieldTypeListBox extends FieldType {
    function default_Form($field_value="") {
        //$rdata = $this->getRData('list');
        $dbc = $this->getDbCon();
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {

            if (preg_match("/\:/", $this->getRdata('list'))) {

                list($tablename, $fielduniqid, $fielddisplay, $defaultvalue, $query) = explode (":", $this->getRData('list')) ;
            } else {
                $tablename = $this->table_name;
                $fielduniqid = $this->table_field_value;
                $fielddisplay = $this->table_field_display;
                $defaultvalue = $this->default_value;
                $query = $this->saved_sql_query;
            }
            if (substr($defaultvalue, 0, 1) == "[" && substr($defaultvalue, strlen($defaultvalue) -1, 1) == "]") {
                $defaultvar = substr($defaultvalue, 1, strlen($defaultvalue) -2 ) ;
                if (preg_match("/\;/i", $defaultvar)) {
                    $a_paramdefaultvar = explode(";", $defaultvar);
                    if (function_exists($a_paramdefaultvar[0])) {
                        $defaultvalue = $a_paramdefaultvar[0]($a_paramdefaultvar);
                    }
                } else {
                    global $$defaultvar ;
                    $defaultvalue = $$defaultvar ;
                }
            }
            if (strlen($field_value) > 0) { $defaultvalue = $field_value;  }
            $fval = "<select class=\"adformfield\" name=\"fields[".$this->field_name."]\">\n" ;
            if ($this->getRData('emptydefault') != "no") {
                $fval .= "<option value=\"\"></option>";
            }
            if (strlen($query) > 0) {
                $qlist = new sqlSavedQuery($dbc, $query) ;
                $qlist->query() ;
            } else {
                $qlist = new sqlQuery($dbc) ;
                $qlist->query("select  $fielduniqid, $fielddisplay from $tablename order by $fielddisplay") ;
            }
            while ($alistcontent = $qlist->fetchArray()) {
                $tmp_selected = "" ;
                if (trim($alistcontent[0]) == trim($defaultvalue)) { $tmp_selected = " selected" ; }
                $fval .= "<option value=\"".htmlentities($alistcontent[0])."\"".$tmp_selected.">" ;
                for ($i=1; $i<count($alistcontent) ; $i++) {
                    $fval .= htmlentities($alistcontent[$i])." " ;
                }
                $fval .= "</option>\n" ;
            }
            $fval .= "</select>";
            $this->processed .= $fval;
        }
    }

    function default_Disp($field_value="") {
        if (!$this->getRData('hidden')) {
            $dbc = $this->getDbCon();
            //$dbc = $GLOBALS['conx'];
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