#!/usr/bin/php -q
<?php
    // version 0.0001 experimentatal

$fp = fopen("php://stdin", "r");
 //$fp = fopen("mails.txt", "r");
$content_new = '';
$content_Email = "";
$message_content = Array();
// Attachement:
// filename, content

$attachment = Array();
// We should not need this :
//require_once("parseMail.php");
$mail = mailparse_msg_create();

while(!feof($fp)) {
  $content_new = fread($fp, 1024);
  $content_Email .= $content_new;
  mailparse_msg_parse($mail,$content_new);
}
fclose($fp);
$fp = fopen("/usr/local/sqlf/log/ofuz_catch.log", "a");
//fwrite($fp, $content_Email) ;
fclose($fp);
$struct = mailparse_msg_get_structure($mail);
function get_content ($str) {
    $GLOBALS['email_out'] .= $str;
}
$header = '';
$receive_story = '';
$section_content = '';
$base_header = mailparse_msg_get_part_data($mail);
foreach ($base_header as $info1name => $info1value) {
    if (is_array($info1value)) {
        $header .= "\n\nHeader: ";
        foreach($info1value as $headername=>$headervalue) {
              if (is_array($headervalue)) {
                foreach ($headervalue as $receivename=>$receivevalue) {
                  $receive_story .= "\n Receive: ".$receivename.":\n".$receivevalue;
                }
              } else {
                $header .= "\n".$headername.":".$headervalue;
              }
        }
          } else {
        $header .= "\n ".$info1name." = ".$info1value;
      }
}

foreach($struct as $st) {
    $GLOBALS['email_out'] = '';
    $section = mailparse_msg_get_part($mail, $st);
    $info = mailparse_msg_get_part_data($section);
    $header .= "\n\n Section:".$st;
    foreach ($info as $infoname => $infovalue) {
          if (is_array($infovalue)) {
            foreach ($infovalue as $sectionheadername => $sectionheadervalue) {
                $header .= "\n ".$sectionheadername.":".$sectionheadervalue;
            }
          } else {
            $header .= "\n ".$infoname." = ".$infovalue;
          }
    }
    mailparse_msg_extract_part ($section, $content_Email, "get_content");
    $section_content .= "\n--------------------------\n Section ".$st.":\n".$GLOBALS['email_out']."\n----------------------\n";
    // only collect plain/html content for now
    if ($info['content-type'] == "text/plain")  {
      $message_content[$st] = $GLOBALS['email_out'];
    }
    if ($info['content-type'] == "text/html")  {
      $message_content_html[$st] = $GLOBALS['email_out'];
    }
    // Extracting the attachments
    if (trim($info['content-disposition']) == "attachment") {
      $attachment[]['filename'] = $info['disposition-filename'];
      $attachment[]['content'] = $GLOBAL['email_out'];
    }
}
$fp = fopen("/usr/local/sqlf/log/ofuz_catch.log", "a");
//fwrite($fp, $receive_story) ;
//fwrite($fp, $header);

// Catch the target email address in the receive headers in case its a bcc.
$regexp = "/for \<(.*?)\>;/i";
if (preg_match($regexp, $receive_story, $receive_matches)) {
  $original_target = $receive_matches[1];
}
fwrite($fp,  "\n\ntarget email should show in bcc:".$original_target);
// end catching email target in the receive header

$final_message_content = '';
foreach ($message_content as $section => $content) {
  //$final_message_content .= mb_convert_encoding($content."\n", "UTF-8");
    $final_message_content .= $content."\n";

}

if(strlen($final_message_content) < 1){
  $final_message_content = '';
  foreach ($message_content_html as $section => $content) {
    //$final_message_content .= mb_convert_encoding($content."\n", "UTF-8");
    $final_message_content .= $content."\n";
  }
}

$final_message_content = strip_tags($final_message_content);
$final_message_content = nl2br($final_message_content);
$final_message_content = preg_replace('/(<br[^>]*>\s*){2,}/', '<br/>', $final_message_content);

