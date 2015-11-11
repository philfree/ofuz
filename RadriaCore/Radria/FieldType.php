<?php
namespace RadriaCore\Radria;
 /**
  *  Apply load and apply the registry rules.
  *
  *  The Registry is load from a .reg.xml file in registry/ directory.
  *  for each field element in the .reg.xml file a RegistryField object
  *  is created based on its fieldtype (rdata).<br>
  *  It can then be apply for Form context or Display context.
  *  Each RegistryField object contains a default_form and default_disp
  *  method that generate HTML code for both context.
  *  From the .reg.xml files all the rdata values are accessible
  *  with the getRdata().
  *  And they can also be methods of a RegistryField object.
  *  This is to simplify the extention of the RegistryField object.
  *  Currently ordering or executions of methods as been diseabled.
  *
  * @package RadriaCore
  * @author Philippe Lewicki  <phil@sqlfusion.com>
  * @copyright  SQLFusion LLC 2001-2007
  * @version 4.0.0
  * @access public
  */

use RadriaCore\Radria\BaseObject;

Class FieldType extends BaseObject {

    var $processed = "";
    var $field_name;
    var $default_value;
    var $nothing = true;
    var $dbc;
    var $form_name = "";
    var $rdata = Array();
    var $exec_order = Array();
    var $originalval ;
    var $field_value;
    var $registry_name;
    static $event_level = 10;

    /**
     * RegistryFieldBase constructor
     *
     * The constructor used by the Registry object to
     * create new fields for that registry.
     * @param string $field_name name of the files of that registry / table
     * @param array $rtype_rdata array with all rdata tags in format $rtype_rdata[name]=value loaded from the xml file.
     * @param sqlConnect $dbc database connexion from the Registry object. Needed if a registry field needs to run a query
     * @access public
     */

    function __construct($field_name="", $rtype_rdata=0, $dbc=0) {
        parent::__construct();
        $this->setFieldName($field_name);
        if (!is_resource($dbc)) {
            $dbc = $GLOBALS['conx'];
        }
        //$this->dbc = $dbc;
        if (is_array($rtype_rdata)) {
            $this->setRDatas($rtype_rdata);
        } else {
            $this->setRDatas(Array()) ;
        }
		if (strlen($this->fieldtype) == 0) {
			$this->fieldtype = get_class($this);
		} 
     /** Curently ordering is not implemented
      *  Not sure its needed.
        $this->exec_order['default'] = 20;
        $this->exec_order['required'] = 30;
        $this->exec_order['hidden'] = 40;
        $this->exec_order['readonly'] = 50;
        $this->exec_order['textline'] = 500;
        $this->exec_order['nothing'] = 50000;
      **/
      if (defined("RADRIA_LOG_RUN_FIELDTYPE")) {
            $this->setLogRun(RADRIA_LOG_RUN_FIELDTYPE);
      }
    }

    /**
     * setFormName set a name for the form context
     *
     * In the form context some fields type needs the name of the form to generate some javascripts code.
     * this value comes from the Registry object. Use the method from Registry not this one.
     *
     * @param string $formname Name of the form for the form context
     * @see getFormName()
     */
    function setFormName($formname) {
        //$this->setLogRun(false);
        $this->form_name = $formname ;
    }

    /**
     * getFormName return the name of the form
     * @return string Name of the form
     */
    function getFormName() {
        return $this->form_name;
    }

    /**
     * @return sqlConnect current database connection
     */
    function getDbCon() {
        //return $this->dbc;
	return $GLOBALS['conx'];
    }

    /**
     * process will execute all rdata methods and default_ for a context
     *
     * Process recieve the context and the value of the field.
     * The rdata can be used with getRData() and setRdata() but they can also be methods of a RegistryField object.
     * If the reg.xml files for that field contains a rdata thats define as a method, that method will then be executed.
     * Naming Format for a rdata method follow this convention :
     * rdata<context>_rdatatype
     * For exemple for the reg.xml sample :
     * <code>
     * <rdata type="datef">d/m/Y - H:i:s</rdata>
     * </code>
     * The associate rdata method for a Display context would be :
     * <code>
     * rdata_Disp_datef($field_value);
     * </code>
     * After processing all the rdata methods the default_<Context> methods are executed for that field.
     *
     * @param string $context name of the context usualy "disp" or "form"
     * @param string $field_value value of field.
     * @see getRData(), setRData()
     */

    function process($context, $field_value) {
      $this->originalval = $field_value;
      $this->setFieldValue($field_value);
      $this->processed = "";
      if (empty($field_value) && !empty($this->default_value)) {
          if (strlen($this->getFieldValue()) == 0)  { $this->setFieldValue($this->default_value) ; }
          $field_value = $this->default_value;
      }
      foreach($this->rdata as $rtype=>$rdata) {
        if (!empty($rdata)) {
            if (method_exists($this, "rdata".$context."_".$rtype)) {
               $this->{"rdata".$context."_".$rtype}($field_value) ;
            }
        }
        if (empty($field_value) && !empty($this->default_value)) {
            if (strlen($this->getFieldValue()) == 0)  { $this->setFieldValue($this->default_value) ; }
            $field_value = $this->default_value;
        }
      }
      if (method_exists($this, "default_".$context)) {
          $this->{"default_".$context}($field_value) ;
      }
      return $this->processed;
    }

    function addProcessed($string_value) {
        $this->processed .= $string_value;
    }

    /**
     * rdataForm_default rdata method
     *
     * All fields can have default value.
     * If a default rdata is found with a value and the field_value is empty then the default value found is assign  to the property default_value
     * @param string $field_value value of the field
     */

    function rdataForm_default($field_value="") {
        $rdata = $this->getRData('default');
        if(!empty($rdata)) {
            if (substr($rdata, 0, 1) == "[" && substr($rdata, strlen($rdata) -1, 1) == "]") {
                $defaultvar = substr($rdata, 1, strlen($rdata) -2 ) ;
                if (preg_match("/\;/", $defaultvar)) {
                    $a_paramdefaultvar = explode(';', $defaultvar);
                    $defaultvalue = $a_paramdefaultvar[0]($a_paramdefaultvar);
                } elseif(preg_match("/\:/", $defaultvar)) {
                    $a_paramdefaultvar = explode(':', $defaultvar);
                    $defaultvalue = $a_paramdefaultvar[0]($a_paramdefaultvar);
                } elseif(strrchr($defaultvar, '.') !== false) {
                    list ($table_name, $field_name) = explode(".", $defaultvar);
                    $a_paramdefaultvar = Array( "getparam", "eDetail_".$table_name, $field_name); 
                    $defaultvalue = getsavedparam($a_paramdefaultvar);
                } elseif(strpos($defaultvar, '->') > 0) {
                    $this->setLog("\n Registry: found default value in object");
                    list ($object_name, $variable_name) = explode("->", $defaultvar);
                    if (is_object($_SESSION[$object_name])) {
                       $defaultvalue = $_SESSION[$object_name]->{$variable_name};
                    }
                } elseif (!empty($_SESSION[$defaultvar])) {
                    $defaultvalue = $_SESSION[$defaultvar] ;
                } else {
                    $defaultvalue = $GLOBALS[$defaultvar] ;
                }
            } else {
                $defaultvalue = $rdata;
            }
        $this->default_value = $defaultvalue;
        } else {
            $this->default_value = "";
        }
        $this->setLog("\nRegistry: default (".$this->field_name.":".$rdata.") varname: ".$defaultvar." value:".$this->default_value);
    }

    /**
     * rdataForm_required rdata method require for form context.
     *
     * This rdata will trigger the mydb.checkRequired event to check if the field contains a value.
     * @param string $field_value value of the field
     */
    function rdataForm_required($field_value="") {
        if ($this->getRData('required')) {
			$e_require = new Event($this->getEventActionName("eventCheckRequired"));
			$e_require->setLevel($this->getEventLevel())
			          ->addParam("required[".$this->getFieldName()."]", "yes");			          
            $this->addProcessed($e_require->getEvent());
        }
    }
  /**   Event Mydb.checkRequired
  *
  * Check that all the field set as required are field in.
  * If not it sets the doSave param at "no" to block the save and
  * call the message page.
  * <br>- param array fields that contains the content of the fields to check
  * <br>- param array required indexed on fields name and contains value "yes"
  * <br>Option:
  * <br>- param string errorpage page to display the error message
  */   
    function eventCheckRequired(EventControler $evctl) {
		$this->setLog("\n eventCheckRequired:".$this->id);
		global $strRequiredField;
		if (!isset($strRequiredField)) {
			$strRequiredField = "You must fill in all the fields that are required." ;
		}
		 if ($evctl->submitbutton != _("Cancel")) {
				//if (is_array($evctl->fields)) {
					$this->setLogArray($evctl->fields);			
					$this->setLog("\n $key -> $val");
					
					if (($evctl->required[$this->getFieldName()]=="yes") && $evctl->fields[$this->getFieldName()] == "") {
						if (strlen($evctl->errorpage)>0) {
							$urlerror = $evctl->errorpage;
						} else {
							$urlerror = $evctl->getMessagePage() ;
						}
						$disp = new Display($urlerror);
						$disp->addParam("message", $strRequiredField) ;
						$evctl->setDisplayNext($disp) ;
						$evctl->updateParam("doSave", "no") ;
					}
				  
				//}
		 }
	}
    /**
     * rdataForm_unique rdata method check in the field content is unique in the db.
     *
     * This rdata will trigger the mydb.checkRequired event to check if the field contains a value.
     * @param string $field_value value of the field
     */
    function rdataForm_unique($field_value="") {
		$this->setLog("\n calling method: rdataForm_unique");
        if ($this->getRData('unique') && $this->getRData('unique_table_name')) {
			$this->setLog("\n Generating eventAction for unique");
			$e_unique = new Event($this->getEventActionName("eventCheckUnique"));
			$e_unique->addParam("unique[".$this->getFieldName()."]", "yes")   
			         ->setLevel($this->getEventLevel());
            $this->addProcessed($e_unique->getEvent());
        }
    }

  /**   Event FieldType::eventCheckUnique
  *
  * Check that all the field set as required are field in.
  * If not it sets the doSave param at "no" to block the save and
  * call the message page.
  * <br>- param array fields that contains the content of the fields to check
  * <br>- param array required indexed on fields name and contains value "yes"
  * <br>Option:
  * <br>- param string errorpage page to display the error message
  */   
    function eventCheckUnique(EventControler $evctl) {
		$this->setLog("\n Check Unique , table:".$this->unique_table_name." message:".$this->unique_message);
		if (strlen($this->unique_message) > 0) {
			$validate_message = $this->unique_message ;
		} elseif (strlen($this->label)>0) {
			$validate_message = $this->label._(" must be unique");
		}
		 if ($evctl->submitbutton != _("Cancel") && strlen($this->unique_table_name) > 0) {
				$field_name = $this->getFieldName();
				if ($evctl->unique[$field_name]=="yes") {
					$q_check = new sqlQuery($this->getDbCon());
					$q_check->query("select {$field_name} from ".$this->unique_table_name." where {$field_name} = '".$q_check->quote($evctl->fields[$field_name])."'");					
					if ($q_check->getNumRows() > 0 ) {
						if (strlen($evctl->errorpage)>0) {
							$urlerror = $evctl->errorpage;
						} else {
							$urlerror = $evctl->getMessagePage() ;
						}
						$disp = new Display($urlerror);
						$disp->addParam("message", $validate_message) ;
						$_SESSION['in_page_message'] = $validate_message;
						$this->setLog("\n Validate message:".$_SESSION['in_page_message']);
						$evctl->setDisplayNext($disp) ;
						$evctl->updateParam("doSave", "no") ;
					}
				}				  
		 }
	}
    
    /**
     * rdataForm_hidden rdata method hidden for form context
     *
     * This if a rdata hidden is found then the hidden field is set in form context
     * all other rdata methods and default_method will be check the getRData('hidden') before processing.
     * and return nothing in all contexts.
     * @param string $field_value value of the field
     */
    function rdataForm_hidden($field_value="") {
        if ($this->getRData('hidden')) {
            $this->addProcessed("<input type=\"hidden\" name=\"fields[".$this->field_name."]\" value=\"".htmlentities($this->getFieldValue())."\"/>")  ;
        }
    }

    /**
     * rdataForm_readonly rdata method readonly for form context
     *
     * This if a rdata readonly is found then the hidden field is set in form context
     * all other rdata methods and default_method will be check the getRData('readonly') before processing.
     * and if in the disp context it will display its value.
     * @param string $field_value value of the field
     */
    function rdataForm_readonly($field_value="") {
        if ($this->getRData('readonly')) {
            $this->addProcessed("<input type=\"hidden\" name=\"fields[{$this->field_name}]\" value=\"".htmlentities($this->getFieldValue())."\"/>")  ;
        }
    }

    /**
     * rdataForm_addevent rdata method trigger a specific eventaction
     *
     * This will add an event action call based on the eventaction name
     * set in the rdata.
     * @param string $field_value value of the field
     */
    function rdataForm_addevent($field_value="") {
        if ($this->getRData('addevent')) {
            $event_name = $this->getRData('addevent');
            if (is_object($_SESSION[$event_name])) {
                $_SESSION[$event_name]->setSecure(false);
                $this->addProcessed($_SESSION[$event_name]->getFormEvent())  ;
            } else {
                list ($event_name, $level) = explode(":", $this->getRData('addevent'));
                $this->addProcessed("<input type=\"hidden\" name=\"mydb_events[".$level."]\" value=\"".$event_name."\"/>")  ;
            }
        }
    }
    /**
     * @deprecate not used and dont use it
     */
    function rdataForm_nothing($field_value="") {
             $this->processed .= "<input class=\"adformfield\" type=\"text\" name=\"fields[".$this->field_name."]\" value=\"".$this->getFieldValue()."\"/>";
    }

   /**
    * getEventLevel
    * Return the next level for an eventaction
    */
    function getEventLevel() {
		$classname = 'FieldType';
		return $classname::$event_level++;
	}

	/**
	 * getEventActionName
	 * will generate the eventaction object to call for FieldType classes
	 * @param String method_name name of the event method
	 * @return String with eventaction object call
	 */
    function getEventActionName($method_name) {
		if (method_exists($this, $method_name)) {
			return $this->getObjectName().":".$this->getFieldName()."->".$method_name;
		} else { return "";}
	}

   /**
    * setRDatas
    * Set the rdata for this field with a new Array of RDatas.
    * The keys contains rdata types.
    * This will overwrite all preset rdatas.
    *
    * @param array $a_rtypes Array will all rdata for this field
    */
    function setRDatas($a_rtypes) {
        $this->rdata = $a_rtypes;
    }
   /**
    * getRData
    * Return all the rdatas type as an array
    * The array is indexed on the type.
    *
    * @return array with all rdata type indexed on the type
    */
    function getRDatas() {
        return $this->rdata;
    }

   /**
    * getRData
    * Return the value of one RData type
    *
    * @param string $rtype type of the RData
    * @return string value of the rdata
    */
    function getRData($rtype) {
        if(array_key_exists($rtype, $this->rdata)) {
          return $this->rdata[$rtype];
        } else {
          return false; 
        }
    }

   /**
    * setRData
    * Set only one rdata for this field.
    *
    * @param string $type type of rdata (hidden, default, textline...)
    * @param string value values of this rdata.
    * @see setRdatas
    */
    function addRData($type, $value) {
        $this->rdata[$type] = $value;
    }

    /**
    * setRData
    * Set only one rdata for this field.
    *
    * @param string $type type of rdata (hidden, default, textline...)
    * @param string value values of this rdata.
    * @see addRdata, setRdata
    */
    function setRdata($type, $value) {
        $this->addRData($type, $value);
        return $this;
    }

    /** 
     * Set RData with the magic __set
     * 
     * @param string $data_value
     */
    function __set($type, $value) {
         $this->setRdata($type, $value);
    }

    /** 
     * Get RData with the magic __get 
     * 
     * @param string $data_value
     */
    function __get($type) {
        return $this->getRdata($type);
    }

    function setFieldName($field_name) {
        $this->field_name = $field_name;
    }

    function getFieldName() {
        return $this->field_name;
    }

    function setRegistryName($reg_name) { 
        $this->registry_name = $reg_name;
    }
    function getRegistryName() {
        return $this->registry_name;
    }

    /**
     * Set the value of a field.
     * Used with getFieldValue it allow rdata methods to
     * share and change the value of the field.
     * @access private
     * @param string $field_value value of the field to be set.
     */
    function setFieldValue($field_value) {
        $this->field_value = $field_value;
    }
    function getFieldValue() {
        return $this->field_value;
    }

    /**
     * Change PHP tags to HTML entities.
     * This is an important to security feature
     * So the users can execute php code from datacontent they
     * have access to.
     *
     * PHL Note: This function seems to have an influance on perfomances.
     * Will need more QA and profiling.
     *
     * @access private
     * @param string $code field value to parse and transform php tags.
     * @return string $code string with the php tags in html entities.
     */
    function no_PhpCode($code) {
        $code = str_replace("<?", "&lt;?", $code);
        $code = str_replace("?>", "?&gt;", $code);
        $code = str_replace("<%", "&lt;?", $code);
        $code = str_replace("%>", "?&gt;", $code);
        return $code;
    }
}

