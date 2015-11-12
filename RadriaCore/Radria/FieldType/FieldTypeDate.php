<?php
namespace RadriaCore\Radria\FieldType;

/**
 * Class strFBFieldTypeDate RegistryField class
 *
 * In the Form context display the date in 3 line field and trigger the EventAction: mydb.formatDateField to reformat the 3 fields in a standard unix timestamp.
 * In the Disp context display the date in the format template provided in the rdata datef
 * @package PASClass
 */
class FieldTypeDate extends FieldType
{
    function default_Form($field_value="") {
        $fieldvalue = $field_value;
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $fname = $this->getFieldName();
            list($dateFormat, $today, $hidden, $popup) = explode(":", $this->getRData('datef')) ;
            if ($today == "today" && $fieldvalue < 10) {
                $fieldvalue = time() ;
            }
            if ($hidden) {
                $datefieldtype = "hidden" ;
            } else {
                $datefieldtype = "text" ;
            }
            $day = date("d", $fieldvalue) ; $month = date("m", $fieldvalue) ; $year = date("Y", $fieldvalue) ;
            $hour = date("H", $fieldvalue) ; $minute = date("i", $fieldvalue) ; $second = date("s", $fieldvalue) ;
            $fval .= "<div class=adformfield> <input type=hidden name=datefieldname[] value=\"".$fname."\">";
            $fval .= "<input type=\"hidden\" name=\"mydb_events[4]\" value=\"mydb.fieldsToArray\"/>" ;
            $fval .= "<input type=\"hidden\" name=\"fields[$fname]\" value=\"\"/>" ;
            $fday = " <input type=\"$datefieldtype\" name=\"datefieldday_$fname\" value=\"".$day."\"  size=\"4\"  maxlength=\"2\"/>" ;
            $fmonth = " <input type=\"$datefieldtype\" name=\"datefieldmonth_$fname\" value=\"".$month."\"  size=\"4\"  maxlength=\"2\"/>" ;
            $fyear = " <input type=\"$datefieldtype\" name=\"datefieldyear_$fname\" value=\"".$year."\"  size=\"4\" maxlength=\"4\"/>" ;
            if (preg_match("/\[His\]/", $dateFormat)) {
                $fhour = " <input type=\"$datefieldtype\" name=\"datefieldhour[$fname]\" value=\"".$hour."\"  size=\"4\"  maxlength=\"2\"/>" ;
                $fminute = " <input type=\"$datefieldtype\" name=\"datefieldminute[$fname]\" value=\"".$minute."\"  size=\"4\"  maxlength=\"2\">" ;
                $fsecond = " <input type=\"$datefieldtype\" name=\"datefieldsecond[$fname]\" value=\"".$second."\"  size=\"4\"  maxlength=\"2\">" ;
                $datefields = str_replace("H", "phlppehour", $dateFormat) ;
                $datefields = str_replace("i", "phlppeinute", $datefields) ;
                $datefields = str_replace("s", "phlppesecon", $datefields) ;
                $dateFormat = $datefields ;
            }
            $datefields = str_replace("d", "phlppesjour", $dateFormat) ;
            $datefields = str_replace("m", "phlppesos", $datefields) ;
            $datefields = str_replace("Y", "phlppesanne", $datefields) ;
            $datefields = str_replace("phlppesjour", $fday, $datefields) ;
            $datefields = str_replace("phlppesos", $fmonth, $datefields) ;
            $datefields = str_replace("phlppesanne", $fyear, $datefields) ;
            if (preg_match("/\[His\]/", $dateFormat)) {
                $datefields = str_replace("phlppehour", $fhour, $datefields) ;
                $datefields = str_replace("phlppeinute", $fminute, $datefields) ;
                $datefields = str_replace("phlppesecon", $fsecond, $datefields) ;
            }
            if ($hidden) {
                $datefields = str_replace("/", "", $datefields) ;  $datefields = str_replace("-", "", $datefields) ;
            } elseif (($popup == "1") && (file_exists("images/popup_icon_calendar.gif"))) {
                if ($this->datejsinclude) {
                    $js = "
                    <script language=\'javascript\'>
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
                $fval .= "<a HREF=\"#\" onClick=\"open_popup_calendar('popup_calendar.php','".$this->getFormName()."','datefieldyear_".$fname."','datefieldmonth_".$fname."','datefieldday_".$fname."');\"><img SRC=\"images/popup_icon_calendar.gif\" BORDER=0></a>";
            }
            $fval .= $datefields ;
            $fval .= "<input type=\"hidden\" name=\"mydb_events[31]\" value=\"mydb.formatDateField\"/>" ;
            $fval .= "</div>";
            $this->processed .= $fval;
        }

    }
    function default_Disp($field_value="") {

        if (!$this->getRData('hidden') && strlen($this->getRData('datef')) > 2) {
            $this->setLog("\n datef Display : ".$this->getRData('datef')." - Timestamp:".$field_value);
            $dateformat = explode(":", $this->getRData('datef'))  ;
            $this->processed .= date($dateformat[0], $field_value);
        }
    }
}