//mb_convert_encoding($str, "UTF-8",
//fwrite($fp, "\n\n text/plain content:".$final_message_content);

//$task_from_php_email = $base_header['headers']['x-ofuz-emailer'];
$email_sub = $base_header['headers']['subject'];
$raw_to =  $base_header['headers']['to'];
$raw_cc =  $base_header['headers']['cc'];
$raw_from = $base_header['headers']['from'];
//$raw_cc =  $info['headers']['cc'];
// as bcc are invisible replaced by original_target
//$bcc = $info['headers']['bcc'];
$bcc = $original_target;
$to_parsed =  mailparse_rfc822_parse_addresses($raw_to);
$cc_parsed =  mailparse_rfc822_parse_addresses($raw_cc);
$bcc_parsed =  mailparse_rfc822_parse_addresses($bcc);
$from_parsed = mailparse_rfc822_parse_addresses($raw_from);

$to_namerr = array();
$to_emailarr =  array();
$email_marged_array = array();
$email_marged_array_task = array();
//$email_marged_array = array_merge($to_parsed,$cc_parsed,$bcc_parsed);
$email_marged_array = array_merge($to_parsed,$bcc_parsed);


$code_found = false;
$addnote = false;
$addtask = false;
$addprojectnote = false;
$add_project_task = false;

//fwrite($fp, "\nEmail array:".$email_marged_array);

/*
From the marge array for To/BCC get the email ids and
out in a seperate id and get the array length
*/
if(is_array($email_marged_array)){
  foreach($email_marged_array as $to){
    $to_namerr[] = $to['display'];
    $to_emailarr[] = $to['address'];
  //  fwrite($fp, "\nEmail found:".$to['address']);
  }
  $len_to_emailarr = count($to_emailarr);
}
/*
From the above email array  $to_emailarr check if there is an email id like
addnote-123@ofuz.net and get the drop box code from that.
*/
for($i=0 ;$i<$len_to_emailarr;$i++){
    $email_code_split = split("@", $to_emailarr[$i]);
    $email_code = $email_code_split[0];
    $email_code_domain = $email_code_split[1];
    if($email_code_domain == 'ofuz.net'){
      if(preg_match("/addnote-/",$email_code,$matches)) {
        $code_split = split("-",$email_code);
        $drop_box_code = $code_split[1];
        $code_found = true;
        $addnote = true;
        break;
      }
    }
}
/*
Generate a new merge array with TO/CC/BCC and get all the email addresses
and keep them in a seperate array $to_emailarr_task and get the array length.
*/
$email_marged_array_task = array_merge($to_parsed,$bcc_parsed,$cc_parsed);
if(is_array($email_marged_array_task)){
  foreach($email_marged_array_task as $to_task){
    $to_namerr_task[] = $to_task['display'];
    $to_emailarr_task[] = $to_task['address'];
    //fwrite($fp, "\nEmail found:".$to_task['address']);
  }
  $len_to_emailarr_task = count($to_emailarr_task);
}
/*
From the above generated array with email addresses check if there is an email id
like addtask-123@ofuz.net and then get the drop box code for that.
*/
for($i=0 ;$i<$len_to_emailarr_task;$i++){
    $email_code_split_task = split("@", $to_emailarr_task[$i]);
    $email_code_task = $email_code_split_task[0];
    $email_code_domain = $email_code_split_task[1];
    if($email_code_domain == 'ofuz.net'){
      if(preg_match("/addtask-/",$email_code_task,$matches)) {
        $code_split = split("-",$email_code_task);
        $drop_box_code_task = $code_split[1];
        $code_found = true;
        $addtask = true; 
        break;
      }
    }
}


$email_marged_array_proj_task = array_merge($to_parsed,$bcc_parsed,$cc_parsed);
if(is_array($email_marged_array_proj_task)){
  foreach($email_marged_array_proj_task as $proj_task){
    $to_namerr_proj_task[] = $proj_task['display'];
    $to_emailarr_proj_task[] = $proj_task['address'];
   // fwrite($fp, "\nEmail found:".$to_task['address']);
  }
  $len_to_emailarr_proj_task = count($email_marged_array_proj_task);
}