/**
 * Class FieldType 
 * Experimental, working on a new monenclature for Radria to be
 * more intuitive.
 * As registry is only used for Data Fields we will call it
 * Fields and FieldBase
 */

//class FieldType extends RegistryFieldBase {};

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


/**
 * Class FieldTypeChar
 *
 * This is the default field type.
 * Its used when no other field type are set and it extends fields type that
 * doesn't need more than a textline field.
 * Also if no textline rdata are set the default will fallback to textline,
 * @package PASClass
 */

Class FieldTypeChar extends RegistryFieldStyle {
    var $style_param = "";
    function rdataForm_textline($field_value="") {
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $regparams = explode(":", $this->getRData('textline')) ;
            $this->addStyleParam(" size=\"$regparams[0]\"  maxlength=\"$regparams[1]\"");
            if (!$this->getRData('execute'))  {
                $field_value = $this->no_PhpCode($field_value);
            }
            $this->processed  .= "<input  type=\"text\" name=\"fields[".$this->getFieldName()."]\" value=\"".htmlentities($field_value)."\"";
            if (strlen($this->getStyleParam()) > 0) {
                $this->processed .= $this->getStyleParam();
            } else {
                $this->processed .= " id=\"".$this->getFieldName()."\"  class=\"adformfield\"";
            }
            if ($this->getRdata('disabled')) {
                $this->processed .= " disabled";
            }
            $this->processed .= "/>";
        }
    }

    function default_Form($field_value="") {
        $field_value = $this->getFieldValue();
        if (!$this->getRData('hidden') && !$this->getRData('readonly') && !$this->getRData('textline')) {
            if (!$this->getRData('execute'))  {
                $field_value = $this->no_PhpCode($field_value);
            }
            if (strlen($this->getRData("size")) > 0) {
                $this->addStyleParam(" size=\"".$this->getRData("size")."\"");
            } 
            if (strlen($this->getRData("maxlength")) > 0) {
                $this->addStyleParam(" maxlength=\"".$this->getRData("maxlength")."\"");
            }
            $this->processed  .= "<input  type=\"text\" name=\"fields[".$this->getFieldName()."]\" value=\"".htmlentities($field_value)."\"";
            if (strlen($this->getStyleParam()) > 0) {
                $this->processed .= $this->getStyleParam();
            } else {
                $this->processed .= " id=\"".$this->getFieldName()."\"  class=\"adformfield\"";
            }
            if ($this->getRdata('disabled')) {
                $this->processed .= " disabled";
            }
            $this->processed .= "/>";

        }
    }

    function rdataDisp_substring($field_value="") {
        $field_value = substr( $this->getFieldValue(), 0, $this->getRData("substring")) ;
        $this->setFieldValue($field_value);
    }

    function default_Disp($field_value="") {
        $field_value = $this->getFieldValue();
        if (!$this->getRData('hidden')) {
            if (!$this->getRData('execute'))  {
                $field_value = $this->no_PhpCode($field_value);
            }
            if (strlen($this->getStyleParam()) > 0) {
                $this->processed .= "<span ".$this->getStyleParam().">".$field_value."</span>";
            } else {
                $this->processed .= $field_value;
            }
        }
    } 
}

