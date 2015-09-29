<?php 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Project class
     * Managed most of the action, data manipulation and display related to Projects.
     *
     * description, due date, category, status (open/closed)
     *
     * @author SQLFusion's Dream Team <info@sqlfusion.com>
     * @package OfuzPage
     * @license GNU Affero General Public License
     * @version 0.6
     * @date 2010-09-06
     * @since 0.3
     */

class Worklog extends DataObject {
    
    public $table = 'project_discuss';
    protected $primary_key = 'idproject_discuss';
    public $project_status = "";
    function addNewWorklog($idproject_task,$discuss,$date_added,$document,$hours_work,$iduser,$discuss_edit_access,$type) {
        $this->idproject_task = $idproject_task;
        $this->note = $discuss;
        $this->date_added = $date_added;
        $this->document = $document;
        $this->hours_work = $hours_work;
        $this->iduser = $iduser;
        $this->discuss_edit_access = $discuss_edit_access;
        $this->type = $type;
        $this->add(); 
    }

    /**
     * display a form to add a project
     * The form HTML is in the forms/ofuz_add_project.form.xml
     * template.
     * @return the HTML code to display the form
     */

    function getWorklogAddForm() {
        $this->setRegistry('ofuz_log_entry');
        $f_projectForm = $this->prepareSavedForm('ofuz_log_entry');
        $f_projectForm->setAddRecord();
        $f_projectForm->setUrlNext('index.php');
        $f_projectForm->setForm();
        //$f_projectForm->execute();
	    return $f_projectForm->executeToString();
    }

    

}
?>
