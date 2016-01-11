   <?php
   require_once 'callib/iCalcreator.class.php';
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
