<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    $pageTitle = 'Ofuz';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');

//    $do_notes = new ContactNotes($GLOBALS['conx']);
//    $do_contact = new Contact($GLOBALS['conx']);
//    $do_company = new Company($GLOBALS['conx']);
//    $do_task = new Task($GLOBALS['conx']);
//    $do_task_category = new TaskCategory($GLOBALS['conx']);
//    $do_contact_task = new Contact();
//    $do_notes->sessionPersistent("ContactNotesEditSave", "index.php", 3600);
  
?>
<?php 
$tmp_task = new Task();
$tmp_task_cat = new TaskCategory();
$tmp_task_update = new Task();

$tmp_task->getAll();
while($tmp_task->next()){
  if($tmp_task->category){
    $category = $tmp_task_cat->getTaskCategoryName($tmp_task->category);
    $tmp_task_update->tmpUpdateTaskCat($tmp_task->idtask,$category);
  }
}

?>
Hello
</body>
</html>