/*
  For project task drop box same email array is used as in the 
  task $email_marged_array_task
*/
for($i=0 ;$i<$len_to_emailarr_proj_task;$i++){
    $email_code_split_proj_task = split("@", $to_emailarr_proj_task[$i]);
    $email_code_proj_task = $email_code_split_proj_task[0];
    $email_code_proj_domain = $email_code_split_proj_task[1];
    if($email_code_proj_domain == 'ofuz.net'){
      if(preg_match("/task-/",$email_code_proj_task,$matches)) {
        $code_split = split("-",$email_code_proj_task);
        $drop_box_code_proj_task = $code_split[1];
        $addprojectnote = true;
        break;
      }
    }
}
// If the email is PHP generated while adding a task discussion and sending emails
if(preg_match("/x-ofuz-emailer/",$header,$matches)) {
   $addprojectnote = false;
}


/*
Adding a task for a project
Generate a new merge array with TO/CC/BCC and get all the email addresses
and keep them in a seperate array $to_emailarr_proj_task and get the array length.
*/
$to_emailarr_proj_task = array_merge($to_parsed,$bcc_parsed,$cc_parsed);
if(is_array($to_emailarr_proj_task)){
  foreach($to_emailarr_proj_task as $to_proj_task){
    $to_emailarr_proj_task[] = $to_proj_task['address'];
  }
  $len_to_emailarr_proj_task = count($to_emailarr_proj_task);
}

/*
From the above generated array with email addresses check if there is an email id
like newtask-5@ofuz.net and then get the drop box code for that.
*/
for($i=0 ;$i<$len_to_emailarr_proj_task;$i++){
    $email_code_split_proj_task = split("@", $to_emailarr_proj_task[$i]);
    $email_code_proj_task = $email_code_split_proj_task[0];
    $email_code_proj_domain = $email_code_split_proj_task[1];
    if($email_code_proj_domain == 'ofuz.net'){
      if(preg_match("/newtask-/",$email_code_proj_task,$matches)) {
        $code_split_proj_task = split("-",$email_code_proj_task);
        $drop_box_code_proj = $code_split_proj_task[1];
        $add_project_task = true;
        break;
      }
    }
}

