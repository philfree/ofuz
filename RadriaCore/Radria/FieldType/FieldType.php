<?php
namespace RadriaCore\Radria\FieldType;
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
use RadriaCore\Radria\Display;
use RadriaCore\Radria\Event;
use RadriaCore\Radria\EventControler;
use RadriaCore\Radria\mysql\SqlQuery;

class FieldType extends BaseObject {

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
					$q_check = new SqlQuery($this->getDbCon());
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
