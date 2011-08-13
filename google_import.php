<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

    //session_start();
    include_once("config.php");
    require_once 'class/GoogleContactImport.class.php';

    $gci_contact = new GoogleContactImport();
    $gci_contact->id_user = $_SESSION['do_User']->iduser;

	//without storing SESSION TOKEN in table

    if(isset($_SESSION['sessionToken'])){
            //$gci_contact->uEmail = $_POST['email'];
            $gci_contact->processAuth();
            $gci_contact->retrieveUserContacts();

			//rebuilding the userXX_contact table
			$contact_view = new ContactView();
			$contact_view->setUser($_SESSION['do_User']->iduser);
			$contact_view->rebuildContactUserTable();

            //$contacts = $gci_contact->retrieveContactsPhotos(); exit();
            if($gci_contact->status_code_desc == ""){
                $status_code_desc = $gci_contact->getStatusDescription(207);
            } else{
                $status_code_desc = $gci_contact->status_code_desc;
            }
            if($_SESSION["page_from"] == 'reg'){
                header("Location:import_contacts.php?message=".$status_code_desc);
            }else{
                header("Location:sync.php?msg=".$status_code_desc);
            }
    }  
    else if($_POST['action']=="import")
    {
            $gci_contact->uEmail = $_POST['email'];
            $gci_contact->processAuth();
    }
    else if(isset($_GET['token']) && isset($_SESSION["uEmail"]))
    {
            //$gci_contact->uEmail = $_POST['email'];
            $gci_contact->processAuth();
            $gci_contact->retrieveUserContacts();

			//rebuilding the userXX_contact table
			$contact_view = new ContactView();
			$contact_view->setUser($_SESSION['do_User']->iduser);
			$contact_view->rebuildContactUserTable();

            //$contacts = $gci_contact->retrieveContactsPhotos(); exit();
            if($gci_contact->status_code_desc == ""){
                $status_code_desc = $gci_contact->getStatusDescription(207);
            } else{
                $status_code_desc = $gci_contact->status_code_desc;
            }
             if($_SESSION["page_from"] == 'reg'){
                header("Location:import_contacts.php?message=".$status_code_desc);
             }else{
                header("Location:sync.php?msg=".$status_code_desc);
             }
    }

?>