//Class  strFBFieldTypeChar extends FieldTypeChar {}

/**
 * Class strFBFieldTypeInt
 *
 * Inherit everything from strFBFieldTypeChar
 * @package PASClass
 */
Class FieldTypeInt extends  FieldTypeChar {
     function default_disp($field_value="") {
         if (!$this->getRData('hidden')) {
            $val ="";
            if (strlen($this->getRData('numberformat'))>0) {
                list($prestr, $dec_num, $dec_sep, $thousands,  $poststr) = explode(":", $this->getRData('numberformat'));
                $val = $prestr.number_format($this->getFieldValue(), $dec_num, $dec_sep, $thousands).$poststr;
            } else {
                $val = $this->getFieldValue();
            }
            if (!$this->getRdata('execute')) {
                    $val = $this->no_PhpCode($val);
            }
            if (strlen($this->getStyleParam()) > 0) {
                $this->processed .= "<span ".$this->getStyleParam().">".$val."</span>";
            } else {
                $this->processed .= $val;
            }
         }
     }
}
//Class strFBFieldTypeInt extends FieldTypeInt {}

/**
 * Class FieldTypeText
 *
 * Display a textarea box in Form context
 * @package PASClass
 */ 

Class FieldTypeText extends RegistryFieldStyle {
    function default_Form($field_value="") {

        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $fieldname = "fields[".$this->getFieldName()."]" ;
            $fval = "<textarea ";
            if (strlen($this->getStyleParam()) > 0) {
                $fval .= $this->getStyleParam();
            } else {
                $fval .= " id=\"".$this->getFieldName()."\"  class=\"adformfield\"";
            }
            $fval .= " name=\"".$fieldname."\"";
            if (strlen($this->getRData('textarea')) > 0) {
                $regparams = explode(":", $this->getRData('textarea')) ;
                $fval .= " rows=\"".$regparams[1]."\" cols=\"".$regparams[0]."\" " ;
            } elseif ($this->rows || $this->cols) {
				$fval .= ' rows="'.$this->rows.'" cols="'.$this->cols.'" ';
				
			}
            if (strlen($this->getRData('wrap')) > 0 ) { $fval .= " wrap=\"".$this->getRData('wrap')."\"" ;}
            if ($this->getRdata('disabled')) {
                $fval .= " disabled";
            }
            $fval .= ">".htmlentities($this->getFieldValue())."</textarea>\n";
            $this->processed .= $fval;

        }
    }

  function rdataDisp_substring($field_value="") {
        $field_value = substr( $this->getFieldValue(), 0, $this->getRData("substring")) ;
        $this->setFieldValue($field_value);
  }

  function default_Disp($field_value="") {
        $field_value = $this->getFieldValue();
        if (!$this->getRData('hidden')) {
            $val = "";
            if ($this->getRData('html')) {
                $val .= htmlspecialchars($field_value);
            } else {
                if (!$this->getRdata('execute')) {
                    $field_value = $this->no_PhpCode($field_value);
                }
                $val .= $field_value;
            }
            if (strlen($this->getStyleParam()) > 0) {
                $this->processed .= "<span ".$this->getStyleParam().">".$val."</span>";
            } else {
                $this->processed .= $val;
            }
        }
    }
}
//Class strFBFieldTypeText extends FieldTypeText  { }

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
//Class strFBFieldTypeListBox extends FieldTypeListBox {} 

