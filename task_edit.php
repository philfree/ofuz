<?php 
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

  /**
   * Default Template web pages 
   */
  $pageTitle = "Page Title";
  $Author = "PAS Pagebuilder";
  $Keywords = "PAS Pagebuilder SQLFusion Web authoring tool";
  $Description = "The best way to built rich web sites";
  $background_color = "white";
  $background_image = "none";
  include_once("config.php");
  include_once("includes/header.inc.php"); include_once("pb_globaldivs.sys.php");?>
<DIV id="DRAG_script_Script" style="top:46px;left:237px;height:267px;width:451px;position:absolute;visibility:visible;z-index:5;">
<!--META  metainfo="execorder:30;filename:includes/blank_script.script.inc.php;" --><?php 
  $do_task = new Task($GLOBALS['conx']);
  $do_task->getTaskAddForm();
 
  echo '<br><br><br> Following is just the edit example for the task id 1 <br><br>';
  $TaskEdit  = new Task($GLOBALS['conx']);
  $TaskEdit->getId(1);
  $TaskEdit->sessionPersistent("TaskEdit", "index.php", 120);
 // $TaskEdit->newUpdateForm("TaskEdit");
  $e_task = new Event("TaskEdit->eventValuesFromForm");
  $e_task->setLevel(1999);
  $e_task->addEventAction("TaskEdit->eventSetDateInFormat", 20);
  $e_task->addEventAction("TaskEdit->update", 2000);
  echo $e_task->getFormHeader();
  echo $e_task->getFormEvent();
  $_SESSION['TaskEdit']->setRegistry("task");
  $_SESSION['TaskEdit']->setApplyRegistry(true, "Form");
  echo $_SESSION['TaskEdit']->task_description;echo '<br><br>';
  $_SESSION['TaskEdit']->due_date = $TaskEdit->convertDateToString($TaskEdit->getDateFormatForTask(1));
  echo $_SESSION['TaskEdit']->due_date;echo '<br><br>';
  echo $_SESSION['TaskEdit']->category; echo '<br><br>';
  echo $_SESSION['TaskEdit']->iduser;
 
  echo $e_task->getFormFooter("Update");
?>
</DIV>



  </body>
</html>