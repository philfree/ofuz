<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    $pageTitle = 'Ofuz :: User Profile';
    $Author = 'SQLFusion LLC';
    include_once('config.php');


    if ($_GET['u']) {
        $do_contact = new Contact();
        $idcontact = $do_contact->getContactByUsername($_GET['u']);
        try {
        	if (!$do_contact->idcontact) { echo "No public profiles have been found."; exit;  }
        } catch (Exception $e) {
           echo $e->getMessage();
        }
        $idcontact = $do_contact->idcontact;
        $do_contact->sessionPersistent("do_contact", "index.php", OFUZ_TTL);
    } elseif (isset($_SESSION['do_contact'])) {
        $idcontact = $_SESSION['do_contact'];
    } else {
        exit;
    }
    $vcf_support = true ;
    $send_email = false ;
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if(preg_match("/iPhone/i",$user_agent,$matches)) {
      $vcf_support = false ;
    }elseif(preg_match("/BlackBerry/i",$user_agent,$matches)){
      $vcf_support = false ;
    }elseif(preg_match("/webOS/",$user_agent,$matches)){
      $vcf_support = false ;
    }

    /*if($_SERVER['HTTP_USER_AGENT'] == 'Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_1_3 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7E18 Safari/528.16'){
      $vcf_support = false ;
    }elseif($_SERVER['HTTP_USER_AGENT'] == 'BlackBerry9700/5.0.0.330 Profile/MIDP-2.1 Configuration/CLDC-1.1 VendorID/100'){
      $vcf_support = false ;
    }*/

    if($vcf_support === false){
      if(isset($_POST['email_mbl']) && $_POST['email_mbl'] != ''){
            $send_email = true;
            $email_mbl = $_POST['email_mbl'];
      }else{
            echo '<form method ="POST">';
            echo '<input type = "text" name = "email_mbl" id="email_mbl">';
            echo '<input type = "submit" name = "send_email" value="proceed">';
            echo '</form>';
            exit;
      }
    }
    $vcard = "BEGIN:VCARD\r\n";
    $vcard .= "VERSION:3.0\r\n";
    $vcard .= "REV:".date("Y-m-d")."\r\n";
    $vcard .= "N:".$_SESSION['do_contact']->firstname.";".$_SESSION['do_contact']->lastname.";;;\r\n";
    $vcard .= "FN:".$_SESSION['do_contact']->lastname." ".$_SESSION['do_contact']->firstname."\r\n";
    if($_SESSION['do_contact']->position != ''){ $vcard .= "TITLE:".$_SESSION['do_contact']->position."\r\n"; }
    if($_SESSION['do_contact']->company != ''){ $vcard .= "ORG:".$_SESSION['do_contact']->company."\r\n"; }

    $ContactAddress = $_SESSION['do_contact']->getChildContactAddress();
    if ($ContactAddress->getNumRows()) {
      while($ContactAddress->next()){
        $vcard .= "ADR;TYPE=".$ContactAddress->address_type.":"
        .str_replace(array("\r\n", "\r", "\n"), ' ', $ContactAddress->address)."\r\n";
      }

    }  
    
    $ContactEmail = $_SESSION['do_contact']->getChildContactEmail();
    if($ContactEmail->getNumRows()){
	while($ContactEmail->next()){
	    $vcard .= "EMAIL;TYPE=".$ContactEmail->email_type.",pref:".$ContactEmail->email_address."\r\n";
	}
    }

    $ContactPhone = $_SESSION['do_contact']->getChildContactPhone();
    if($ContactPhone->getNumRows()){
      while($ContactPhone->next()){
        $vcard .= "TEL;TYPE=".$ContactPhone->phone_type.",voice:".$ContactPhone->phone_number."\r\n";
      }
    }

    $ContactWebsite = $_SESSION['do_contact']->getChildContactWebsite();
    if($ContactWebsite->getNumRows()){
      while($ContactWebsite->next()){
        $vcard .= "URL;TYPE=".$ContactWebsite->website_type.":".$ContactWebsite->website."\r\n";
      }
    }
    $vcard .= "END:VCARD\r\n";

    //echo $vcard;
    $file_name = $_SESSION['public_profile_name'];
    if($send_email === true){
        $file_path = 'contact_vcf/'.$file_name.'vcf';
        $fh = fopen($file_path, 'w') or die("can't open file");
        fwrite($fh, $vcard);
        fclose($fh);
        $do_template = new EmailTemplate();
        $do_template->setSenderName("Ofuz.net");
        $do_template->setSenderEmail("info@ofuz.net");
        $do_template->setSubject( "Contact ::".$_SESSION['do_contact']->lastname." ".$_SESSION['do_contact']->firstname);
        //$do_template->setMessage("You have received a contact");
        $do_template->bodytext = "You have received a contact";
        $do_template->bodyhtml = nl2br($do_template->bodytext);
        $emailer = new Radria_Emailer();
        $emailer->setEmailTemplate($do_template);
        $emailer->mergeArray($email_data);
        $emailer->addTo($email_mbl);
        
        $vcf_file = file_get_contents($file_path);
          
        $at = $emailer->createAttachment($vcf_file);
        $at->type = 'text/x-vcard';
        //$at->disposition = Zend_Mime::DISPOSITION_INLINE;
        $at->encoding = Zend_Mime::ENCODING_BASE64;
        $at->filename = $file_name.'.vcf'; 
        try{
            $emailer->send(); 
            echo _('<b>Contact is sent.</b>');
            echo '<br /><a href="/profile/'.$_SESSION['public_profile_name'].'">'._('Go Back').'</a>';
        }catch(Exception $e){
            echo _('<b>Error Sending Email, Please try again.</b>');
            echo '<br />';
            echo '<br /><a href="/profile/'.$_SESSION['public_profile_name'].'">'._('Go Back').'</a>';
        }
	  
    }else{
        header("Content-type: text/x-vcard");
        header("Content-Disposition: attachment; filename=".$file_name.".vcf");
        header("Pragma: public");
        echo $vcard;
    }

   /* $vcard .=  '<div class="vcard">';
    $vcard .= '<span class="fn">'.$_SESSION['do_contact']->firstname.' '.$_SESSION['do_contact']->lastname.'</span>';
    
    $ContactAddress = $_SESSION['do_contact']->getChildContactAddress();
    if ($ContactAddress->getNumRows()) {
	  $vcard .= '<div class="adr">';
	  if($ContactAddress->getNumRows()){
	      $vcard .= '<span class="type">Work</span> :';
	      $vcard .= '<div class="street-address">'.nl2br($ContactAddress->address).'</div>';
	  }
    }
    
    $ContactPhone = $_SESSION['do_contact']->getChildContactPhone();
    if($ContactPhone->getNumRows()){
	while($ContactPhone->next()){
	    $vcard .= '<div class="tel">';
	    $vcard .= '<span class="type">'.$ContactPhone->phone_type.'</span> :'.$ContactPhone->phone_number ;
	    $vcard .= '</div>';
	}
    }

    $ContactEmail = $_SESSION['do_contact']->getChildContactEmail();
    if($ContactEmail->getNumRows()){
	  $vcard .= '<div class="email">';
	  $vcard .= '<span class="type">'.$ContactEmail->email_type.'</span> : <a href="mailto:'.$ContactEmail->email_address.'" title="'.$ContactEmail->email_type.'"> '.$ContactEmail->email_address.'</a>' ;
	  $vcard .= '</div>';
    }
    
    */

    
    //echo $vcard;

?>
