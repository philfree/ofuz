<?php
namespace RadriaCore\Radria\FieldType;

/**
 * Class strFBFieldTypeDateSQL RegistryField class
 *
 * In the Form context display the date in 3 line field and trigger the EventAction: mydb.formatDateSQLField to reformat the 3 fields in a standard SQL dateformat.
 * In the Disp context display the date in the format template provided in the rdata datef
 * @package PASClass
 */
class FieldTypeDateSQL extends FieldType
{
    function default_Form($field_value="") {
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $fieldvalue = $field_value;
            $fname = $this->getFieldName();
            list($dateFormat, $today, $hidden, $popup) = explode(":", $this->getRData('datesql')) ;
            if ($today == "today" && strlen($fieldvalue) < 10) {
                $fieldvalue = date("Y-m-d", time()) ;
            }
            if ($hidden) {
                $datefieldtype = "hidden" ;
            } else {
                $datefieldtype = "text" ;
            }
            list ($year, $month, $day) = explode("-", $fieldvalue) ;

            $fval .= "<div class=\"adformfield\"> <input type=\"hidden\" name=\"datesqlfieldname[]\" value=\"".$fname."\"/>";
            $fval .= "<input type=\"hidden\" name=\"mydb_events[4]\" value=\"mydb.fieldsToArray\"/>" ;
            $fval .= "<input type=\"hidden\" name=\"fields[$fname]\" value=\"\"/>" ;
            $fday = " <input type=\"$datefieldtype\" name=\"datefieldday_$fname\" value=\"".$day."\"  size=\"4\"  maxlength=\"2\"/>" ;
            $fmonth = " <input type=\"$datefieldtype\" name=\"datefieldmonth_$fname\" value=\"".$month."\"  size=\"4\"  maxlength=\"2\"/>" ;
            $fyear = " <input type=\"$datefieldtype\" name=\"datefieldyear_$fname\" value=\"".$year."\"  size=\"4\"  maxlength=\"4\"/>" ;
            $datefields = str_replace("d", "phlppesjour", $dateFormat) ;
            $datefields = str_replace("m", "phlppesos", $datefields) ;
            $datefields = str_replace("Y", "phlppesanne", $datefields) ;
            $datefields = str_replace("phlppesjour", $fday, $datefields) ;
            $datefields = str_replace("phlppesos", $fmonth, $datefields) ;
            $datefields = str_replace("phlppesanne", $fyear, $datefields) ;
            $fval .= "<!-- ".$popup." - images/popup_icon_calendar.gif --->";
            $popuplink = "";
            if ($hidden) {
                $datefields = str_replace("/", "", $datefields) ;  $datefields = str_replace("-", "", $datefields) ;
            } elseif (($popup == "1") && (file_exists("images/popup_icon_calendar.gif"))) {
                if ($this->datejsinclude) {
                    $js = "
        <script language=\"javascript\">
            function open_popup_calendar(url, form, field, field2, field3) {
                if (form=='') form = 'forms[0]';
                var old_value1 = eval('document.'+form+'.'+field+'.value');    old_value1 = escape(old_value1);
                var old_value2 = eval('document.'+form+'.'+field2+'.value');old_value2 = escape(old_value2);
                var old_value3 = eval('document.'+form+'.'+field3+'.value');old_value3 = escape(old_value3);
                new_window = open(url+'?form='+form+'&field='+field+'&field2='+field2+'&field3='+field3+'&old_value1='+old_value1+'&old_value2='+old_value2+'&old_value3='+old_value3,'Calendar','left=30,top=30,resizable=yes,width=250,height=200');
                return false;
            }
            </script>
            ";
                    echo $js;
                    $this->datejsinclude = false;
                }
                $popuplink = "<a href=\"#\" onClick=\"open_popup_calendar('popup_calendar.php','".$this->getFormName()."','datefieldyear_".$fname."','datefieldmonth_".$fname."','datefieldday_".$fname."');\"><img SRC=\"images/popup_icon_calendar.gif\" border=\"0\"></a>";
            }
            $fval .= $datefields ;
            $fval .= "<input type=\"hidden\" name=\"mydb_events[30]\" value=\"mydb.formatDateSQLField\"/>" ;
            $fval .= $popuplink;
            $fval .= "</div>";
            $this->processed .= $fval;

        }
    }

    function default_Disp($field_value) {

        if (!$this->getRData('hidden') && strlen($this->getRData('datesql')) > 2) {
            $dateformat = explode(":", $this->getRData("datesql"))  ;
            list($year, $month, $day) = explode("-", $field_value) ;
            $fval = str_replace("d", $day, $dateformat[0]) ;
            $fval = str_replace("m", $month, $fval) ;
            $fval = str_replace("Y", $year, $fval) ;
        } else {
            $fval = "" ;
        }
        $this->processed .= $fval;
    }
}