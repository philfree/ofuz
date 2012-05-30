<?php 
// Copyright 2001 - 2007 SQLFusion LLC, Author: Philippe Lewicki           info@sqlfusion.com
// For licensing, reuse, modification and distribution see license.txt
 /**
  * List of fuctions that can be called in a report/form/savedqueries.
  *
  * These function are usefull if you want to hide a big part of php code
  * from a report.
  * The functions are executed by a Report object that filled the 3 parameters.
  * $reportdata is an array withall the parameter inside the [ ].
  * $row its an array index by field name with the data from the database
  * $dbc is the current database connexion from the report object.
  * All the functions must return a string that will be inserted in the executed report.
  * @package RadriaCore
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2007
  * @version 4.0.0
  * @access public
  *
  * Function Sub uses ReportTable as default report.
  * We should find a solution for the choise of the report Table  
  */

  
  /**
   * Sub will call and execute a report.
   * It will check the query result and will return an empty string if its empty. 
   * This can avoid infinit loop in recursive reports.
   * Usage: [subForm:reportName:field1:field2...:]
   * field1, 2.. will be passed to the sub report as global, usefull for queries
   */
function sub($reportdata, $row, $dbc)  {
  $sub =  $reportdata ;
  $numrow = $sub[count($sub)-1];
  if (is_numeric($numrow)) {
        for ($i=2; $i<count($sub)-1; $i++) {
            $ruptfield = $sub[$i] ;
            if (!empty($ruptfield)) {
            global $$ruptfield;
            $$ruptfield = $row[$numrow][$ruptfield] ;
            //echo $$ruptfield  ;
            }
        }	  
    } else {
        for ($i=2; $i<count($sub); $i++) {
            $ruptfield = $sub[$i] ;
            if (!empty($ruptfield)) {
            global $$ruptfield;
            $$ruptfield = $row[$ruptfield] ;
            //echo $$ruptfield  ;
            }
        }
  }
  $recurs = new Report($dbc, $sub[1]) ;
  if (is_resource($recurs->squery->getResultSet())) {
    $subreport = $recurs->doReport() ;
    return $subreport ;
  } else return "" ;
}

  /**
   * SubTable is like sub but calls a reportTable object
   * Usage: [subForm:reportTableName:field1:field2...:]
   * field1, 2.. will be passed to the sub report as global, usefull for queries
   * @see sub()
   */
function subTable($reportdata, $row, $dbc)  {
     $recurs = new ReportTable($dbc) ;
     $sub =  $reportdata ;
      $recurs->id = $sub[1];
      for ($i=2; $i<count($sub); $i++) {
         $ruptfield = $sub[$i] ;
         if (!empty($ruptfield)) {
            global $$ruptfield;
            $$ruptfield = $row[$ruptfield] ;
         }
      }
      $subreport = $recurs->display($dbc) ;
      return $subreport ;
}
  /**
   * SubTable is like sub but calls a reportForm object
   * Usage: [subForm:reportFormName:field1:field2...:]
   * field1, 2.. will be passed to the sub report as global, usefull for queries
   * @see sub()
   */
function subForm($reportdata, $row, $dbc)  {
     $recurs = new ReportForm($dbc) ;
     $sub =  $reportdata ;
      $recurs->id = $sub[1];
      for ($i=2; $i<count($sub); $i++) {
         $ruptfield = $sub[$i] ;
         if (!empty($ruptfield)) {
            global $$ruptfield;
            $$ruptfield = $row[$ruptfield] ;
         }
      }
      $subform = $recurs->display($dbc) ;
      return $subform;
}

  
  /**
   * savedquery Saved Query function will call a sql Saved Query
   * execute it and return the first row first collumn.
   */
   
function savedquery($reportdata, $row, $dbc)  {
  $sub =  $reportdata ;
  for ($i=2; $i<count($sub); $i++) {
    $ruptfield = $sub[$i] ;
    if (!empty($ruptfield)) {
      global $$ruptfield;
      $$ruptfield = $row[$ruptfield] ;
    }
  }
  $sq = new sqlSavedQuery($dbc, $sub[1]);
  if ($sq->getQueryReady())  {
      $sq->query();
      if (is_resource($sq->getResultSet())) {
          $a = $sq->fetchArray();
          $value = $a[0];
          return $a[0];
      } else {
          return "";
      }
  } else {
      return "";
  }
}

  /**
   * hidden will overide the registry and display an imput hidden field
   * Usage: [hidden:fieldname:]
   */
