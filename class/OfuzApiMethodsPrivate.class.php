<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

   /**
    * Execute all the API methods
    *
    * Copyright 2009 - 2010 SQLFusion LLC
    * @author Abhik Chakraborty abhik@sqlfusion.com, Philippe phil@sqlfusion.com
    * @version 0.1
    * Changed the class to extends OfuzApiMethods and reduce the duplicate code. 
    * 
    *
    */
include_once('config.php');
include_once('class/OfuzApiBase.class.php');
class OfuzApiMethodsPrivate extends OfuzApiMethods {

    
    /*Constructor Function*/
    function OfuzApiMethodsPrivate($output_type="json", $values=Array()){
              parent::OfuzApiMethods($output_type, $values);
      }

    /*
        Method adding User 
    */
    function add_user(){
        $do_api_user = new User();
        if($this->firstname == '' || $this->lastname == ''){
             $this->setMessage("521","First name, Last name required");
	     return false;
        }elseif($this->email ==''){
             $this->setMessage("522","Email Id Required");
	     return false;
        }elseif($this->username ==''){
             $this->setMessage("523","Username required.");
	     return false;
        }elseif($do_api_user->checkDuplicateUserName(trim($this->username))){
              $this->setMessage("524","Username is already in use.");
	      return false;
        }elseif($this->password == ''){
              $this->setMessage("525","Password is required.");
	      return false;
        }else{
             $do_api_user->addNew();
             $do_api_user->firstname = $this->firstname;
             $do_api_user->lastname = $this->lastname;
             $do_api_user->email = $this->email;
             $do_api_user->username = $this->username;
             $do_api_user->password = $this->password;
             $do_api_user->company = $this->company;
             $do_api_user->plan = $this->plan;
             $do_api_user->regdate = date("Y-m-d");
             $do_api_user->add();
             $inserted_id = $do_api_user->getPrimaryKeyValue();
             //$do_api_user->addUserAsContact($this->firstname,$this->lastname,$this->company,$this->email,$inserted_id);
            // Lets create the Contact view now
             $ContactView = new ContactView();
             $ContactView->rebuildContactUserTable($inserted_id);

             $this->email_work = $this->email;
             $this->add_contact(); // adding the contact to the API key user
             
			 //$do_api_user->idcontact = $this->idcontact;
			 //$do_api_user->update();
             
             $this->setValues(Array("msg" => "User Added", "stat" => "ok", "code" => "520","iduser"=>$inserted_id,"contact"=>$this->idcontact));
             return true; 
        }    
    }

    /*
        Method deleting a user
    */
    function delete_user(){
          $do_api_user = new User();
          $do_api_user->getId($this->user_id);
          $do_api_user->delete();
    }

    /*
        function setting user as status = active
		* set the Ofuz user with status = active
		* requires user_id (iduser is the API owner)
    */
    function set_user_active(){
          $do_api_user = new User();
          $do_api_user->getId($this->user_id);
          $do_api_user->status = 'active';
          $do_api_user->update();
    }

    /*
        function setting reg user invoice Log
    */
    function set_reg_user_invoice(){
          $do_api_reg_inv_log = new RegistrationInvoiceLog();
          $do_api_reg_inv_log->addNew();
          $do_api_reg_inv_log->iduser = $this->iduser;
          $do_api_reg_inv_log->idinvoice = $this->idinvoice;
          $do_api_reg_inv_log->reg_iduser = $this->reg_iduser;
          $do_api_reg_inv_log->add();
    }

    /*
        function getting reg user invoice Log
    */
    function get_reg_user_invoice(){
          $do_api_reg_inv_log = new RegistrationInvoiceLog();
          $iduser_returned = $do_api_reg_inv_log->getUserIdRegistered($this->idinvoice,$this->iduser);
          if($iduser_returned){
              $this->setValues(Array("msg" => "Reg User Id Found", "stat" => "ok", "code" => "810","iduser"=>$iduser_returned));
              return true; 
          }else{
            $this->setValues(Array("msg" => "Reg User Id Not Found", "stat" => "fail", "code" => "811"));
            return true; 
          }
    }

 }   

?>
