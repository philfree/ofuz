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
	public $table = "project_discuss";
    protected $primary_key = "idproject_discuss";

    function __construct(sqlConnect $conx=NULL, $table_name="") {
       parent::__construct($conx, $table_name);
       $this->setLogRun(RADRIA_LOG_RUN_OFUZ);
    }

    function add() {
        $do_NoteDraft = new NoteDraft();
        $idnote_draft = $do_NoteDraft->isDraftExist($this->idproject_task,'project_discuss');
        if($idnote_draft){
            $do_NoteDraft->getId($idnote_draft);
            $do_NoteDraft->delete();  
        }
	
        if (get_magic_quotes_gpc()) {
            $project_discuss = $this->discuss;
        }
        else {
            $project_discuss = htmlentities(addslashes($this->discuss));
        }
        	echo "tt===".$this->idproject_task;
        $this->query("INSERT INTO project_discuss (idproject_task,discuss,date_added,document,hours_work,iduser,discuss_edit_access,type)
                      VALUES 
                      (".$this->idproject_task.",'".$project_discuss."','".$this->date_added."','".$this->document."','".$this->hours_work."','".$this->iduser."','".$this->discuss_edit_access."','Note')");
        echo "INSERT INTO project_discuss (idproject_task,discuss,date_added,document,hours_work,iduser,discuss_edit_access,type)
                      VALUES 
                      (".$this->idproject_task.",'".$project_discuss."','".$this->date_added."','".$this->document."','".$this->hours_work."','".$this->iduser."','".$this->discuss_edit_access."','Note')";die();              
        $this->setPrimaryKeyValue($this->getInsertId($this->getTable(), $this->getPrimaryKey()));
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