function hidden($reportdata, $row, $dbc) {
   if ($reportdata[2]) {
    $noregdata = $row[$reportdata[2]][$reportdata[1]] ;
    $fieldname = $reportdata[2];
  } else {
    $noregdata = $row[$reportdata[1]] ;
    $fieldname = $reportdata[1];
    }
   $field = "<INPUT type=\"hidden\" name=\"fields[$fieldname]\" value=\"$noregdata\">" ;
   return $field ;
}

  /**
   * noreg will overide the registry and just return the value from the plain value
   * Usage : [noreg:fieldname:]
   */
function noreg($reportdata, $row, $dbc) {
   if ($reportdata[2]) {
    $noregdata = $row[$reportdata[2]][$reportdata[1]] ;
  } else {
    $noregdata = $row[$reportdata[1]] ;
    }
   return $noregdata ;
}

  /**
   * nocoma will remove all comas in the exported field content
   * Usage : [nocoma:fieldname:]
   */
function nocoma($reportdata, $row, $dbc) {
   if ($reportdata[2]) {
    $nocomadata = str_replace(","," ",$row[$reportdata[2]][$reportdata[1]]) ;
  } else {
    $nocomadata = str_replace(","," ",$row[$reportdata[1]]) ;
    }
   return $nocomadata ;
}

  /**
   * lang extract a lang string using the current language
   * First version will work like the global 
   */
 function lang($reportdata, $row, $dbc) {
    return $GLOBALS[$reportdata[1]]; 
 }

  /**
   * noquote will remove quotes in a string.
   * This is very usefull when assigning [var] in php $var like :
   * <code>
   * $description = "[noquote:description:]";
   * </code>
   */
function noquote($reportdata, $row, $dbc) {
   if ($reportdata[2]) {
    $noquotedata = str_replace("\"", "", $row[$reportdata[2]][$reportdata[1]]) ;
   } else {
    $noquotedata = str_replace("\"", "",$row[$reportdata[1]]) ;
    }
   return $noquotedata ;
}

  /**
   * pas addslashes will call the php addslashes function 
   * This is very usefull when assigning [var] in php $var like :
   * <code>
   * $description = "[pasaddslashes:description:]";
   * </code>
   */
function pasaddslashes($reportdata, $row, $dbc) {
   if ($reportdata[2]) {
    $slashed_data = addslashes($row[$reportdata[2]][$reportdata[1]]) ;
   } else {
    $slashed_data = addslashes($row[$reportdata[1]]) ;
    }
   return $slashed_data ;
}

  /**
   * globalvar grab the value of a variable from the global variable
   * Usage: [globalvar:variablename:]
   */
function globalvar($reportdata, $row, $dbc) {
         $sub = $reportdata;  ;
// echo $sub[1] ;
         if(!isset($$sub[1])) {
              global $$sub[1];
         }
 // echo "--".$$sub[1] ;
  $returnvalue = $$sub[1] ;
   return $returnvalue ;
}

  /**
   * extkey return a value from an external table
   * Usage: [extkey:primarykey:returnfield:]
   */
function extkey($reportdata, $row, $dbc) {
   $key = $reportdata[1] ;
   $table = ereg_replace("^id", "", $key);
   $q = new sqlQuery($dbc) ;
   $q->query("select ".$reportdata[2]." from ".$table." where ".$key."='".$row[$key]."'");
   if ($q->getNumRows() > 0) {
     $q->fetch();
     $value = $q->getData($reportdata[2]) ;
   } else {
     $value = "";
   }
   return $value; 
}

  /**
   * query, run a query and return the first row, first collumn.
   * Usage: [query:sql query statement:]
   */
function query($reportdata, $row, $dbc) {
   $q = new sqlQuery($dbc) ;
   $q->query($reportdata[1]);
   if ($q->getNumRows() > 0) {
     $a = $q->fetchArray();
     $value = $a[0] ;
   } else {
     $value = "";
   }
   return $value; 
}


/**
 *  This is a short cut to call the RecordEvent object,
 *  Usage: [addrecord:tablename:nextpage to display:mydb_num(nextreportname)]
 * @return URL of the event
 **/

function addrecord($reportdata, $row, $dbc) {
    $e_add = new RecordEvent($reportdata[1]) ;
    $e_add->addParam("goto", $reportdata[2]) ;
    if (!empty($reportdata[3])) {
        $e_add->addParam("mydb_num", $reportdata[3]) ;
    }
    $e_add->requestSave("manageData", $reportdata[2]) ;
    $returnvalue = $e_add->getUrlAdd() ;
    return $returnvalue ;
}

 /** This is a short cut to call the RecordEvent object
 *  Usage: [editrecord:tablename:nextpage to display:valueofprimarykey:mydb_num(nextreportname)]
  * @return URL of the event
 **/
