<?php
// Copyright 2012 SQLFusion LLC           info@sqlfusion.com

   /**
    * Class Invoice 
	*
    * @package RadriaSampleSite
    * @author Philippe Lewicki  <phil@sqlfusion.com>
    * @copyright  SQLFusion LLC 2012
    * @version 1.0
	*/
	

class Invoice extends DataObject {
    public $table = "invoice";
    public $primary_key = "idinvoice";

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
		$invoice_fields = new Fields();
		$invoice_fields->addField(new FieldTypeInt('idinvoice'));
		$invoice_fields->addField(new FieldTypeChar('num'));
		$invoice_fields->addField(new FieldTypeInt('iduser'));
		$invoice_fields->addField(new FieldTypeText('description'));
		$invoice_fields->addField(new FieldTypeFloat('amount'));
		$invoice_fields->addField(new FieldTypeDateSQL('datepaid'));
		$invoice_fields->addField(new FieldTypeDateSQL('datecreated'));
		$invoice_fields->addfield(new FieldTypeListBoxSmall('status'));
		$invoice_fields->idinvoice->hidden = 1;
		$invoice_fields->iduser->hidden = 1;
		$invoice_fields->description->rows = 10;
		$invoice_fields->description->cols = 40;
		$invoice_fields->amount->numberformat = '$:1:.:,:';
		$invoice_fields->status->listvalues = 'New:Sent:Paid';
        $invoice_fields->status->listlabels = 'new:sent:paid';
        $invoice_fields->status->emptydefault = 'no';		
		$this->setFields($invoice_fields);
		
	}
	
	function eventSetInvoiceNumber(EventControler $evctl) {
		// todo
	}
	
}