/**
 * Class strFBFieldTypeListBoxSmall RegistryField class
 *
 * Display a drop down (SELECT) in the Form context.
 * The content of the drop down are from the rdata listvalues and listkeys
 * @package PASClass
 */
Class FieldTypeListBoxSmall extends FieldType {
    function default_Form($field_value="") {
        if (strlen($this->getRData('listvalues'))>0) {
            $values = explode(":", $this->getRData('listvalues'));
        } else {
            $values = explode(":", $this->getRData('listlabels'));
        }
        $labels = explode(":", $this->getRData('listlabels'));
        if (strlen($field_value) > 0) {
            $defaultvalue = $field_value;
          } else {
            $defaultvalue = $this->default_value;
         }
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $fval = "<select class=\"adformfield\" name=\"fields[".$this->field_name."]\">\n" ;
            if ($this->getRData('emptydefault') != "no") {
                $fval .= "<option value=\"\"></option>";
            }
            for($i=0; $i<count($labels); $i++) {
                $tmp_selected = "";
                if (trim($values[$i]) == trim($defaultvalue)) { $tmp_selected = " selected" ; }
                  $fval .= "\n<option value=\"".htmlentities($values[$i])."\"".$tmp_selected.">".$labels[$i]."</option>" ;
            }
            $fval .= "</select>";
            $this->processed .= $this->no_PhpCode($fval);
        }
    }

    function default_Disp($field_value="") {
        if (!$this->getRData('hidden')) {
        if (strlen( $this->getRData('listvalues'))>0) {
            $values = explode(":", $this->getRData('listvalues'));
        } else {
            $values = explode(":", $this->getRData('listlabels'));
        }
        $labels = explode(":", $this->getRData('listlabels'));
        for($i=0; $i<count($labels); $i++) {
            if (trim($values[$i]) == trim($field_value)) {  $fval = $labels[$i] ; }
        }
        if (!$this->getRdata('execute')) {
            $fval = $this->no_PhpCode($fval);
        }
        $this->processed .= $fval;
        }
    }
}

