<?php
include_once('config.php');

/*$savefile = "savehere.txt";

$sf = fopen($savefile, 'a+') or die("can't open file");*/
ob_start();

// read from stdin
$fd = fopen("files/ofuz_catch.log", "r");
$email = "";
while (!feof($fd)) {
	$email .= fread($fd, 1024);
}
fclose($fd);
// handle email

//$lines = explode("\n", $email);

$OfuzEmailFetcher = new OfuzEmailFetcher();
$OfuzEmailFetcher->fetchEmailRow($email);

$to = $OfuzEmailFetcher->getToEmail();
$from = $OfuzEmailFetcher->getFromEmail();
$bcc = $OfuzEmailFetcher->getBCCEmail();
$cc = $OfuzEmailFetcher->getCCEmail(); 

//$email_marged_array = array_merge($to,$bcc);
$all_emails_merged = array_merge($to,$cc);
//print_r($all_emails_merged);die();

if(is_array($all_emails_merged)){
  foreach($all_emails_merged as $email_add){
	  
    $display_array[] = $OfuzEmailFetcher->getEmailAddress($email_add);
    $email_array[] = $OfuzEmailFetcher->getEmailDisplay($email_add);
  }
  $len_to_emailarr = count($email_array);
}


$drop_box_list = array("addnote-","addtask-","task-","newtask-");

for($i=0 ;$i<$len_to_emailarr;$i++){
	
    $email_code_split = split("@", $email_array[$i]);
    $email_code = $email_code_split[0];
    $email_code_domain = $email_code_split[1];
    if($email_code_domain == $GLOBALS['email_domain']){
	foreach($drop_box_list as $key=>$value){	 
		if(preg_match("/$value/",$email_code,$matches)) { 
			$valid = 1;
			break;
        }
	  }
    }
}


// empty vars
if($valid){
//echo $email;die();	
//fwrite($sf,"$message");
ob_end_clean();
}

//fclose($sf);
?>
<form method="post" action="ofuz_catch_new.php">
<textarea name="email" cols="40" rows="20"><?php echo $email;?></textarea>
<input type="submit" value="submit">
</form>
