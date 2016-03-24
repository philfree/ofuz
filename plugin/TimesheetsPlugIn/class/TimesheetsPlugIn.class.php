<?php
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/ 

  /**
    * @package TimesheetsPlugIn
    * @license ##License##
    * @version 1.0
    * @date 2016-03-20
    */
class TimesheetsPlugIn extends DataObject{
	
	public $table = 'project_discuss';
    protected $primary_key = 'idproject_discuss';
    public $project_status = "";
    
    function addNewTimesheetsPlugIn($idproject_task,$idtask,$discuss,$iduser,$date_added,$document,$hours_work,$iduser,$discuss_edit_access,$type) {
        $this->idproject_task = $idproject_task;
        $this->discuss = $discuss;
        $this->iduser = $iduser;
        $this->date_added = $date_added;
        $this->document = $document;
        $this->hours_work = $hours_work;
        $this->iduser = $iduser;
        $this->discuss_edit_access = $discuss_edit_access;
        $this->type = $type;
        $this->add(); 
    }

	function getTimeEntryAddForm($nextPage=""){
        $errPage = $nextPage;
        $this->setRegistry("ofuz_time_entry");
        $f_quoteForm = $this->prepareSavedForm("ofuz_time_entry");
        $f_quoteForm->setFormEvent($this->getObjectName()."->eventAdd", 1005);
       // $f_taskForm->addEventAction($this->getObjectName()."->eventAddInvoiceLine", 1010);
        $f_quoteForm->setAddRecord();
        //$f_quoteForm->setUrlNext($nextPage);
        $f_quoteForm->addParam("goto", $nextPage);
        $f_quoteForm->setForm();
        $f_quoteForm->execute();
    }
}
?>