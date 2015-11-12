<?php
namespace RadriaCore\Radria\FieldType;

use RadriaCore\Radria\mysql\SqlQuery;
/**
 * strFBFieldTypeEnum
 * Not sure where this comes from no documentation and looks very broken.
 * Should considere removing it or move it to a package as its a mysql specific.
 */
class FieldTypeEnum extends FieldType
{
    function default_Form($field_value="") {
        if (!$this->getRData("hidden")) {
            //global $conx;
            //$query = new sqlQuery($conx);
            $tableName  = "auditlog";
            $columnName = "application";
            $sql = "SHOW COLUMNS FROM $tableName LIKE '$columnName'";

            $query = new SqlQuery($this->getDbConn());
            $query->query($sql);
            $row = $query->fetchArray();
            $enum = explode("','",
                preg_replace("/(enum|set)\('(.+?)'\)/",
                    "\\2", $row["Type"]));
            $fval = "<select name=\"fields[".$this->getFieldName()."]\">";
            for ($i=0; $i<sizeof($applications); $i++) {
                $fval .= "<option value=\"".$applications[$i]."\">".$applications[$i]."</option>";
            }
            $fval .= "</select>";

            $this->processed .= $fval;
        }
    }

    function default_Disp($field_value="") {
        if (!$this->getRData("hidden")) {
            $this->processed .= $field_value;
        }
    }
}