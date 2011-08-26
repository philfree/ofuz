<?php
// Copyright 2008-2010 SQLFusion LLC           info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/
  /**
   * Ical event/task calender generator
   * This is file will generates the calendar for particular user in calender event format
   * Its load class and set hooks 
   *
   * @package Icalgenerator
   * @author Philippe Lewicki <phil@sqlfusion.com>
   * @license GNU Affero General Public License
   * @version 0.1
   * @date 2011-08-04  
   */

$GLOBALS['cfg_full_path'] = "../../";
include_once($GLOBALS['cfg_full_path']."config.php");
require_once 'callib/iCalcreator.class.php';
  
  
  $c = new vcalendar( array( 'unique_id' => 'ofuz.net' ));
  $c->setProperty( "method", "PUBLISH" );                    // required of some calendar software
  $c->setProperty( "x-wr-calname", "Ofuz Task" );      // required of some calendar software
  $c->setProperty( "X-WR-CALDESC", "Ofuz Task Description" ); // required of some calendar software
  

  $v = new Task();
  
  
  
  $apikey = $_REQUEST['apikey'];
  $iduser=$v->getUserid($apikey);
  

  
  
if($iduser !=''){
  
  $v->getAllTaskByuser($iduser);
  
  while($v->fetch()){
      $stdt = explode('-',$v->getData("due_date_dateformat"));
      $startdate = "$stdt[0]"."$stdt[1]"."$stdt[2]";
      $enddate = $startdate;
      
      $e = & $c->newComponent( 'vevent' );

      $e->setProperty( "dtstart", "$startdate", array("VALUE" => "DATE"));
      $e->setProperty( 'description', $v->getData("task_description") );    
      $e->setProperty( 'summary', $v->getData("task_description") );
      $e->setProperty( 'class', 'PUBLIC' );
        
   }
   $str=$c->createCalendar();
  
 
  echo $str;
  
  
 }
?>