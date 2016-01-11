<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

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
<DIV id="DRAG_script_DBInteractiveDataDisplay" style="top:52px;left:46px;height:164px;width:722px;position:absolute;visibility:visible;z-index:5;">
<!--META  metainfo="execorder:30;filename:includes/database.interactive_data_display.script.inc.php;" -->
<?php
  $idbr_report_template = "interactive_report";
  $idbr_table_name = "";  
  $idbr_saved_sql_query = "registration_search_users";
  $idbr_registry = "user_list";
  $idbr_registry_form = "";  
  $idbr_form_page = "";
  $idbr_form_page .= ".php";
  $idbr_detail_page = "";
  $idbr_detail_page .= ".php";
  $idbr_primary_key_var = "";
  $idbr_detail_field1_to_save = "";
  $idbr_detail_field2_to_save = "";
  $idbr_detail_field3_to_save = "";
  $idbr_detail_field4_to_save = "";
 
  $idbr_display_add_link = "";
  $idbr_display_update_link = "Yes";
  $idbr_display_delete_link = "Yes";
  $idbr_display_detail_link = "";
  $idbr_display_search = "Yes";

   if (is_object(${"eDetail_".$idbr_table_name})) {
     global ${"id".$idbr_table_name};
     ${"id".$idbr_table_name} = ${"eDetail_".$idbr_table_name}->getParam("id".$idbr_table_name);
  } 

  $r_db_disp_table = new ReportTable($GLOBALS["conx"]);  

  $r_db_disp_table->setValue("display_add_link", $idbr_display_add_link);
  $r_db_disp_table->setValue("display_update_link", $idbr_display_update_link);
  $r_db_disp_table->setValue("display_delete_link", $idbr_display_delete_link);
  $r_db_disp_table->setValue("display_detail_link", $idbr_display_detail_link);
  $r_db_disp_table->setValue("display_search", $idbr_display_search);  
  $r_db_disp_table->setValue("detail_field1_to_save", $idbr_detail_field1_to_save);
  $r_db_disp_table->setValue("detail_field2_to_save", $idbr_detail_field2_to_save);
  $r_db_disp_table->setValue("detail_field3_to_save", $idbr_detail_field3_to_save); 
  $r_db_disp_table->setValue("detail_field4_to_save", $idbr_detail_field4_to_save); 

  if (!empty($idbr_registry)) {
      $r_db_disp_table->setRegistry($idbr_registry);
  }
  if (!empty($idbr_registry_form)) {
      $r_db_disp_table->setValue("registryname", $idbr_registry_form);
  }
  if (strlen($idbr_form_page)>4) {
      $r_db_disp_table->setFormPage($idbr_form_page);
  }
  if (strlen($idbr_detail_page)>4) { 
      $r_db_disp_table->setValue("detailpage", $idbr_detail_page);
  }
  if (!empty($idbr_primary_key_var)) {
     $r_db_disp_table->setPrimaryKeyVar($idbr_primary_key_var);
  }
  if (!empty($idbr_saved_sql_query)) {
     $r_db_disp_table->setSavedQuery($idbr_saved_sql_query);
  } elseif (is_object($GLOBALS["eDetail_".$idbr_table_name])) {
     global ${"id".$idbr_table_name};
     ${"eDetail_".$idbr_table_name} = $GLOBALS["eDetail_".$idbr_table_name];
     ${"id".$idbr_table_name} = ${"eDetail_".$idbr_table_name}->getParam("id".$idbr_table_name);
    $r_db_disp_table->squery = new sqlQuery($GLOBALS['conx']);
    $r_db_disp_table->squery->query("SELECT * FROM ".$idbr_table_name." WHERE id".$idbr_table_name."=".${"id".$idbr_table_name});
  } 
  $r_db_disp_table->setDefault($idbr_table_name, $idbr_report_template);
  $r_db_disp_table->setQuery();
  $r_db_disp_table->execute();
  
?>
</DIV>



  </body>
</html>