/**
 * Class strFBFieldTypeFloat
 *
 * Inherit everything from strFBFieldTypeChar
 * @package PASClass
 */
Class FieldTypeFloat extends FieldTypeChar {
     function default_disp($field_value="") {
         if (!$this->getRData('hidden')) {
             if (strlen($this->getRData('numberformat'))>0) {
                 list($prestr, $dec_num,  $dec_sep, $thousands, $poststr) = explode(":", $this->getRData('numberformat'));
                 $this->processed .= $prestr.number_format($field_value, $dec_num,  $dec_sep, $thousands).$poststr;
             } else {
                  $this->processed .= $field_value;
             }
         }
     }
}
//Class strFBFieldTypeFloat extends FieldTypeFloat {}

/**
 * Class FieldTypeCheckBox RegistryField class
 * Possibility:
 * Default: 
 *   - checked
 *   - unchecked
 * Want it in the ass: [ ]
 *  1- default: unchecked
         user check    = Yes in the DB.
         user uncheck  = '' in the DB
 * Serialized sample:
    <rfield name="foreColor">
    <rdata type="checked_value">Yes</rdata>
    <rdata type="unchecked_value">No</rdata>
    <rdata type="default">Yes</rdata>
    <rdata type="label">Text color</rdata>
    <rdata type="fieldtype">FieldTypeCheckBox</rdata>
    <rdata type="checkbox">1</rdata>
    <rdata type="databasetype">varchar</rdata>
  </rfield>  
 * Display a checkbox in the Form Context.
 * In Disp context if the box is checked the content of the default value is displayed.
 * @package PASClass
 */
