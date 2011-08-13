<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

set_time_limit(18000); //5 hrs
/**
  * ContactImport class
  * with all the usefull functions.
  * @author SQLFusion
  */


class ContactImport extends DataObject {


    function __construct() {
       //$this->target_path = $target_path;
    }

    function eventImportContactsFromCsv(EventControler $eventcontroler){
        $iduser = $eventcontroler->iduser;
        $handle = fopen($eventcontroler->targetpath, "r");
        $row = 1;
        while($data = fgetcsv($handle)){

            $contact_firstname = "";
            $contact_lastname = "";
            $contact_company = "";
            $company = "";
            $contact_position = "";
            $contact_summary = "";
            $contact_birthday = "";

            $contact_city = "";
            $contact_state = "";
            $contact_street = "";
            $contact_zipcode = "";
            $contact_country = "";
            $contact_address_hm = "";
            $contact_address_hm_type = "";
            $contact_address_wk = "";
            $contact_address_wk_type = "";
            $contact_address_ot = "";
            $contact_address_ot_type = "";

            $contact_email_hm = "";
            $contact_email_hm_type = "";
            $contact_email_wk = "";
            $contact_email_wk_type = "";
            $contact_email_ot = "";
            $contact_email_ot_type = "";

            $contact_phone_hm = "";
            $contact_phone_hm_type = "";
            $contact_phone_wk = "";
            $contact_phone_wk_type = "";
            $contact_phone_ot = "";
            $contact_phone_ot_type = "";
            $contact_phone_mb = "";
            $contact_phone_mb_type = "";
            $contact_phone_fx = "";
            $contact_phone_fx_type = "";

            $contact_website_comp = "";
            $contact_website_comp_type = "";
            $contact_website_blog = "";
            $contact_website_blog_type = "";
            $contact_website_ot = "";
            $contact_website_ot_type = "";
            $contact_website_personal = "";
            $contact_website_personal_type = "";
            $contact_website_twitter = "";
            $contact_website_twitter_type = "";

            $contact_im_aim_wk = "";
            $contact_im_aim_wk_type = "";
            $contact_im_aim_per = "";
            $contact_im_aim_per_type = "";
            $contact_im_aim_ot = "";
            $contact_im_aim_ot_type = "";
            $im_aim = "";

            $contact_im_msn_wk = "";
            $contact_im_msn_wk_type = "";
            $contact_im_msn_per = "";
            $contact_im_msn_per_type = "";
            $contact_im_msn_ot = "";
            $contact_im_msn_ot_type = "";
            $im_msn = "";

            $contact_im_icq_wk = "";
            $contact_im_icq_wk_type = "";
            $contact_im_icq_per = "";
            $contact_im_icq_per_type = "";
            $contact_im_icq_ot = "";
            $contact_im_icq_ot_type = "";
            $im_icq = "";

            $contact_im_jabber_wk = "";
            $contact_im_jabber_wk_type = "";
            $contact_im_jabber_per = "";
            $contact_im_jabber_per_type = "";
            $contact_im_jabber_ot = "";
            $contact_im_jabber_ot_type = "";
            $im_jabber = "";

            $contact_im_yahoo_wk = "";
            $contact_im_yahoo_wk_type = "";
            $contact_im_yahoo_per = "";
            $contact_im_yahoo_per_type = "";
            $contact_im_yahoo_ot = "";
            $contact_im_yahoo_ot_type = "";
            $im_yahoo = "";

            $contact_im_skype_wk = "";
            $contact_im_skype_wk_type = "";
            $contact_im_skype_per = "";
            $contact_im_skype_per_type = "";
            $contact_im_skype_ot = "";
            $contact_im_skype_ot_type = "";
            $im_skype = "";

            $contact_im_gt_wk = "";
            $contact_im_gt_wk_type = "";
            $contact_im_gt_per = "";
            $contact_im_gt_per_type = "";
            $contact_im_gt_ot = "";
            $contact_im_gt_ot_type = "";
            $im_gt = "";

            $contact_note = "";

            $do_contact = new Contact();
            $do_company = new Company();
            $do_contact_notes = new ContactNotes();

            $num = count($data);

            if($row > 1){
                for ($c=0; $c < $num; $c++) {

                    switch ($eventcontroler->fields[$c]) {
                        case "firstname":
                                            $contact_firstname = $data[$c];
                                            break;
                        case "lastname":
                                            $contact_lastname = $data[$c];
                                            break;
                        case "company":
                                            $company = $data[$c];
                                            $contact_company = $data[$c];
                                            break;
                        case "position":
                                            $contact_position = $data[$c];
                                            break;
                        case "summary":
                                            $contact_summary = $data[$c];
                                            break;
                        case "birthday":
                                            $contact_birthday = $data[$c];
                                            break;
                        case "city":
                                            $contact_city = $data[$c];
                                            break;
                        case "state":
                                            $contact_state = $data[$c];
                                            break;
                        case "street":
                                            $contact_street = $data[$c];
                                            break;
                        case "zipcode":
                                            $contact_zipcode = $data[$c];
                                            break;
                        case "country":
                                            $contact_country = $data[$c];
                                            break;
                        case "address_hm":
                                            $contact_address_hm = $data[$c];
                                            $contact_address_hm_type = "Home";
                                            break;
                        case "address_wk":
                                            $contact_address_wk = $data[$c];
                                            $contact_address_wk_type = "Work";
                                            break;
                        case "address_ot":
                                            $contact_address_ot = $data[$c];
                                            $contact_address_ot_type = "Other";
                                            break;
                        case "email_hm":
                                            $contact_email_hm = $data[$c];
                                            $contact_email_hm_type = "Home";
                                            break;
                        case "email_wk":
                                            $contact_email_wk = $data[$c];
                                            $contact_email_wk_type = "Work";
                                            break;
                        case "email_ot":
                                            $contact_email_ot = $data[$c];
                                            $contact_email_ot_type = "Other";
                                            break;
                        case "phone_number_hm":
                                            $contact_phone_hm = $data[$c];
                                            $contact_phone_hm_type = "Home";
                                            break;
                        case "phone_number_wk":
                                            $contact_phone_wk = $data[$c];
                                            $contact_phone_wk_type = "Work";
                                            break;
                        case "phone_number_ot":
                                            $contact_phone_ot = $data[$c];
                                            $contact_phone_ot_type = "Other";
                                            break;
                        case "phone_number_mb":
                                            $contact_phone_mb = $data[$c];
                                            $contact_phone_mb_type = "Mobile";
                                            break;
                        case "phone_number_fx":
                                            $contact_phone_fx = $data[$c];
                                            $contact_phone_fx_type = "Fax";
                                            break;
                        case "website_comp":
                                            $contact_website_comp = $data[$c];
                                            $contact_website_comp_type = "Company";
                                            break;
                        case "website_blog":
                                            $contact_website_blog = $data[$c];
                                            $contact_website_blog_type = "Blog";
                                            break;
                        case "website_ot":
                                            $contact_website_ot = $data[$c];
                                            $contact_website_ot_type = "Other";
                                            break;
                        case "website_personal":
                                            $contact_website_personal = $data[$c];
                                            $contact_website_personal_type = "Personal";
                                            break;
                        case "website_twitter":
                                            $contact_website_twitter = $data[$c];
                                            $contact_website_twitter_type = "Twitter";
                                            break;
                        case "website_linkedin":
                                            $contact_website_linkedin = $data[$c];
                                            $contact_website_linkedin_type = "LinkedIn";
                                            break;
                        case "website_fb":
                                            $contact_website_fb = $data[$c];
                                            $contact_website_fb_type = "Facebook";
                                            break;
                        case "im_aim_wk":
                                            
                                            $contact_im_aim_wk = $data[$c];
                                            $im_aim = "AIM";
                                            $contact_im_aim_wk_type = "Work";
                                            break;
                        case "im_aim_per":
                                            $contact_im_aim_per = $data[$c];
                                            $im_aim = "AIM";
                                            $contact_im_aim_per_type = "Personal";
                                            break;
                        case "im_aim_ot":
                                            $contact_im_aim_ot = $data[$c];
                                            $im_aim = "AIM";
                                            $contact_im_aim_ot_type = "Other";
                                            break;
                        case "im_msn_wk":
                                            $contact_im_msn_wk = $data[$c];
                                            $im_msn = "MSN";
                                            $contact_im_msn_wk_type = "Work";
                                            break;
                        case "im_msn_per":
                                            $contact_im_msn_per = $data[$c];
                                            $im_msn = "MSN";
                                            $contact_im_msn_per_type = "Personal";
                                            break;
                        case "im_msn_ot":
                                            $contact_im_msn_ot = $data[$c];
                                            $im_msn = "MSN";
                                            $contact_im_msn_ot_type = "Other";
                                            break;
                        case "im_icq_wk":
                                            $contact_im_icq_wk = $data[$c];
                                            $im_icq = "ICQ";
                                            $contact_im_icq_wk_type = "Work";
                                            break;
                        case "im_icq_per":
                                            $contact_im_icq_per = $data[$c];
                                            $im_icq = "ICQ";
                                            $contact_im_icq_per_type = "Personal";
                                            break;
                        case "im_icq_ot":
                                            $contact_im_icq_ot = $data[$c];
                                            $im_icq = "ICQ";
                                            $contact_im_icq_ot_type = "Other";
                                            break;

                        case "im_jabber_wk":
                                            $contact_im_jabber_wk = $data[$c];
                                            $im_jabber = "Jabber";
                                            $contact_im_jabber_wk_type = "Work";
                                            break;
                        case "im_jabber_per":
                                            $contact_im_jabber_per = $data[$c];
                                            $im_jabber = "Jabber";
                                            $contact_im_jabber_per_type = "Personal";
                                            break;
                        case "im_jabber_ot":
                                            $contact_im_jabber_ot = $data[$c];
                                            $im_jabber = "Jabber";
                                            $contact_im_jabber_ot_type = "Other";
                        case "im_yahoo_wk":
                                            $contact_im_yahoo_wk = $data[$c];
                                            $im_yahoo = "Yahoo";
                                            $contact_im_yahoo_wk_type = "Work";
                                            break;
                        case "im_yahoo_per":
                                            $contact_im_yahoo_per = $data[$c];
                                            $im_yahoo = "Yahoo";
                                            $contact_im_yahoo_per_type = "Personal";
                                            break;
                        case "im_yahoo_ot":
                                            $contact_im_yahoo_ot = $data[$c];
                                            $im_yahoo = "Yahoo";
                                            $contact_im_yahoo_ot_type = "Other";
                                            break;
                        case "im_skype_wk":
                                            $contact_im_skype_wk = $data[$c];
                                            $im_skype = "Skype";
                                            $contact_im_skype_wk_type = "Work";
                                            break;
                        case "im_skype_per":
                                            $contact_im_skype_per = $data[$c];
                                            $im_skype = "Skype";
                                            $contact_im_skype_per_type = "Personal";
                                            break;
                        case "im_skype_ot":
                                            $contact_im_skype_ot = $data[$c];
                                            $im_skype = "Skype";
                                            $contact_im_skype_ot_type = "Other";
                        case "im_gt_wk":
                                            $contact_im_gt_wk = $data[$c];
                                            $im_gt = "Google Talk";
                                            $contact_im_gt_wk_type = "Work";
                                            break;
                        case "im_gt_per":
                                            $contact_im_gt_per = $data[$c];
                                            $im_gt = "Google Talk";
                                            $contact_im_gt_per_type = "Personal";
                                            break;
                        case "im_gt_ot":
                                            $contact_im_gt_ot = $data[$c];
                                            $im_gt = "Google Talk";
                                            $contact_im_gt_ot_type = "Other";
                                            break;
                        case "note":
                                            $contact_note .= "<p>".$data[$c]."</p>";
                                            break;
                    
                    }
        
                }
        
                $do_company->name = $company;
                $do_company->iduser = $iduser;
                $do_company->add();
                $idcompany = $do_company->getPrimaryKeyValue();

                $do_contact->idcompany = $idcompany;
                $do_contact->iduser = $iduser;
                $do_contact->firstname = $contact_firstname;
                $do_contact->lastname = $contact_lastname;
                $do_contact->company = $contact_company;
                $do_contact->position = $contact_position;
                $do_contact->summary = $contact_summary;
                $do_contact->birthday = $contact_birthday;
                $do_contact->add();
                $idcontact = $do_contact->getPrimaryKeyValue();
                //In theory this below should not be needed
                $do_contact->idcontact = $idcontact;

                $do_tag = new Tag();
                if (strpos($_SESSION['import_tag'],",") === false) {
                    $do_tag->addTagAssociation($idcontact,trim($_SESSION['import_tag']),"contact",$_SESSION['do_User']->iduser);
                } else {
                    $tags = explode(",", $_SESSION['import_tag']);
                    foreach($tags as $tag) {
                        $do_tag->addTagAssociation($idcontact,trim($tag),"contact",$_SESSION['do_User']->iduser);
                    }
                }

                if(($contact_address_hm != "") || ($contact_city != "") || ($contact_state != "") || ($contact_street != "") || ($contact_zipcode != "") || ($contact_country != "") ){
                $do_contact->addAddress($contact_address_hm,$contact_address_hm_type,$contact_city,$contact_state,$contact_street,$contact_zipcode,$contact_country);
                }

                if(($contact_address_wk != "") || ($contact_city != "") || ($contact_state != "") || ($contact_street != "") || ($contact_zipcode != "") || ($contact_country != "") ){
                $do_contact->addAddress($contact_address_wk,$contact_address_wk_type,$contact_city,$contact_state,$contact_street,$contact_zipcode,$contact_country);
                }

                if(($contact_address_ot != "") || ($contact_city != "") || ($contact_state != "") || ($contact_street != "") || ($contact_zipcode != "") || ($contact_country != "") ){
                $do_contact->addAddress($contact_address_ot,$contact_address_ot_type,$contact_city,$contact_state,$contact_street,$contact_zipcode,$contact_country);
                }

                if($contact_email_hm != ""){
                $do_contact->addEmail($contact_email_hm,$contact_email_hm_type);
                }
                if($contact_email_wk != ""){
                $do_contact->addEmail($contact_email_wk,$contact_email_wk_type);
                }
                if($contact_email_ot != ""){
                $do_contact->addEmail($contact_email_ot,$contact_email_ot_type);
                }

                if($contact_phone_hm != ""){
                $do_contact->addPhone($contact_phone_hm,$contact_phone_hm_type);
                }
                if($contact_phone_wk != ""){
                $do_contact->addPhone($contact_phone_wk,$contact_phone_wk_type);
                }
                if($contact_phone_ot != ""){
                $do_contact->addPhone($contact_phone_ot,$contact_phone_ot_type);
                }
                if($contact_phone_mb != ""){
                $do_contact->addPhone($contact_phone_mb,$contact_phone_mb_type);
                }
                if($contact_phone_fx != ""){
                $do_contact->addPhone($contact_phone_fx,$contact_phone_fx_type);
                }

                if($contact_website_comp != ""){
                $do_contact->addWebsite($contact_website_comp, $contact_website_comp_type);
                }
                if($contact_website_blog != ""){
                $do_contact->addWebsite($contact_website_blog, $contact_website_blog_type);
                }
                if($contact_website_ot != ""){
                $do_contact->addWebsite($contact_website_ot, $contact_website_ot_type);
                }
                if($contact_website_personal != ""){
                $do_contact->addWebsite($contact_website_personal, $contact_website_personal_type);
                }
                if($contact_website_twitter != ""){
                $do_contact->addWebsite($contact_website_twitter, $contact_website_twitter_type);
                }
                if($contact_website_linkedin != ""){
                $do_contact->addWebsite($contact_website_linkedin, $contact_website_linkedin_type);
                }
                if($contact_website_fb != ""){
                $do_contact->addWebsite($contact_website_fb, $contact_website_fb_type);
                }

                if($contact_im_aim_wk != ""){
                $do_contact->addIM($im_aim, $contact_im_aim_wk_type, $contact_im_aim_wk);
                }
                if($contact_im_aim_per != ""){
                $do_contact->addIM($im_aim, $contact_im_aim_per_type, $contact_im_aim_per);
                }
                if($contact_im_aim_ot != ""){
                $do_contact->addIM($im_aim, $contact_im_aim_ot_type, $contact_im_aim_ot);
                }

                if($contact_im_msn_wk != ""){
                $do_contact->addIM($im_msn, $contact_im_msn_wk_type, $contact_im_msn_wk);
                }
                if($contact_im_msn_per != ""){
                $do_contact->addIM($im_msn, $contact_im_msn_per_type, $contact_im_msn_per);
                }
                if($contact_im_msn_ot != ""){
                $do_contact->addIM($im_msn, $contact_im_msn_ot_type, $contact_im_msn_ot);
                }

                if($contact_im_icq_wk != ""){
                $do_contact->addIM($im_icq, $contact_im_icq_wk_type, $contact_im_icq_wk);
                }
                if($contact_im_icq_per != ""){
                $do_contact->addIM($im_icq, $contact_im_icq_per_type, $contact_im_icq_per);
                }
                if($contact_im_icq_ot != ""){
                $do_contact->addIM($im_icq, $contact_im_icq_ot_type, $contact_im_icq_ot);
                }

                if($contact_im_jabber_wk != ""){
                $do_contact->addIM($im_jabber, $contact_im_jabber_wk_type, $contact_im_jabber_wk);
                }
                if($contact_im_jabber_per != ""){
                $do_contact->addIM($im_jabber, $contact_im_jabber_per_type, $contact_im_jabber_per);
                }
                if($contact_im_jabber_ot != ""){
                $do_contact->addIM($im_jabber, $contact_im_jabber_ot_type, $contact_im_jabber_ot);
                }

                if($contact_im_yahoo_wk != ""){
                $do_contact->addIM($im_yahoo, $contact_im_yahoo_wk_type, $contact_im_yahoo_wk);
                }
                if($contact_im_yahoo_per != ""){
                $do_contact->addIM($im_yahoo, $contact_im_yahoo_per_type, $contact_im_yahoo_per);
                }
                if($contact_im_yahoo_ot != ""){
                $do_contact->addIM($im_yahoo, $contact_im_yahoo_ot_type, $contact_im_yahoo_ot);
                }

                if($contact_im_skype_wk != ""){
                $do_contact->addIM($im_skype, $contact_im_skype_wk_type, $contact_im_skype_wk);
                }
                if($contact_im_skype_per != ""){
                $do_contact->addIM($im_skype, $contact_im_skype_per_type, $contact_im_skype_per);
                }
                if($contact_im_skype_ot != ""){
                $do_contact->addIM($im_skype, $contact_im_skype_ot_type, $contact_im_skype_ot);
                }

                if($contact_im_gt_wk != ""){
                $do_contact->addIM($im_gt, $contact_im_gt_wk_type, $contact_im_gt_wk);
                }
                if($contact_im_gt_per != ""){
                $do_contact->addIM($im_gt, $contact_im_gt_per_type, $contact_im_gt_per);
                }
                if($contact_im_gt_ot != ""){
                $do_contact->addIM($im_gt, $contact_im_gt_ot_type, $contact_im_gt_ot);
                }

                if($contact_note != ""){
                    $do_contact_notes->idcontact = $idcontact;
                    $do_contact_notes->iduser = $iduser;
                    $do_contact_notes->note = $contact_note;
                    $do_contact_notes->date_added = date('Y-m-d');
                    $do_contact_notes->add();
                }

                $do_cont_view =  new ContactView();
                $do_cont_view->addFromContact($do_contact);
                $do_cont_view->updateFromContact($do_contact);// Added the method call updateFromContact() so that the child data is updated just after insert
                $do_cont_view->addTag($_SESSION['import_tag'],$do_contact->idcontact);// Update the contact view for tags.

        
                $do_contact->free();
                $do_company->free();
                $do_contact_notes->free();
        
            }
        
            $row++;
        }
        fclose($handle);

        $goto = $eventcontroler->getParam("goto");
        $disp = new Display($goto);
        $disp->addParam("message", "Contacts have been imported successfully.");
        $eventcontroler->setDisplayNext($disp);
    }

    function eventCsvUpload(EventControler $eventcontroler){
       /* echo $_FILES['fields']['name']['contact_csv'];
        exit();*/
        $msg = "";

        $uploaded_file = $_FILES['fields']['name']['contact_csv'];
        $target_path = 'files/' . $uploaded_file;

        if(!move_uploaded_file($_FILES['fields']['tmp_name']['contact_csv'], $target_path)) {
              $msg = "There was an error uploading the file, please try again!";
        }
        chmod($target_path, 0755);

        $_SESSION['csv_file'] = $_FILES['fields']['name']['contact_csv'];

        $_SESSION['import_tag'] = $eventcontroler->fields['import_tag'];

        $goto = $eventcontroler->getParam("goto");
        $disp = new Display($goto);
        if($eventcontroler->getParam("fromReg") == 'Yes'){//if importing while registration
           $_SESSION["page_from"] = 'reg';
        }
        $eventcontroler->setDisplayNext($disp);
    }
}

?>
