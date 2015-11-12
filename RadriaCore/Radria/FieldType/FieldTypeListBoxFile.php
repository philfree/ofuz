<?php
namespace RadriaCore\Radria\FieldType;

/**
 * Class strFBFieldTypeListBoxFile RegistryField class
 *
 * In the Form context display a drop down (SELECT) with a list of files as content.
 * @package PASClass
 */
class FieldTypeListBoxFile extends FieldType
{
    function default_Form($field_value="") {
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            //$dbc = $this->getDbCon();
            list($directory,  $extention, $defaultvalue) = explode (":", $this->getRData('listfile')) ;
            if (strlen($field_value) > 0) {  $defaultvalue = $field_value;  }
            $this->setLog("\n Default value: ".$defaultvalue." field value :".$field_value);
            $fval = "<select class=\"adformfield\" name=\"fields[".$this->getFieldName()."]\">\n" ;
            $fval .= "<option value=\"\"></option>";
            $dirqueries = dir($directory);
            $this->setLogRun(false);
            $this->setLog("\n list box dir ".$directory);
            if (strlen($extention) > 0) {
                while ($entry = $dirqueries->read()) {
                    // echo $entry;
                    if (strlen($entry) > 2 && preg_match("/".$extention."$/", $entry) && !preg_match("/\.sys.\php$/i", $entry)) {
                        $dirname = str_replace($extention, "", $entry) ;
                        $a_listfile[$entry] = $dirname ;
                    }
                }
            } else {
                while ($entry = $dirqueries->read()) {
                    if (strlen($entry) > 2) {
                        $a_listfile[$entry] = $entry ;
                    }
                }
            }
            if (is_array($a_listfile)) {
                ksort($a_listfile) ;

                while (list($entry, $listcontent) = each($a_listfile)) {
                    $tmp_selected = "" ;
                    if (trim($listcontent) == trim($defaultvalue)) { $tmp_selected = " selected" ; }
                    $fval .= "<option value=\"".htmlentities($listcontent)."\"".$tmp_selected.">" ;
                    $fval .= $listcontent ;
                    $fval .= "</option>\n" ;
                }
            }
            $fval .= "</select>";
            $this->processed .= $this->no_PhpCode($fval);
        }
    }
    function default_Disp($field_value="") {
        if (!$this->getRData('hidden')) {
            list ($directory, $extension, $defaultvalue) = explode(":", $this->getRData('listfile')) ;
            if (strlen($extension) > 0) {
                $this->processed .= $this->no_PhpCode($field_value).$extension;
            } else {
                $this->processed .= $this->no_PhpCode($field_value);
            }
        }
    }
}