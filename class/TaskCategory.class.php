<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Tag class
     * Using the DataObject
     * description, due date, category, status (open/closed)
     * @author Abhik Chakraborty info@sqlfusion.com
     */

class TaskCategory extends DataObject {
    
    public $table = "task_category";
    protected $primary_key = "idtask_category";

    function getTaskCategoryName($id){
      $q = new sqlQuery($this->getDbCon());
      $q->query("select name from task_category where idtask_category =".$id);
      $q->fetch();
      return $q->getData("name");
    }

    function getUsersTaskCategory(){
        $this->query("select * from task_category where iduser = ".$_SESSION['do_User']->iduser);
    }

     function getTaskCategoryAddForm(){
      $this->setRegistry("task_category");
      $f_taskForm = $this->prepareSavedForm("ofuz_add_task_category");
      $f_taskForm->setFormEvent($this->getObjectName()."->eventAdd", 1005);
      $f_taskForm->setAddRecord();
      $f_taskForm->setUrlNext("task_category.php");
      $f_taskForm->setForm();
      $f_taskForm->execute();
    }

     function getiTaskCategoryAddForm(){
      $this->setRegistry("task_category");
      $f_taskForm = $this->prepareSavedForm("i_ofuz_add_task_category");
      $f_taskForm->setFormEvent($this->getObjectName()."->eventAdd", 1005);
      $f_taskForm->setAddRecord();
      $f_taskForm->setUrlNext("i_task_category.php");
      $f_taskForm->setForm();
      $f_taskForm->execute();
    }

     function eventAjaxTaskCategoryDelete(EventControler $evctl) {
        $this->getId($evctl->id);
        $this->delete();
        $update_task_category = new Task();
        $update_task_category->updateTaskCategory($evctl->id);
        $evctl->addOutputValue('ok');
    }

    function eventAjaxEditTaskCategoryForm(EventControler $evctl) {
        $form = '<div class="taskbox1a"><div class="taskbox1b"><div class="taskbox1c">';
        $this->getId($evctl->id);
        $this->sessionPersistent("TaskCategoryEdit", "index.php", 120);
        $e_task = new Event("TaskCategoryEdit->eventValuesFromForm");
        $e_task->setLevel(1999);
        $e_task->addEventAction("TaskCategoryEdit->eventUpdate", 2000);
        $form .= $e_task->getFormHeader();
        $form .= $e_task->getFormEvent();
        $_SESSION['TaskCategoryEdit']->setRegistry("task_category");
        $_SESSION['TaskCategoryEdit']->setApplyRegistry(true, "Form");
        $form .= $_SESSION['TaskCategoryEdit']->name . '<br /><br />';
        $form .= $_SESSION['TaskCategoryEdit']->iduser;
        $form .= $e_task->getFormFooter("Update this Category");
        $form .= '<div class="cancellink">or <a href="#" onclick="fnCancelEdit(' . $evctl->id . ');">'._('cancel').'</a></div>';
        $form .= '</div></div></div>';
        $evctl->addOutputValue($form);
    }

}
?>