   <?php
   
   //start
   
   
  //$c->setProperty( 'X-WR-CALNAME', 'Ofuz tasks' );          
  //$c->setProperty( 'X-WR-CALDESC', 'Description of the calendar' );
    
    //echo "<br />";
   
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
   
  
		 //$str = $c->saveCalendar();
	
		$tt=$_SERVER["SERVER_NAME"];
		$path =  "http://"."$tt"."/"."iCalfeed/"."$apikey.ics";
  
  //$ff="Calevents/$iduser.ics";
		
  //system("chmod 0777 $ff");

		echo _('Your iCal URL is : '.$path.' ');
		
		
	}else{
		echo _('You do not yet have an API key. ');
		echo _('Please generate one ');
		echo $e_set_api->getLink('here');
		echo '<div class="spacerblock_20"></div>';
	}
?>