Class FieldTypeCheckBox extends FieldType {
    function default_Form($field_value="") {
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
             $using_checked_value = false;
            if (strlen($this->getRData("checked_value")) > 0) {
                $checkbox_value = $this->getRData("checked_value");
                $using_checked_value = true;
            } else {
                $checkbox_value = $this->default_value;
            }

            $fval .= "<input type=\"hidden\" name=\"mydb_events[24]\" value=\"FieldTypeCheckBox::eventFormatCheckBoxValue\">";
            $fval .= "<input type=\"hidden\" name=\"checkbox_fields[]\" value=\"".$this->getFieldName()."\">";
            $fval .= "<input type=\"hidden\" name=\"checkbox_uncheck[".$this->getFieldName()."]\" value=\"".$this->getRData("unchecked_value")."\"/>" ;
            $fval .= "\n<input type=\"checkbox\" class=\"adformfield\" id=\"fields_".$this->getFieldName()."\"  name=\"fields[".$this->getFieldName()."]\" value=\"".htmlentities($checkbox_value)."\" ";
            // Old logic, just stay for compatibility reasons
            if ($this->originalval == $this->default_value && !$using_checked_value) { $fval .= " checked=\"yes\" "; }
            // New logic that requires the checked_value rdata to be set.
            if ($field_value == $this->getRData("checked_value")) { $fval .= " checked=\"yes\" "; }
            $fval .= ">";
        $this->setLog("\n".$this->getFieldName()." ".$checkbox_value." == ".$this->default_value." - current value:".$field_value);
        $this->processed .= $fval;
       }
    }
    function default_Disp($field_value="") {
        if (!$this->getRData('hidden')) {
            $this->processed .= $field_value;
        }
    }
    static function eventFormatCheckBoxValue(EventControler $event_controler) {
          $checkbox_fields = $event_controler->checkbox_fields;
          $checkbox_uncheck = $event_controler->checkbox_uncheck;
          $fields = $event_controler->fields;
          foreach ($checkbox_fields as $field_name) {
            if ((strlen($checkbox_uncheck[$field_name]) > 0) && strlen($fields[$field_name]) == 0) {
                $fields[$field_name] = $checkbox_uncheck[$field_name];
            }
          }
          $event_controler->fields = $fields;
    }
}
/**
 * Class strFBFieldTypeRadioButton RegistryField class
 *
 * Display a list of radio buttons in the Form context, retrive the list or radio button from an external table..
 * @package PASClass
 * @see strFBFieldTypeListBox
 */
Class FieldTypeRadioButton extends FieldTypeChar {
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
            $qFieldDisplay = new sqlQuery($dbc) ;
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
//Class strFBFieldTypeRadioButton extends FieldTypeRadioButton {}

/**
 * Class strFBFieldTypeRadioButtonSmall RegistryField class
 *
 * Display a list of radio buttons in the Form context,
 * retrive the list or radio button from rdata radiovalues and radiolabels
 * @package PASClass
 * @see strFBFieldTypeListBoxSmall
 */
Class FieldTypeRadioButtonSmall extends RegistryFieldStyle {
    function default_Form($field_value="") {
        if (strlen( $this->getRData('radiovalues'))>0) {
            $values = explode(":", $this->getRData('radiovalues'));
        } else {
            $values = explode(":", $this->getRData('radiolabels'));
        }
        $labels = explode(":", $this->getRData('radiolabels'));
       
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            for($i=0; $i<count($labels); $i++) {
                $tmp_selected = "";
                if (trim($values[$i]) == trim($field_value)) { $tmp_selected = " checked" ; }
                $this->processed .= "\n<input type=\"radio\" ";
                if (strlen($this->getStyleParam()) > 0) {
                        $this->processed .= $this->getStyleParam();
                } else {
                        $this->processed .= " id=\"".$this->getFieldName()."\"  class=\"adformfield\"";
                }
                $this->processed .= "name=\"fields[".$this->field_name."]\" value=\"".htmlentities($values[$i])."\"".$tmp_selected." />".$labels[$i];
                if ($this->getRData("vertical") != "no") { $fval.="<br/>"; } else { $fval.="&nbsp;&nbsp;"; }
            }
            $this->processed .= $this->no_PhpCode($this->processed);
        }
    }

    function default_Disp($field_value="") {
        if (!$this->getRData('hidden')) {
        if (strlen( $this->getRData('radiovalues'))>0) {
            $values = explode(":", $this->getRData('radiovalues'));
        } else {
            $values = explode(":", $this->getRData('radiolabels'));
        }
        $labels = explode(":", $this->getRData('radiolabels'));
        for($i=0; $i<count($labels); $i++) {
            if (trim($values[$i]) == trim($field_value)) {  $fval = $labels[$i] ; }
        }
        $this->processed .= $this->no_PhpCode($fval);
        }
    }
}
//Class strFBFieldTypeRadioButtonSmall extends FieldTypeRadioButtonSmall  {}
 

/**
 * Class strFBFieldTypeEmail RegistryField class
 *
 * In the Form context trigger the EventAction: mydb.checkEmail to check the value field in is a real  email address.
 * In the Disp context display the email content around a mailto: link.
 * @package PASClass
 */
Class FieldTypeEmail extends FieldTypeChar {
    function default_Form($field_value="") {
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            $this->processed .= "<input type=\"hidden\" name=\"mydb_events[7]\" value=\"mydb.checkEmail\"/>" ;
            $this->processed .="<input name=\"emailfield[]\" type=\"hidden\" value=\"".$this->field_name."\"/>" ;
        }
    }

    function default_Disp($field_value="") {
        if (!$this->getRData('hidden')) {
          if (!$this->getRdata('execute')) {
                    $field_value = $this->no_PhpCode($field_value);
          }
          $this->processed .= "<a class=\"mailtolink\" href=\"mailto:".$field_value."\">".$field_value."</a>" ;
        }
    }
}
//Class strFBFieldTypeEmail extends FieldTypeEmail { }