function editrecord($reportdata, $row, $dbc) {
    $e_edit = new RecordEvent($reportdata[1]) ;
    $e_edit->addParam("goto", $reportdata[2]) ;
    if (!empty($reportdata[4])) {
        $e_edit->addParam("mydb_num", $reportdata[4]) ;
    }
    $e_edit->requestSave("manageData", $reportdata[2]) ;
    $returnvalue = $e_edit->getUrlEdit($row[$reportdata[3]]) ;
   return $returnvalue ;
}

 /** This is a short cut to call the RecordEvent object
 *  Usage: [deleterecord:tablename:nextpage to display:valueofprimarykey:mydb_num(nextreportname)]
 * @return URL of the event
 **/
function deleterecord($reportdata, $row, $dbc) {
    $e_del = new RecordEvent($reportdata[1]) ;
    $e_del->addParam("goto", $reportdata[2]) ;
    if (!empty($reportdata[4])) {
        $e_del->addParam("mydb_num", $reportdata[4]) ;
    }
    $e_del->requestSave("manageData", $reportdata[2]) ;
    $returnvalue = $e_del->getUrlDelete($row[$reportdata[3]]) ;
   return $returnvalue ;
}


function pluriel($reportdata, $row, $dbc) {
   if ($reportdata[2]) {
      if($row[$reportdata[2]][$reportdata[1]] > 1)
          { $returnvalue = $reportdata[3] ; } else { $returnvalue = "";  }
  } else {
    if($row[$reportdata[1]] > 1)
          { $returnvalue = $reportdata[3] ;  } else { $returnvalue = "";  }
    }
   return $returnvalue ;

}

function currencyvar($reportdata, $row, $dbc) {
      $c = new currency;
      $sub = $reportdata ;
      $currencyname= ereg_replace ('\]', "", $sub[1] );
      $curfield = ereg_replace ('\]', "", $sub[2] );
      $c->name = $currencyname;
      $c->getcurrency($dbc) ;
      $printcur = $c->printcurrency($dbc, $row[$curfield]) ;
     return $printcur ;
      $newrow = ereg_replace('\[currency:.+\:]', $printcur, $newrow) ;
}


   /**
    * setglobal will add a field value as global.
    * Usage: [setglobal:globalvarname:fieldname:]
    */
function setglobal($reportdata, $row, $dbc) {
  global $$reportdata[1] ;
    if (strlen($reportdata[2])>0) {
     $$reportdata[1] = $row[$reportdata[2]] ;
    } else {
     $$reportdata[1] = $row[$reportdata[1]] ;
    }
   $returnempty = "" ;
   return $returnempty ;
}

   /**
    * substring
    * Run the php substring function
    * [substring:fieldname:lenght]
    * and only show the lenght
    */

function substring($reportdata, $row, $dbc) {
   $shorterstring = substr($row[$reportdata[1]], 0, $reportdata[2]);
   return $shorterstring;
}


/**
 * Form / Report function to display a multiselect box
 *
 * Display a multiselect list box using a multiple/multiple table relationship.
 * This form function requires 6 parameters:
 * primarykey_fieldname, size, ext_tablename, data_tablename, foreign_key, fields to display
 *
 * primarykey_fieldname: Name of the primary key of the current table form.
 * size: its the number of row for the multiselect form 
 * ext_tablename: Name of the table that will that will store the multiple/multiple relation
 * data_tablename: Name of the table that contain what will be displayed in the multiselect listbox
 * foreign_key: will link ext_tablename and data_tablename with a straight join (foreign_key variable name must be the same in both)
 * fields to display: fields name separated by a coma that will be displayed in the multiselect list box.
 *
 * Usage Exemple: [multiselect_form:idrequirement:20:userassign:users:idusers:firstname, lastname:]
 */

