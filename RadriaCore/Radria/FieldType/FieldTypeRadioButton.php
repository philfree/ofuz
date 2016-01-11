<?php
namespace RadriaCore\Radria\FieldType;
use RadriaCore\Radria\mysql\SqlQuery;

/**
 * Class strFBFieldTypeRadioButton RegistryField class
 *
 * Display a list of radio buttons in the Form context, retrive the list or radio button from an external table..
 * @package PASClass
 * @see strFBFieldTypeListBox
 */
class FieldTypeRadioButton extends FieldTypeChar
{
    function default_Form($field_value="") {
        //        $rdata = $this->getRData('radiobutton');
        $dbc = $this->getDbCon();
        //$dbc = $GLOBALS['conx'];
        $fieldvalue = $field_value;
        $fname = $this->getFieldName();
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            list($tablename, $fielduniqid, $fielddisplay, $defaultvalue) = explode (":", $this->getRData('radiobutton')) ;
            if (substr($defaultvalue, 0, 1) == "[" && substr($defaultvalue, strlen($defaultvalue) -1, 1) == "]") {
                $defaultvar = substr($defaultvalue, 1, strlen($defaultvalue) -2 ) ;
                if (preg_match("/\;/i", $defaultvar)) {
                    $a_paramdefaultvar = explode(";", $defaultvar);
                    $defaultvalue = $a_paramdefaultvar[0]($a_paramdefaultvar);
                } else {
                    global $$defaultvar ;
                    $defaultvalue = $$defaultvar ;
                }
            }
            if (strlen($fieldvalue) > 0) { $defaultvalue = $fieldvalue;  }
            $qlist = new sqlQuery($dbc) ;
            $qlist->query("select $fielddisplay, $fielduniqid from $tablename order by $fielddisplay") ;
            while (list($vfielddisplay, $vfielduniqid) = $qlist->fetchArray()) {
                $tmp_selected = "" ;
                if ($vfielduniqid == $defaultvalue) { $tmp_selected = " checked" ; }
                $fval .= "<input type=\"radio\" name=\"fields[".$fname."]\" value=\"".htmlentities($vfielduniqid)."\"".$tmp_selected." />".$this->no_PhpCode($vfielddisplay)."\n" ;
                if ($this->getRData("vertical") != "no") { $fval.="<br/>"; } else { $fval.="&nbsp;&nbsp;"; }
                $tmp_selected = "" ;
            }
            $this->processed .= $fval;
        }
    }

    function default_Disp($field_value="") {
        if (!$this->getRData('hidden')) {
            $dbc = $this->getDbCon();
            //$dbc = $GLOBALS['conx'];
            list($tablename, $fielduniqid, $fielddisplay, $defaultvalue, $query) = explode (":", $this->getRData('radiobutton')) ;
            $qFieldDisplay = new SqlQuery($dbc) ;
            $qFieldDisplay->query("select  $fielduniqid, $fielddisplay from $tablename where $fielduniqid='".$field_value."'") ;
            $avfielddisplay = $qFieldDisplay->fetchArray() ;
            $fval = "" ;
            for ($i=1; $i<count($avfielddisplay) ; $i++) {
                $fval .= $avfielddisplay[$i]." " ;
            }
            // $fval=$vfielddisplay;
            $fval = substr($fval, 0, strlen($fval)-2);
            $qFieldDisplay->free() ;
            $this->processed .= $this->no_PhpCode($fval);
        }
    }
}