/**
 * Class strFBFieldFile RegistryField class
 *
 * In the Form context Display a input type File and trigger the EventAction: mydb.formatPictureField that will process the uploaded file.
 * In the Disp context if the file is an image display it in an image tag, otherwize in a link to download it.
 * @package PASClass
 */
 
Class FieldTypeFile extends FieldType {
    function default_Disp($field_value="") {
        $file_path = trim($this->rdata['picture']);
        if (!preg_match("/\/\$/", $file_path)) {
            $file_path .= "/";
        }
        if (!$this->getRdata('execute')) {
            $field_value = $this->no_PhpCode($field_value);
        }
        if ($this->getRData('showpicture')=="1" && !empty($field_value)) {
            $fval="<img border=\"0\" src=\"".$file_path.$field_value."\">";
         } else {
            $fval = $file_path.$field_value;
            $fval = "<a href=\"".$fval."\">".$fval."</a>" ;
         }
         $this->processed .= $fval;
    }

    function default_Form($field_value="") {
        if (!$this->rdata['hidden'] && !$this->rdata['readonly']) {
            if (!$this->getRdata('execute')) {
                    $field_value = $this->no_PhpCode($field_value);
            }
      //      list ($filedir, $filename) = explode(":", $this->rdata['picture']) ; PHL 012006 uses no overwrite
            $overwrite = strtolower($this->rdata['overwrite']);
            $filedir =  $this->rdata['picture'];
            $fval .= "<input type=\"hidden\" name=\"mydb_events[5]\" value=\"mydb.formatPictureField\"/>" ;
        //    if (strlen($filename) > 0) {
        //        $fval .= "<input type=\"hidden\" name=\"filenameuploaded[]\" value=\"$filename\"/>" ;
        //    }
            if ($overwrite == "no") {
                $fval .= "<input type=\"hidden\" name=\"fileoverwrite[]\" value=\"no\"/>" ;
            }
            $fval .= "<input type=\"hidden\" name=\"filedirectoryuploaded[]\" value=\"$filedir\"/>" ;
            $fval .= "<input type=\"hidden\" name=\"filefield[]\" value=\"".$this->field_name."\"/>";
            $fval .= "<input type=\"hidden\" name=\"fields[".$this->field_name."]\" value=\"".$field_value."\"/>";
            $fval .= "<input class=\"adformfield\" name=\"userfile[]\" type=\"file\"/>";
            if($field_value!="") $fval .= "(".$field_value.")";
            $this->processed .= $fval;
        }
    }
}

/**
 * Class strFBFieldTypeDate RegistryField class
 *
 * In the Form context display the date in 3 line field and trigger the EventAction: mydb.formatDateField to reformat the 3 fields in a standard unix timestamp.
 * In the Disp context display the date in the format template provided in the rdata datef
 * @package PASClass
 */