function multiselect_form($reportdata, $row, $dbc)  {
  global $multiselectvalues;
  $sub =  $reportdata ;
  $rval = "";
  $ext_table = $reportdata[3];  // Name of the table containing 1.1 values for each tables keys
  $foreign_key = $reportdata[5];   // Use to link ext_table and data_table
  $data_tablename = $reportdata[4]; // Table that contain the data to display in multiselect
  $disp_fields = $reportdata[6]; // Field name from data_table to display in multiselect
  $values = Array();

  $e = new Event("mydb.formatMultiSelectField");
  $e->setLevel(1200);
  $e->addParam("mprimarykey[$ext_table]", $reportdata[1]);
  $e->addParam("mforeignkey[$ext_table]", $foreign_key);
  $e->addParam("multiselectfield[]", $ext_table);
  $l = new BaseObject();
  $l->setLogRun(false);
  $l->setLog("\n Start multiselect for ".$ext_table);
  if ($row[$reportdata[1]]>0) {
    $e->addParam($reportdata[1], $row[$reportdata[1]]);
    $q_values = new sqlQuery($dbc);
    $q_values->query("select ".$ext_table.".".$foreign_key." from ".$ext_table." left join ".$data_tablename." on (".$data_tablename.".".$foreign_key."=".$ext_table.".".$foreign_key.") where ".$ext_table.".".$reportdata[1]."='".$row[$reportdata[1]]."'") ;
    $l->setLog("\nselect ".$ext_table.".".$foreign_key." from ".$ext_table." left join ".$data_tablename." on (".$data_tablename.".".$foreign_key."=".$ext_table.".".$foreign_key.") where ".$ext_table.".".$reportdata[1]."='".$row[$reportdata[1]]."'");
    while ($q_values->fetch()) {
        $values[$q_values->getData($foreign_key)] = 1;
        $l->setLog("\n ".$q_values->getData($foreign_key));
    }
  }
  if (is_array($multiselectvalues[$ext_table])) {
    foreach($multiselectvalues[$ext_table] as $sel_value) {
        $values[$sel_value] = 1;
    }
  }
    $rval .= $e->getFormEvent();
    $q_display = new sqlQuery($dbc);
    $q_display->query("select ".$foreign_key.", ".$disp_fields." from ".$data_tablename." order by ".$disp_fields);
    $rval .= "\n<select size=\"".$reportdata[2]."\" name=\"multiselectvalues[".$ext_table."][]\" MULTIPLE>";
    while($q_display->fetch()) {
        $selected = "";
        if ($values[$q_display->getData($foreign_key)]) {
            $selected = " SELECTED";
            $l->setLog("\n selected:".$q_display->getData($foreign_key));
        }
        $rval .= "\n<option value=\"".$q_display->getData($foreign_key)."\"".$selected.">";
        $a_disp_fields = explode(",", $disp_fields);
        foreach($a_disp_fields as $disp_field) {
            $rval .= $q_display->getData(trim($disp_field))." ";
        }
        $rval .= "</option>";

    }
    $rval .= "\n</select>";

  return $rval;

}



/**
 *  Report function to display a content of a multiselect box
 *
 *  Display the content of nultiple / multiple relation ship.
 *  It needs the following information:
 *  primarykey_fieldname, size, ext_tablename, data_tablename, foreign_key, fields to display
 *
 *  primarykey_fieldname: Name of the primary key of the current table form.
 *  size: not used in this context
 *  ext_tablename: Name of the table that will store the multiple/multiple relation
 *  data_tablename: Name of the table that contain what will be displayed.
 *  foreign_key: will link ext_tablename and data_tablename with a straight join (foreign_key variable name must be the same in both)
 *  fields to display: fields name separated by a coma that will be displayed in the multiselect list box.
 * 
 *  Usage Exemple: [multiselect_disp:idrequirement:20:userassign:users:idusers:firstname, lastname:]
 * 
 */

