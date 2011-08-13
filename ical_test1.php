<?php
	
	/**
	 * ical_test.php
	 * Collect the user in an array and procees each user for there events and those will be created a single user file in ics format
	 * It uses the object: Task
	 * Copyright 2001 - 2010 All rights reserved SQLFusion LLC, info@sqlfusion.com 
	 */
	
    $pageTitle = 'Ofuz :: iCal Events';
    $Author = 'SQLFusion LLC';
    $Keywords = 'Keywords for search engine';
    $Description = 'Description for search engine';
    $background_color = 'white';
    include_once('config.php');
    include_once('includes/ofuz_check_access.script.inc.php');
    include_once('includes/header.inc.php');
	require_once 'callib/iCalcreator.class.php';
   
   ?>
   <?php $do_feedback = new Feedback(); $do_feedback->createFeedbackBox(); ?>
<table class="layout_columns"><tr><td class="layout_lmargin"></td><td>
<div class="layout_content">
<?php $thistab = ''; include_once('includes/ofuz_navtabs.php'); ?>
<?php $do_breadcrumb = new Breadcrumb(); $do_breadcrumb->getBreadcrumbs(); ?>
    <div class="grayline1"></div>
    <div class="spacerblock_20"></div>
    <table class="layout_columns"><tr><td class="layout_lcolumn settingsbg">
        <div class="settingsbar"><div class="spacerblock_16"></div>
            <?php
		$GLOBALS['thistabsetting'] = 'Ical Event';
		include_once('includes/setting_tabs.php');
             ?>
        <div class="settingsbottom"></div></div>
    </td><td class="layout_rcolumn">
        <div class="banner60 pad020 text32"><?php echo _('Settings'); ?></div>
        <div class="banner50 pad020 text16 fuscia_text"><?php echo _('ICal Events'); ?></div>
        <div class="contentfull">
   <?php
   
   //start
   
   $c = new vcalendar( array( 'unique_id' => 'ofuz.net' ));
   $c->setProperty( "method", "PUBLISH" );                    // required of some calendar software
   $c->setProperty( "x-wr-calname", "Ofuz Task" );      // required of some calendar software
   $c->setProperty( "X-WR-CALDESC", "Ofuz Task Description" ); // required of some calendar software
  //$c->setProperty( 'X-WR-CALNAME', 'Ofuz tasks' );          
  //$c->setProperty( 'X-WR-CALDESC', 'Description of the calendar' );
    
    //echo "<br />";
    $vivek = new task();
    //echo $_SESSION['do_User']->iduser."<br /><br />";
    //$users=$vivek->getAllTaskuser();
    //print_r($users);
    //echo "<br />";
    
    $iduser = $_SESSION['do_User']->iduser;
    //echo $user;
    
    //$time = time();
    //$key = "$iduser"."$time";    
    
    $e_set_api =  new Event("do_User->eventGenerateAPIKey");
    $e_set_api->addParam("goto",$_SERVER['PHP_SELF']);
	if($_SESSION['do_User']->api_key !=''){
   
	
		$apikey = $_SESSION['do_User']->api_key;
 
    
  $c->setConfig( array( "directory" => "Calevents",
                              "filename"  => "$iduser.ics" ));
	
			
			$vivek->getAllTaskByuser($iduser);
			
			while($vivek->fetch()){
            
 
            $stdt = explode('-',$vivek->getData("due_date_dateformat"));
            $startdate = "$stdt[0]"."$stdt[1]"."$stdt[2]";
            $enddate = $startdate;
            
            $e = & $c->newComponent( 'vevent' );
			//$e->setProperty( 'dtstart', $startdate );
			//$e->setProperty( 'dtend', $enddate );
   $e->setProperty( "dtstart", "$startdate", array("VALUE" => "DATE"));
			$e->setProperty( 'description', $vivek->getData("task_description") );    
			$e->setProperty( 'summary', $vivek->getData("task_description") );
			$e->setProperty( 'class', 'PUBLIC' );
			
            
         
          }
		 $c->createCalendar();
		 $str = $c->saveCalendar();
	
		$tt=$_SERVER["SERVER_NAME"];
		$path =  "http://"."$tt"."/"."Calevents/"."$iduser.ics";
  
  $ff="Calevents/$iduser.ics";
		
  system("chmod 0777 $ff");

		echo _('Your iCal URL is : '.$path.' ');
		
		
	}else{
		echo _('You do not yet have an API key. ');
		echo _('Please generate one ');
		echo $e_set_api->getLink('here');
		echo '<div class="spacerblock_20"></div>';
	}
?>
</div>
        <div class="solidline"></div>
    </td></tr></table>
    <div class="spacerblock_40"></div>
    <div class="layout_footer"></div>
</div>
</td><td class="layout_rmargin"></td></tr></table>
<?php //include_once('includes/ofuz_facebook.php'); ?>
</body>
</html>