Class FieldTypeDate extends FieldType {
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


/**
 * Class strFBFieldTypeLogin RegistryField class
 *
 * In the Form context display a text line feild. It works with the password field.
 * @package PASClass
 */
Class FieldTypeLogin extends FieldTypeChar {
    function default_Form($field_value="") {
		parent::default_Form($field_value="");
        $this->processed .= "<input name=\"accessfield[login]\" type=\"hidden\" value=\"".$this->getFieldName()."\"/>";
    }
}

/**
 * Class strFBFieldType RegistryField class
 *
 * In the Form context display 2 text line field in password mode and trigger the EventAction: mydb.checkUsernamePassword to check it the username and password dont already exists and if the 2 passwords are the same.
 * This RegistryField requires a strFBFieldTypeLogin field in the same form.
 * @package PASClass
 */
 
Class FieldTypePassword  extends RegistryFieldStyle {
    function default_Form($field_value="") {
        if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
            if (!$this->getRdata('execute')) {
               $field_value = $this->no_PhpCode($field_value);
            }
            if ($this->getRdata("loginform")) {
            	$fval .= "<input type=\"password\" name=\"fields[".$this->getFieldName()."]\" value=\"".$field_value."\"/>" ;
            } else {
				$e_password = new Event($this->getEventActionName("eventCheckUsernamePassword"));
				$e_password->addParam("accessfield[password]", $this->getFieldName())->setLevel($this->getEventLevel());				
	            $fval = $e_password->getEvent();
        	    $fval .= "<input ".$this->getStyleParam()." type=\"password\" name=\"fields[".$this->getFieldName()."]\" value=\"".$field_value."\"/>" ;
            	$fval .=  "\n<br/><input id=\"confirm_password\" type=\"password\" name=\"fieldrepeatpass[".$this->getFieldName()."]\" value=\"".$field_value."\"/>"  ;
            }
            $this->processed .= $fval;
        }
    }
    
    function default_Disp($field_value="") {
    if (!$this->getRData('hidden')) {
            if (!$this->getRdata('execute')) {
                $field_value = $this->no_PhpCode($field_value);
            }
            $this->processed .= $field_value;
        }
    }
    
    function eventCheckUsernamePassword(EventControler $evctl) {
	 /**   Event CheckUsernamePassword
	  *
	  * To test if passwords matches and there is not already a login and password
	  * To work the uniq id of the table must be named as id<table name>.
	  * If its a new record the uniqid must be an empty string else a integer..
	  * If not it sets the doSave param at "no" to block the save and
	  * Call the message page.
	  * @package RadriaEvents   
	  * @author Philippe Lewicki <phil@sqlfusion.com>
	  * @param array accessfield array with the name of the password and login fields
	  * Option :
	  * @param string errorpage page to display the errors  
	  * @copyright SQLFusion
	  */
	  /*
	  $strMissingField  = "Vous devez avoir 1 login et 1 mot de passe" ;
	  $strErrorPasswordNotMatch = "Les mots de passe saisie ne correspondent pas ";
	  $strErrorLoginAlreadyUsed = "Loggin deja utilise, Vous devez choisir un autre login";
	  */
	  global $strMissingField, $strErrorPasswordNotMatch, $strErrorLoginAlreadyUsed;
	  if (!isset($strMissingField)) {
		$strMissingField = "You need a login and password in the form" ;
	  }
	  if (!isset($strErrorPasswordNotMatch)) {
		$strErrorPasswordNotMatch = "The password entries do not match" ;
	  }
	  if (!isset($strErrorLoginAlreadyUsed)) {
		$strErrorLoginAlreadyUsed = "The username is already in use" ;
	  }
      $accessfield = $evctl->accessfield;
      $fields = $evctl->fields;
      $fieldrepeatpass = $evctl->fieldrepeatpass;
      $errorpage = $evctl->errorpage;
      $this->setLog("\n Check login & password:".$evctl->errorpage);
      $this->setLogArray($fields);
      $this->setLog("\n Repeat pass:");
      $this->setLogArray($fieldrepeatpass);
	  if ($evctl->submitbutton != "Cancel") {
			if (strlen($errorpage)>0) {
					$dispError = new Display($errorpage) ;
			} else {
					$dispError = new Display($evctl->getMessagePage()) ;
			}
			$dispError->addParam("message","") ;
			
			if (is_array($accessfield)) {
					if (!isset($table)) { $table = "users"; } 
					$nbraccess = count($accessfield) ;
					if ($nbraccess != 2) {
						$dispError->editParam("message",$strMissingField) ;
					}
					$passwordfield = $accessfield["password"] ;
					$loginfield = $accessfield["login"] ;
					$this->setLog("\n Verify pass:".$fieldrepeatpass[$passwordfield]);
					if ($fields[$passwordfield] != $fieldrepeatpass[$passwordfield]) {
						$dispError->editParam("message", $strErrorPasswordNotMatch) ;
					}
					if (get_magic_quotes_gpc()) {
							$primarykey = stripslashes($primarykey) ;
					}
					if (strlen($primarykey) > 0) {
						$queryverif = "select * from ".$table." where ".$loginfield."='".$fields[$loginfield]."' AND NOT(".$primarykey.")" ;
					} else {
						$queryverif = "select * from ".$table." where ".$loginfield."='".$fields[$loginfield]."'" ;
					}
					$qVerif = new sqlQuery($evctl->getDbCon()) ;
					$rverif = $qVerif->query($queryverif) ;
					if ($qVerif->getNumRows()) {
						$dispError->editParam("message",$strErrorLoginAlreadyUsed ) ;
					}
			}
			$error  = $dispError->getParam("message") ;
			if (strlen($error) > 0) {
					$_SESSION["in_page_message"] = $error;
					$evctl->setDisplayNext($dispError) ;
					$evctl->updateParam("doSave", "no") ;
			// echo "supposed to be no from here " ;
			}
	  }
	}
}

/**
 * Class strFBFieldTypeListBoxFile RegistryField class
 *
 * In the Form context display a drop down (SELECT) with a list of files as content.
 * @package PASClass
 */
Class FieldTypeListBoxFile extends FieldType {
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

/**
 * Class strFBFieldTypeDateSQL RegistryField class
 *
 * In the Form context display the date in 3 line field and trigger the EventAction: mydb.formatDateSQLField to reformat the 3 fields in a standard SQL dateformat.
 * In the Disp context display the date in the format template provided in the rdata datef
 * @package PASClass
 */
Class FieldTypeDateSQL extends FieldType {
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

 /**
  * Class strFBFieldTypeTimeSQL RegistryField class
  *
  * In the Form context display a text line field and trigger the EventAction: mydb.formatTimeField reformat and validate the content.
  * @package PASClass
  */
Class FieldTypeTimeSQL extends FieldType {
    function default_Form($field_value="") {
      if (!$this->getRData('hidden') && !$this->getRData('readonly')) {
        list($now, $hidden) = explode(":", $this->getRData('timef')) ;
        list($hour, $min, $sec) = explode(":", $field_value) ;

        if ($now == "now" && (($hour == "00" && $min == "00" && $sec == "00") || (strlen($field_value) < 3))) {
            $field_value = date("H:i:s") ;
        }

        if ($hidden) {
            $fval .="<input type=\"hidden\" name=\"fields[".$this->getFieldName()."]\" value=\"$field_value\" size=\"8\"/>";
        } else {
            $fval .="<input type=\"text\" name=\"fields[".$this->getFieldName()."]\" value=\"$field_value\" size=\"8\"/>";
        }

        $fval .= "<input type=\"hidden\" name=\"timefieldname[]\" value=\"".$this->getFieldName()."\"/>";
        $fval .= "<input type=\"hidden\" name=\"mydb_events[35]\" value=\"mydb.formatTimeField\"/>" ;
        $this->processed .= $fval;
     }
   }

   function default_Disp($field_value="") {
    if (!$this->getRData('hidden')) {
        $this->processed .= $field_value;
        }
   }
}

    /**
     * strFBFieldTypeEnum
     * Not sure where this comes from no documentation and looks very broken.
     * Should considere removing it or move it to a package as its a mysql specific.
     */
Class FieldTypeEnum extends FieldType {
    function default_Form($field_value="") {
        if (!$this->getRData("hidden")) {
            //global $conx;
            //$query = new sqlQuery($conx);
            $tableName  = "auditlog";
            $columnName = "application";
            $sql = "SHOW COLUMNS FROM $tableName LIKE '$columnName'";

            $query = new sqlQuery($this->getDbConn());
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


