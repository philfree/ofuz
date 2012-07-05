<?php
// Copyright 2012 SQLFusion LLC           info@sqlfusion.com

   /**
    * Class User 
	*
    * @package RadriaSampleSite
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @copyright  SQLFusion LLC 2012
    * @version 1.0
	*/
	

class User extends DataObject {
    public $table = "user";
    public $primary_key = "iduser";
 /**   
    protected $values = Array(
                  'firstname' => '',
                  'lastname' => '',
                  'email' => '',
                  'username' => '',
                  'password' => '');
**/
    function __construct(sqlConnect $conx=NULL, $table_name="") {
       parent::__construct($conx, $table_name);
       $this->setLogRun(RADRIA_LOG_RUN_SAMPLESITE);
    }
    
    /**
     * Fields initialization
     * This method set the initial fields descriptio for this DataObjec
     * Each property in the values array will be stored
     * in the model (database).
     * The Fields object let you assign to it FieldType and
     * manipulates the FieldType properties.
     * Field type define how a property value will display in a context of a Form
     * or general Display.
     * 
     * All the base FieldType are describe here:
     * http://radria.sqlfusion.com/documentation/core:registry:creating_new_field_types
     * You can create your own:
     * http://radria.sqlfusion.com/documentation/core:registry:creating_new_field_types
     * Some extra package add fieldtype from Dojo or other jquery frameworks.
     */

    function initFields() {
		$user_fields = new Fields();
		$user_fields->addField(new FieldTypeChar('firstname'));
		$user_fields->firstname->required = 1;
		$user_fields->firstname->size = 20;
		$user_fields->addField(new FieldTypeChar('lastname'));
        $user_fields->lastname->size = 20;
		$user_fields->addField(new FieldTypeLogin('email'));
		$user_fields->email->textline = '20:30';
		$user_fields->email->required = 1;
		$user_fields->addField(new FieldTypePassword('password'));
        $user_fields->password->size = 10;
        $user_fields->addField(new FieldTypeListBoxSmall('status'));
        $user_fields->status->listvalues = 'Active:Inactive:Suspended:Paid';
        $user_fields->status->listlabels = 'active:inactive:suspend:paid';
        $user_fields->status->emptydefault = 'no';
        $user_fields->addField(new FieldTypeDateSQL('regdate'));
        $user_fields->regdate->hidden = 1;
        $user_fields->addField(new FieldTypeInt('iduser'));
        $user_fields->iduser->hidden = 1;
		$this->setFields($user_fields);
	}
	
	function eventSetRegistrationDate(EventControler $evctl) {
		$this->values['regdate'] = date('Y-m-d');
		$this->setLog("\n Setting the date to today and updating the record with:".$this->regdate);
	}





}