function multiselect_disp($reportdata, $row, $dbc)  {
  $sub =  $reportdata ;
  $rval = "";
  $ext_table = $reportdata[3];  // Name of the table containing 1.1 values for each tables keys
  $foreign_key = $reportdata[5];   // Use to link ext_table and data_table
  $data_tablename = $reportdata[4]; // Table that contain the data to display in multiselect
  $disp_fields = $reportdata[6]; // Field name from data_table to display in multiselect
  if (!empty($reportdata[7])) {
    $sep = $reportdata[7];
  } else {
    $sep = ", ";
  }

  if ($row[$reportdata[1]]>0) {
    $l = new BaseObject();
    $l->setLogRun(false);
    $q_values = new sqlQuery($dbc);
    $q_values->query("select ".$ext_table.".".$foreign_key." from ".$ext_table." left join ".$data_tablename." on (".$data_tablename.".".$foreign_key."=".$ext_table.".".$foreign_key.") where ".$ext_table.".".$reportdata[1]."='".$row[$reportdata[1]]."'") ;
    $l->setLog("\nselect ".$ext_table.".".$foreign_key." from ".$ext_table." left join ".$data_tablename." on (".$data_tablename.".".$foreign_key."=".$ext_table.".".$foreign_key.") where ".$ext_table.".".$reportdata[1]."='".$row[$reportdata[1]]."'");
    while ($q_values->fetch()) {
        $values[$q_values->getData($foreign_key)] = 1;
        $l->setLog("\n ".$q_values->getData($foreign_key));
    }
    $q_display = new sqlQuery($dbc);
    $q_display->query("select ".$foreign_key.", ".$disp_fields." from ".$data_tablename." order by ".$disp_fields);
    while($q_display->fetch()) {
        if ($values[$q_display->getData($foreign_key)]) {
            $l->setLog("\n selected:".$q_display->getData($foreign_key));
            $a_disp_fields = explode(",", $disp_fields);
            foreach($a_disp_fields as $disp_field) {
                $rval .= $q_display->getData(trim($disp_field))." ";
            }
            $rval .= $sep;
        }
    }
    $rval = ereg_replace($sep."$", "", $rval);
  }
  return $rval;

}

   /******************************************************
    * End of the report's functions
    *****************************************************/

   /******************************************************
    * Start of Default value functions from the registry
    *****************************************************/

/**
 * get saved param
 * used in the default field to retrieve default values 
 * in forms
 * first param is the name of the saved event
 * second param is the name of the variable
 */
function getsavedparam($params) {

    $oparam = $params[1];
    if (!empty($GLOBALS[$oparam])) {
        $e_object = $GLOBALS[$oparam];
    } elseif (!empty($_SESSION[$oparam])) {
        $e_object = $_SESSION[$oparam];
    } elseif (!empty($_REQUEST[$oparam])) {
   //     $e_object = $_REQUEST[$oparam];
    }
    if (is_object($e_object)) {
        $value = $e_object->getParam($params[2]);
    } else { $value = ""; }
    return $value;
}

function getparam($params) {
  return getsavedparam($params);
}

/**
 * get value from the field array, 
 * used with the event mydb.addParamToDisplayNext
 * to send back all the values to a form after an error
 */
  function getfields ($params) {
    if ($_REQUEST['reload_fields'] == "Yes") {
        if (!empty($GLOBALS['fields'])) {
            $fields = $GLOBALS['fields'];
        } elseif (!empty($_SESSION['fields'])) {
            $fields = $_SESSION['fields'];
        //} elseif (!empty($_REQUEST['fields'])) {
        //    $fields = $_REQUEST['fields'];
        //}
        } elseif(is_array($_SESSION['previous_event_params'])) {
        $fields = $_SESSION['previous_event_params']['fields'];
        }
        if (is_array($fields)) {
            $fieldname = $params[1];
            return $fields[$fieldname] ;
        } else { return ''; }
    } else { return ''; }
  }

/**
 * get a value from an array
 * To increase security for now removed the $_REQUEST, so only globals and session can be injected in registry or queries
 * Change done on 10/01/2005 by PhL
 */
 function getfromarray($params) {
    $d = new BaseObject();
    $d->setLogRun(false);
    $oparam = $params[1];
    if (!empty($GLOBALS[$oparam])) {
        $arrayname = $GLOBALS[$oparam];
    } elseif (!empty($_SESSION[$oparam])) {
        $arrayname = $_SESSION[$oparam];
    } elseif (!empty($_REQUEST[$oparam])) {
     //   $arrayname = $_REQUEST[$oparam];
    }
    $fieldname = $params[2];
    $d->setLog("\n ".$fieldname." in ".$params[1]." = ".$arrayname[$fieldname]."-".count($arrayname)."--");
    if (is_array($arrayname)) {
      $d->setLog("\n ".$param[1]." is an array --- ");
      return $arrayname[$fieldname];
    } else {
      return '';
    }
 }
/**
 * get a value from the session array or variables.
 * Support session's arrays. 
 * To get a value from an array in the session:
 * [getsession:array_name:key_name]
 * To just get a value from a variable:
 * [getsession:variable_name]
 */
 function getsession($params) {
    if (count($params) > 1) { 
        $arrayname = $_SESSION[$params[1]];
        $fieldname = $params[2];
        if (is_array($arrayname)) {
            return $arrayname[$fieldname];
        } else {
            return $_SESSION[$params[1]];
        }
    } else {
        return $_SESSION[$params[1]];
    }
 }


?>