//fwrite ($fp, "\nCodefound for Note:".$drop_box_code);
//fwrite ($fp, "\nCodefound for Task:".$drop_box_code_task);
if($addnote){
  //   fwrite ($fp, "\nAdding notes");
    // Replaced this with final message content to only extract plain/text
    //$parse_content = $GLOBALS['email_out'] ;
   // $parse_content = strip_tags($final_message_content);
      $parse_content = $final_message_content;
    // this is only for forwarded emails with inline forwarding vs attachment forwarding.
    // and should catch the > at the begining of the line: ereg_replace("^\>", "", $parse_content);
     $con = mysql_connect("dbserver","user","pass");
    if (!$con)
    {
      die('Could not connect: ' . mysql_error());
    //  fwrite ($fp, "\nError:". mysql_error());
    }
    mysql_select_db("ofuz", $con);
    $sel_qry  = "select iduser from user where drop_box_code = ".$drop_box_code;
     //fwrite ($fp, "\nExecuting the query:". $sel_qry);
    $result_sel = mysql_query($sel_qry);
    $number=mysql_num_rows($result_sel);
    if($number){
      //fwrite ($fp, "\nDrop box code is in database");
      while($row = mysql_fetch_array($result_sel)){
        $iduser = $row['iduser'];
	// fwrite($fp, "\Id User is :".$iduser);
      }
    }
    if($iduser){
      //fwrite($fp, "\nadding note to user:".$iduser);
      foreach($email_marged_array as $finalres){
        if(!preg_match("/addnote-/",$finalres["address"],$matches) && !preg_match("/addtask-/",$finalres["address"],$matches)) {
          $contact_email = $finalres['address'];
 	//  fwrite($fp, "\Checking for email id:".$finalres['address']); 
          $sel_cont_qry  = "select contact.idcontact as idcontact from contact
                            left join contact_email
                            on contact.idcontact = contact_email.idcontact
                            where (contact_email.email_address = '".$contact_email."' AND contact.iduser = ".$iduser.")";

              // echo $sel_cont_qry.'<br />';
          $result_sel = mysql_query($sel_cont_qry);
          $number=mysql_num_rows($result_sel);
          //fwrite($fp, "\n found:".$number." contacts with email:".$contact_email);
          if($number){
              while($row = mysql_fetch_array($result_sel)){
                $idcontact = $row['idcontact'];
              }
          }else{$idcontact = 0;}
          //    fwrite($fp, "\n  idcontact:".$idcontact);
              if($idcontact){
                $ins_qry = "INSERT INTO contact_note (iduser,idcontact,note,date_added)
                          VALUES('$iduser','$idcontact','".mysql_real_escape_string($parse_content)."',CURDATE())";
                //fwrite($fp, "\n query to insert contact:\n".$ins_qry);
                if (!mysql_query($ins_qry,$con)){
                    die('Error: ' . mysql_error());
                //	   fwrite($fp, "\n Error:" . mysql_error());
                }
              }else{
                  $name_email = $finalres['display'];
                  //$name_email = iconv("ISO-8859-1","UTF-8",$name_email);// for non ascii
                  $regexp = "/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";

                  if($name_email != ''){
                        $name = $name_email;
                  }else{
                        $name = $contact_email;
                  }
                  /*if (preg_match($regexp, $name_email)) {
                        $name = $name_email;
                  }else{
                      $name = $contact_email;
                  }*/

                  //$name  = iconv("ISO-8859-1","UTF-8",$name);
                  $ins_qry = "INSERT INTO contact(firstname,iduser) VALUES ('$name','$iduser')";
                  //fwrite($fp, "\n query to insert contact:\n".$ins_qry);
                  if (!mysql_query($ins_qry,$con)){
                    //fwrite($fp, "\n Contact  insertion failed \n");
                    //fwrite($fp, "\n Error:" . mysql_error());
                      die('Error: ' . mysql_error());
                  }else{
                      $idcontact = mysql_insert_id();
                      //fwrite($fp, "\n idcontact after insertion:\n".$idcontact);
                      // Inserting on contact_email
                      $qry_cnt_email = "Insert Into contact_email (idcontact,email_address,email_type) Values 
                            ('$idcontact','$contact_email','Work')
                      ";
                      if(!mysql_query($qry_cnt_email,$con)){
                        //fwrite($fp, "\n Error:" . mysql_error());
                        die('Error: ' . mysql_error());
                      }

                      /*
                          Once the contact is created we will need to enter the contact in the 
                          user contact table (userid123_contact)
                      */
                      $date_activity  = date("Y-m-d H:i:s");
                      $user_cnt_table = 'userid'.$iduser.'_contact';
                      $user_cnt_qry = "Insert Into ".$user_cnt_table." (idcontact,firstname,email_address,last_activity,last_update,first_created)
                              values('$idcontact','$name','$contact_email','$date_activity','$date_activity','$date_activity')
                              ";
                      if(!mysql_query($user_cnt_qry,$con)){
                          //fwrite($fp, "\n Error:" . mysql_error());
                          die('Error: ' . mysql_error());
                      }
			
                      $ins_activity = "INSERT INTO activity(`idcontact`,`when`) VALUES ('$idcontact','$date_activity')";
                      //fwrite($fp, "\n query to insert activity :\n".$ins_activity);
                      if (!mysql_query($ins_activity,$con)){
                      //  fwrite($fp, "\n query to insert activity failed :\n");
        	              die('Error: ' . mysql_error());
                      }
			
                      $ins_rec_log  = "INSERT INTO created_date_log (`table_name`,`id`,`created_date`) 
                          VALUES ('contact','$idcontact','$date_activity') ";
                      // fwrite($fp, "\n query to insert datelog :\n".$ins_rec_log);
                      if (!mysql_query($ins_rec_log,$con)){
                       //    fwrite($fp, "\n query to insert rec log failed :\n");
                              die('Error: ' . mysql_error());
                       }
		

                  }

                //  echo $ins_qry.'<br />';
                  $ins_qry_cont_email = "INSERT INTO contact_email(email_address,idcontact,email_type)
                                        VALUES ('$contact_email','$idcontact','Work')";
                  if (!mysql_query($ins_qry_cont_email,$con))
                  {
                      die('Error: ' . mysql_error());
                  }


                //  echo $ins_qry_cont_email.'<br />';
                  $ins_qry_note = "INSERT INTO contact_note (iduser,idcontact,note,date_added)
                            VALUES('$iduser','$idcontact','".mysql_real_escape_string($parse_content)."',CURDATE())";
                  if (!mysql_query($ins_qry_note,$con))
                  {
                      die('Error: ' . mysql_error());
                  }
                  // echo $ins_qry_note.'<br />';
            }
          }
      }
    }
}
if($addtask){
  fwrite ($fp, "\nadding task");
   $con =mysql_connect("dbserver","user","pass");
  if (!$con)
  {
    die('Could not connect: ' . mysql_error());
//     fwrite ($fp, "\nError".mysql_error() );
  }
  mysql_select_db("ofuz", $con);
  $sel_qry  = "select iduser from user where drop_box_code = ".$drop_box_code_task;
  // fwrite ($fp, "\nTask Query: ".$sel_qry );
  $result_sel = mysql_query($sel_qry);
  $number=mysql_num_rows($result_sel);
  if($number){
    while($row = mysql_fetch_array($result_sel)){
      $iduser = $row['iduser'];
    }
  }
  if($iduser){
    //  fwrite ($fp, "\nTask for userid ".$iduser );
     // fwrite ($fp, "\nSubject ".$email_sub );
      $cont_array = split("due:",$email_sub);
      $parse_content = $cont_array[0];
      $dateformat1 = "/due:([0-9]{4})-([0-9]{2})-([0-9]{2})$/";
      $dateformat2 = "/due:([0-9]{4})\/([0-9]{2})\/([0-9]{2})$/";
      $dateformat3 = "/due:([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/";
      $dateformat4 = "/due:([0-9]{2})-([0-9]{2})-([0-9]{4})$/";
    
      $category = 3; //Email
      if(preg_match("/due:tomorrow/",$email_sub,$matches) || preg_match("/due:Tomorrow/",$email_sub,$matches)) {
          $formated_date = date("Y-m-d",strtotime("+1 day"));
      }elseif(preg_match("/due:this week/",$email_sub,$matches) || preg_match("/due:This Week/",$email_sub,$matches)){
          $formated_date = date("Y-m-d",strtotime("next Friday"));
      }elseif(preg_match("/due:next week/",$email_sub,$matches) || preg_match("/due:Next Week/",$email_sub,$matches)){
        $formated_date = date("Y-m-d",strtotime("next Friday",strtotime("+1 week"))); 
      }elseif(preg_match("/due:Later/",$email_sub,$matches) || preg_match("/due:later/",$email_sub,$matches)){
        $formated_date = '0000-00-00';
      }elseif(preg_match($dateformat1,$email_sub,$matches)){
        if(preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})$/",$email_sub,$matches1)){
          $date_parsed = $matches1[0];
          $formated_date = date("Y-m-d",strtotime($date_parsed));
        }
      }elseif(preg_match($dateformat2,$email_sub,$matches)){
        if(preg_match("/([0-9]{4})\/([0-9]{2})\/([0-9]{2})$/",$email_sub,$matches1)){
          $date_parsed = $matches1[0];
          $formated_date = date("Y-m-d",strtotime($date_parsed));
        }
      }elseif(preg_match($dateformat3,$email_sub,$matches)){
        if(preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/",$email_sub,$matches1)){
          $date_parsed = $matches1[0];
          $formated_date = date("Y-m-d",strtotime($date_parsed));
        }
      }elseif(preg_match($dateformat4,$email_sub,$matches)){
        if(preg_match("/([0-9]{2})-([0-9]{2})-([0-9]{4})$/",$email_sub,$matches1)){
          $date_parsed = $matches1[0];
          $formated_date = date("Y-m-d",strtotime($date_parsed));
        }
      }else{
        $formated_date = date("Y-m-d");
      }
    
      $today = date("Y-m-d");
      $difference = strtotime($formated_date) - strtotime($today);
      $date_diff = round((($difference/60)/60)/24,0);
      if($date_diff < 0 ){
          $return_string = 'Today';
      }elseif($date_diff == 0 ){
          $return_string = 'Today';
      }elseif($date_diff == 1){
          $return_string = 'Tomorrow';
      }elseif($date_diff >=2 && $date_diff < 8){
          $return_string = 'This week';
      }elseif($date_diff >7 && $date_diff < 15){
          $return_string = 'Next week';
      }elseif($date_diff > 15){
          $return_string = 'Later';
      }
      $due_date = $return_string;
      $is_sp_date_set = "Yes";
      $ins_task = "INSERT INTO task(category,due_date,due_date_dateformat,iduser,is_sp_date_set,status,task_description)
                    VALUES(
                    '$category','$due_date','$formated_date','$iduser','$is_sp_date_set','open',
                    '".mysql_real_escape_string($parse_content)."')" ;
  
        //echo $ins_task;
   	// fwrite ($fp, "\nInserting task ".$ins_task );
      if (!mysql_query($ins_task,$con))
      {
          die('Error: ' . mysql_error());
	  //fwrite ($fp, "\nError ".mysql_error() );
      } 

   }
}

    
if($addprojectnote){
  //  fwrite ($fp, "\nInserting  project task ");
   $parse_content = ereg_replace("^\>", "", $final_message_content);
    $con = mysql_connect("dbserver","user","pass");
   // $con  = mysql_connect("dev2.sqlfusion.com","ofuz","phil");
    if (!$con)
    {
        //die('Could not connect: ' . mysql_error());
    //    fwrite ($fp, "\nError:". mysql_error());
    }
    if(!mysql_select_db("ofuz", $con)){
        //die('Could not select db: ' . mysql_error());
      //  fwrite ($fp, "\nError:". mysql_error()); 
    }    

    $sel_qry  = "select idproject_task,idproject from project_task where drop_box_code = ".$drop_box_code_proj_task;
    $result_sel = mysql_query($sel_qry);
    $number=mysql_num_rows($result_sel);
    if($number){
      while($row = mysql_fetch_array($result_sel)){
        $idproject_task = $row['idproject_task'];
        $idproject = $row['idproject'];
        //fwrite ($fp, "\n  project task found ".$idproject_task);
      }
    }
    if($idproject_task){
        $from_name = $from_parsed[0]['display'];
        //$from_name = iconv("ISO-8859-1","UTF-8",$from_name);
        $from_address = $from_parsed[0]['address'];
        //fwrite ($fp, "\n  From name ".$from_name);
       // fwrite ($fp, "\n  From Address ".$from_address);
        $look_user_qry = "select iduser from user where email = '".$from_address."'";
        $result_sel = mysql_query($look_user_qry);
        $number=mysql_num_rows($result_sel);
        if($number){
          while($row = mysql_fetch_array($result_sel)){
              $iduser = $row['iduser'];
             // fwrite ($fp, "\n  user found ".$iduser);
          }
          $ins_qry = "INSERT INTO project_discuss (idproject_task,iduser,discuss,date_added)
                          VALUES('$idproject_task','$iduser','".mysql_real_escape_string($parse_content)."',CURDATE())";
          if (!mysql_query($ins_qry,$con))
          {
              die('Error: ' . mysql_error());
          }
        }else{
          //check if the project is a public project and if yes allow the add 
          $sql_project = "select is_public from project where idproject = ".$idproject ;
          $sql_project_rs = mysql_query($sql_project);
          while($row = mysql_fetch_array($sql_project_rs)){
              $is_public = $row['is_public'];
          }
          // If the project is public let it add the discussion without being into project 
          if($is_public == 1 ){
              if($from_name == ''){$from_name = $from_address;}
              $ins_qry = "INSERT INTO project_discuss (idproject_task,drop_box_sender,discuss,date_added)
                              VALUES('$idproject_task','$from_name','".mysql_real_escape_string($parse_content)."',CURDATE())";
              //fwrite ($fp, "\n  Qry: ".$ins_qry);
              if (!mysql_query($ins_qry,$con))
              {
                  die('Error: ' . mysql_error());
              }
          }
        }
    }
    
}

if($add_project_task){
        fwrite ($fp, "\n  Adding a project task");
        $allow_db_operation = false;
        $allow_without_project_worker =  false;
        $from_address = $from_parsed[0]['address'];
        $con = mysql_connect("dbserver","user","pass");
         if (!$con){
            die('Could not connect: ' . mysql_error());
         }
         if(!mysql_select_db("ofuz", $con)){
            die('Could not select db: ' . mysql_error());
         }

         $look_user_qry = "select iduser from user where email = '".$from_address."'";
         //fwrite ($fp, "\n  User Query :".$look_user_qry);
         $result_sel = mysql_query($look_user_qry);
         $number=mysql_num_rows($result_sel);
         if($number > 0){
             while($row = mysql_fetch_array($result_sel)){
                 $iduser_email = $row['iduser'];
                  fwrite ($fp, "\n  Query executed ".$look_user_qry);
             }
             $qry_project_owner = "select iduser,idcoworker from project_sharing where idproject = ".$drop_box_code_proj;
             fwrite ($fp, "\n  User,coworker Query :".$qry_project_owner);
             $result_sel_proj_users = mysql_query($qry_project_owner);
             $number_users= mysql_num_rows($result_sel_proj_users);
             if($number_users > 0 ){
                  fwrite ($fp, "\n  User found :");
                  $user_array = array();
                  while($row_users = mysql_fetch_array($result_sel_proj_users)){
                      $user_array[] = $row_users["iduser"];
                      $user_array[] = $row_users["idcoworker"];
                  }
                  $user_array = array_unique($user_array);
                  //fwrite ($fp, "\n  Users in the array : ".var_dump($user_array));
             }else{
                  $qry_project_owner = "select iduser from project where idproject = ".$drop_box_code_proj;
                  fwrite ($fp, "\n  Qry :".$qry_project_owner);
                  $res = mysql_query($qry_project_owner);
                  while($row = mysql_fetch_array($res)){
                      $user_array[] = $row['iduser'];
                  }
             }
             fwrite ($fp, "\n  User ID ".$iduser_email);
             $user_found_success = false ;
             foreach($user_array as $id){
                if($id == $iduser_email){
                   $user_found_success = true ;
                   break;
                }
             }

             if ($user_found_success === true ) {
                    $allow_db_operation = true;
                    fwrite ($fp, "\n  User ID found");
             }else{
                  $sql_project = "select is_public from project where idproject = ".$drop_box_code_proj ;
                  fwrite ($fp, "\n  IQuery :".$sql_project);
                  $sql_project_rs = mysql_query($sql_project);
                  while($row = mysql_fetch_array($sql_project_rs)){
                      $is_public = $row['is_public'];
                      fwrite ($fp, "\n  IS PUBLIC VAL :".$is_public);
                  }
                  if($is_public == 1 ){
                      $allow_db_operation = true;
                      $allow_without_project_worker = true;
                      $from_name = $from_parsed[0]['display'];
                      //$from_name = iconv("ISO-8859-1","UTF-8",$from_name);
                      $from_note = $from_name.'<br /> '.$from_address._(' has the following note : ').'<br />';
                      $qry_project_owner = "select iduser from project where idproject = ".$drop_box_code_proj;
                      $result_project_owner = mysql_query($qry_project_owner);
                      while($row = mysql_fetch_array($result_project_owner)){
                          $iduser = $row['iduser'];
                      } 
                  }
              } 
             //}
             if($allow_db_operation){
                  fwrite ($fp, "\n  allow_db_operation");
                  $task_description = $email_sub;
                  $due_date = 'Today';
                  $task_category = 'Email';
                  $due_date_dateformat = date('Y-m-d');
                  $task_ins_qry = "INSERT INTO task (task_description,due_date,task_category,iduser,due_date_dateformat)
                  VALUES ('".mysql_real_escape_string($task_description)."','$due_date','$task_category','$iduser','$due_date_dateformat')";
                  if(mysql_query($task_ins_qry,$con)){
                      $task_id = mysql_insert_id();
                  }
                  $ins_proj_task = "INSERT INTO project_task (idtask, idproject) VALUES ('$task_id','$drop_box_code_proj')";
                  if(!mysql_query($ins_proj_task,$con)){
                      die('Error: ' . mysql_error());
                  }else{ $idproject_task =  mysql_insert_id(); }
                  if(strlen($final_message_content) > 4 ){
                      $parse_content = ereg_replace("^\>", "", $final_message_content);
                       if($allow_without_project_worker){
                          $parse_content = $from_name.$parse_content;
                       }
                       $ins_proj_diss = "INSERT INTO project_discuss (idproject_task,iduser,discuss,date_added)
                                VALUES('$idproject_task','$iduser','".mysql_real_escape_string($parse_content)."',CURDATE())";
                       if(!mysql_query($ins_proj_diss,$con)){
                           die('Error: ' . mysql_error());
                           fwrite ($fp, "\n  Inserted");
                       }
                  }
            }
        }else{

              $sql_project = "select is_public from project where idproject = ".$drop_box_code_proj ;
              $sql_project_rs = mysql_query($sql_project);
              while($row = mysql_fetch_array($sql_project_rs)){
                  $is_public = $row['is_public'];
              }
              if($is_public == 1 ){
                    $from_name = $from_parsed[0]['display'];
                    //$from_name = iconv("ISO-8859-1","UTF-8",$from_name);

                    $qry_project_owner = "select iduser from project where idproject = ".$drop_box_code_proj;
                    $result_project_owner = mysql_query($qry_project_owner);
                    while($row = mysql_fetch_array($result_project_owner)){
                        $idproject_owner = $row['iduser'];
                    }
                    $task_description = $email_sub;
                    $due_date = 'Today';
                    $task_category = 'Email';
                    $due_date_dateformat = date('Y-m-d');
                    $task_ins_qry = "INSERT INTO task (task_description,due_date,task_category,iduser,due_date_dateformat)
                    VALUES ('".mysql_real_escape_string($task_description)."','$due_date','$task_category','$idproject_owner','$due_date_dateformat')";
                    if(mysql_query($task_ins_qry,$con)){
                        $task_id = mysql_insert_id();
                    }
                    $ins_proj_task = "INSERT INTO project_task (idtask, idproject) VALUES ('$task_id','$drop_box_code_proj')";
                    if(!mysql_query($ins_proj_task,$con)){
                        die('Error: ' . mysql_error());
                    }else{ $idproject_task =  mysql_insert_id(); }
                    if(strlen($final_message_content) > 4 ){
                        $from_note = $from_name.'<br /> '.$from_address._(' has the following note : ').'<br />';
                        $parse_content = ereg_replace("^\>", "", $final_message_content);
                        $parse_content = $from_note.$parse_content;
                        $ins_proj_diss = "INSERT INTO project_discuss (idproject_task,iduser,discuss,date_added)
                        VALUES('$idproject_task','$idproject_owner','".mysql_real_escape_string($parse_content)."',CURDATE())";
                        if(!mysql_query($ins_proj_diss,$con)){
                              die('Error: ' . mysql_error());
                        }
                    }
              }
        }
}


fclose($fp);

?>

