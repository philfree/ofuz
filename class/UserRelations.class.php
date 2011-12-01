<?php 
// Copyrights 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Contact class
     * Using the DataObject
	 * Copyright 2001 - 2009 SQLFusion LLC,  info@sqlfusion.com 
     */
   
class UserRelations extends DataObject {
    
    public $table = "user_relations";
    protected $primary_key = "iduser_relations";
    public $secretkey = '';// Set in the config file

    /*function __construct(){
	$this->secretkey = ENC_SECRECT_KEY ;
    }*/

    function __construct(sqlConnect $conx=NULL, $table_name="") {
       parent::__construct($conx, $table_name);
       $this->setLogRun(RADRIA_LOG_RUN_OFUZ);
       $this->secretkey = ENC_SECRECT_KEY ;
    }
      
    public $savedforms = Array (
                    "CoWorkerForm" => "coworker_add_form");
    
    function generateFromAddCoWorker($nextPage,$block=false){
        $errPage = $nextPage;
       
        $co_worker_form =  $this->prepareSavedForm($this->savedforms['CoWorkerForm']);	 
        $co_worker_form->setFormEvent($this->getObjectName()."->eventSetCoWorker",10);
        $co_worker_form->addParam("goto",$nextPage);
        $co_worker_form->addParam("errPage", $errPage);
        $co_worker_form->setForm();
        if($block === false ){
            $co_worker_form->execute();
        }else{
            return $co_worker_form->executeToString();
        }
    }


	function generateFromAddContactAsCoWorker($nextPage){
        $errPage = $nextPage;
        $this->setRegistry('ofuz_contact_emails');
        $co_worker_form =  $this->prepareSavedForm('coworker_contact_add');	 
        $co_worker_form->setFormEvent($this->getObjectName()."->eventSetCoWorker",10);
        $co_worker_form->addParam("goto",$nextPage);
        $co_worker_form->addParam("errPage", $errPage);
        $co_worker_form->setForm();
        $co_worker_form->execute();
    }

     function eventSetCoWorker(EventControler $evtcl) {
		         
        $goto = $evtcl->goto;

        if($evtcl->fields['email_address'] != "") {
        $email_user = $evtcl->fields['email_address'];
        } else {
              $email_user = $evtcl->email;
        }
        if($email_user != '' && !empty($email_user)){
           // $email_user = $evtcl->email;//echo $email_user;exit;
            $iduser = $this->isUserExists($email_user);
            // user already in the db so just add 
            if($iduser){ 
              $q = new sqlQuery($this->getDbCon()) ;
              $q->query("select * from ".$this->table." where iduser = ".$_SESSION['do_User']->iduser." AND idcoworker= ".$iduser);
              //echo "select * from ".$this->table." where iduser = ".$_SESSION['do_User']->iduser." AND idcoworker= ".$iduser;exit;
              if($q->getNumRows() > 0 ){
                  while($q->fetch()){
                      $accepted = $q->getData("accepted");
                  }
                  if($accepted == 'Yes'){
                      $_SESSION['in_page_message'] = "cw_user-is-already-cw";
                  }elseif($accepted == 'No'){
                      $_SESSION['in_page_message'] = "cw_already-have-pending-invitation";
                  }
  
              }else{
                  $this->iduser = $_SESSION['do_User']->iduser;
                  $this->idcoworker = $iduser;
                  $this->accepted = 'No';
                  $this->add();
                  $_SESSION['in_page_message'] = "cw_user-is-already-in-db-notification-sent";

              }
            }else{ // Not found send the email.
              $enc_email =  $this->encrypt($email_user);
              $q = new sqlQuery($this->getDbCon()) ;
              $q->query("select * from ".$this->table." where iduser = ".$_SESSION['do_User']->iduser." AND enc_email = '".$enc_email."' AND accepted ='No'");
              //echo "select * from ".$this->table." where enc_email = '".$enc_email."' AND accepted ='No'";exit;
              if($q->getNumRows() > 0 ){ // If the invitation is still pending
                $_SESSION['in_page_message'] = "cw_already-have-pending-invitation-to";
                $_SESSION['in_page_message_data']['enc_email'] = $this->decrypt($enc_email);

              }else{
                  $this->iduser = $_SESSION['do_User']->iduser;
                  $this->idcoworker = 0;
                  $this->accepted = 'No';
                  $this->enc_email = $enc_email;
                  $this->add();
                  $last_id = $this->getInsertId($this->table, $this->primary_key);
                  include_once("class/Emailer.class.php");
                  $q = new sqlQuery($this->getDbCon());
                  $q->query("select * from ".$this->table." where ".$this->primary_key." = ".$last_id);
                  if ($q->getNumRows() > 0) {
                    while($dData = $q->fetchArray()) {
                        $dData["firstname"] = $_SESSION['do_User']->firstname;
                        $dData["referer"] = $this->encrypt($last_id);
                        $full_name = $_SESSION['do_User']->firstname." ".$_SESSION['do_User']->lastname;
                        $email = new Emailer() ;
                        $email->loadEmailer($this->getDbCon(), "invitation") ;
                        $email->setSender($full_name, $_SESSION['do_User']->email);
                        $email->mergeArray($dData) ;
                        if ($email->hasHtml()) {
                            $email->sendMailHtml($email_user);
                        } else {
                            $email->sendMailStandard($email_user);
                        }
                        $_SESSION['in_page_message'] =  "cw_user-not-in-db-register";

                    }
                  }
                }
            }
          }else{
             $_SESSION['in_page_message'] =  "cw_enter-emailid";
          }
     }

     function isUserExists($email){
        $q = new sqlQuery($this->getDbCon()) ;
        $q->query("Select iduser from user where email = '".trim($email)."'");
        if ($q->getNumRows() > 0){
            while($q->fetch()){
              $iduser =  $q->getData("iduser");
            }
            return $iduser;
        }else{ return false; }
     }


     

     /**
      * Method to add a user relation
      * @param $iduser --int
      * @param $coworker --int
      */
     function addToCoWorkerRel($iduser,$coworker){
      $this->addNew();
      $this->iduser = $iduser ;
      $this->idcoworker = $coworker ;
      $this->accepted = 'Yes';
      $this->add();
     }

     function getAllRequest(){
        $this->query("select * from user_relations where idcoworker = ".$_SESSION['do_User']->iduser." AND accepted = 'No'");
     }

     function eventAcceptInvitation(EventControler $evtcl) {
        $iduser_relations = $evtcl->id;
        $goto = $evtcl->goto;
        $this->getId($iduser_relations);
        $this->accepted = 'Yes';
        $this->update();

        $iduser = $evtcl->user;
        $idcoworker = $evtcl->coworker;
        $q1 = new sqlQuery($this->getDbCon()) ;
        $q1->query("select * from ".$this->table. " where idcoworker = ".$iduser. " AND iduser = ".$idcoworker);
        if($q1->getNumRows() > 0 ){
          while($q1->fetch()){
            $accepted = $q1->getData("accepted");
            $id = $q1->getData("iduser_relations");
          }
          if($accepted == 'No'){
            $q_upd = new sqlQuery($this->getDbCon()) ;
            $q_upd->query("update ".$this->table ." set accepted = 'Yes' where iduser_relations = ".$id);
          }
        }else{
          $q_ins = new sqlQuery($this->getDbCon()) ;
          $q_ins->query("INSERT INTO ".$this->table."(iduser,idcoworker,accepted) VALUES ('$idcoworker','$iduser','Yes') ");
        }
        $this->sendAcceptRejectNotificationEmail($iduser);// iduser is the user who sent the invitation
     }
    
     function eventRejectInvitation(EventControler $evtcl) {
        $iduser_relations = $evtcl->id;
        $goto = $evtcl->goto;
        $this->getId($iduser_relations);
        $iduser = $this->idcoworker;
        $this->accepted = 'Reject';
        $this->update();
        $this->sendAcceptRejectNotificationEmail($iduser,"No");// iduser is the user who sent the invitation
     }

     function getAllRequestsSent(){
        $this->query("select * from user_relations where iduser = ".$_SESSION['do_User']->iduser." AND accepted = 'No'");
     } 

     function getAllRequestsRejected(){
        $this->query("select * from user_relations where iduser = ".$_SESSION['do_User']->iduser." AND accepted = 'Reject'");
     }

     function getAllCoWorker(){
        //$this->query("select * from user_relations where iduser = ".$_SESSION['do_User']->iduser." AND accepted = 'Yes'");
          $this->query("select user_relations.*,user.firstname,user.lastname from user_relations 
                        inner join user on user.iduser = user_relations.idcoworker
                        where user_relations.iduser = ".$_SESSION['do_User']->iduser." AND user_relations.accepted = 'Yes'
                        order by user.firstname
                        ");
     }
     
     function getAllCoWorkersNotInTeam($str_idco_workers){
		 if($str_idco_workers) {
			 $not_in = " AND user_relations.idcoworker not in (".$str_idco_workers.")";
		 }
          $this->query("select user_relations.*,user.firstname,user.lastname from user_relations 
                        inner join user on user.iduser = user_relations.idcoworker
                        where user_relations.iduser = ".$_SESSION['do_User']->iduser." AND user_relations.accepted = 'Yes' ".$not_in."
                        order by user.firstname
                        ");
     }     

     function checkRegURL($enc_email,$id){
        $q = new sqlQuery($this->getDbCon()) ;
        $q->query("select * from ".$this->table." where enc_email = '".$enc_email."' AND ".$this->primary_key." = '".$this->decrypt($id)."' AND accepted ='No'");
        if($q->getNumRows() > 0 ){ 
            return true;
        }else{return false;}
     }
     
     /**
       * Function to check if the relation exists in the table
       * @param $id -- int
       * @return boolean
     */
     function ifRelationExist($id){
        $q = new sqlQuery($this->getDbCon()) ; 
        $q->query("select * from ".$this->table." where ".$this->primary_key." = ".$id);
        if($q->getNumRows() > 0 ){
            return true ;
        }else{
            return false ;
        }
     }

     function getCoWorkerRelationData($idcoworker){
       $q = new sqlQuery($this->getDbCon()) ; 
       $q->query("select * from ".$this->table." where iduser = ".$_SESSION['do_User']->iduser." AND idcoworker = ".$idcoworker);
       if($q->getNumRows()){
          $data = array();
          while($q->fetch()){
            $data["iduser"] = $q->getData("iduser");
            $data["idcoworker"] = $q->getData("idcoworker");
            $data["accepted"] = $q->getData("accepted");
          }
          return $data;
       }else{return false;}
     } 
    
     function getAllCoWorkerRelationData(){
       $q = new sqlQuery($this->getDbCon()) ; 
       $q->query("SELECT user.iduser, user.firstname, user.lastname
                  FROM user
                  LEFT JOIN user_relations ON user.iduser = user_relations.idcoworker
                  WHERE user_relations.iduser = ".$_SESSION['do_User']->iduser."
                  AND user_relations.accepted = 'Yes'"
                );
       if($q->getNumRows()){
          $ret_array = array();
          $data = array();
          while($q->fetch()){
            $data["iduser"] = $q->getData("iduser");
            $data["firstname"] = $q->getData("firstname");
            $data["lastname"] = $q->getData("lastname");
            $ret_array[] = $data;
          }
          return $ret_array;
       }else{return false;}
     }      

    

     function eventAddAsCoWorker(EventControler $evtcl) {
        $this->iduser = $evtcl->iduser;
        $this->idcoworker = $evtcl->idcoworker;
        $this->accepted = 'No';
        $this->add();
        $this->sendCoWorkerInvitationEmail($evtcl->idcoworker);
        $_SESSION['in_page_message'] =  "cw_notification_sent_to_user";
        $disp = new Display($evtcl->goto) ;
        
     }

    function eventRemoveInvitation(EventControler $evtcl) {
       $id = $evtcl->id;  
       if($id){
          $q = new sqlQuery($this->getDbCon()) ; 
          $q->query("delete from ".$this->table. " where iduser_relations = ".$id." Limit 1");
       }   
       $evtcl->setDisplayNext(new Display($evtcl->goto)); 
    }

    function encrypt($sData){
      $sKey = $this->secretkey;
      $sResult = '';
      for($i = 0; $i < strlen($sData); $i ++){
        $sChar    = substr($sData, $i, 1);
        $sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
        $sChar    = chr(ord($sChar) + ord($sKeyChar));
        $sResult .= $sChar;
      }
      return $this->encode_base64($sResult);
    }

   function decrypt($sData){
      $sKey = $this->secretkey;
      $sResult = '';
      $sData   = $this->decode_base64($sData);

      for($i = 0; $i < strlen($sData); $i ++){
         $sChar    = substr($sData, $i, 1);
         $sKeyChar = substr($sKey, ($i % strlen($sKey)) - 1, 1);
         $sChar    = chr(ord($sChar) - ord($sKeyChar));
         $sResult .= $sChar;
      }

      return $sResult;
   }

   function encode_base64($sData){
      $sBase64 = base64_encode($sData);
      return strtr($sBase64, '+/', '-_');
   }

   function decode_base64($sData){
      $sBase64 = strtr($sData, '-_', '+/');
      return base64_decode($sBase64);
   }

   /**
    * Method to send the Co-Worker invitation email
    * @param $idcoworker -- int
    */
   function sendCoWorkerInvitationEmail($idcoworker){
        $email_template = new EmailTemplate("ofuz_coworker_add_notification");
        $email_template->setSenderName($_SESSION['do_User']->getFullName());
        $email_template->setSenderEmail($_SESSION['do_User']->email);
        $coworker_url = $GLOBALS['cfg_ofuz_site_http_base'].'co_workers.php';
        $email_data = Array('coworker-name' => $_SESSION['do_User']->getFullName($idcoworker),
                                    'sender-name' => $_SESSION['do_User']->getFullName(),
                                    'coworker_url' => $coworker_url
                                    );
        $emailer = new Radria_Emailer();
        $emailer->setEmailTemplate($email_template);
        $emailer->mergeArray($email_data);
        $emailer->addTo($_SESSION['do_User']->getEmailId($idcoworker));
        $emailer->send(); 
   }

    
   /**
     * Method to send email on accept/reject invitation
     * @param $idcoworker -- int
     * @param $accepted -- string
   */
   function sendAcceptRejectNotificationEmail($idcoworker,$accepted = "Yes"){
        if($accepted == "No"){
              $email_template = new EmailTemplate("ofuz_coworker_reject_invitation");
        }else{
            $email_template = new EmailTemplate("ofuz_coworker_accept_invitation");
        }
        $coworker_url = $GLOBALS['cfg_ofuz_site_http_base'].'co_workers.php';
        $email_data = Array('name' => $_SESSION['do_User']->getFullName($idcoworker),
                                    'coworker_name' => $_SESSION['do_User']->getFullName()
                                    );
        $emailer = new Radria_Emailer();
        $emailer->setEmailTemplate($email_template);
        $emailer->mergeArray($email_data);
        $emailer->addTo($_SESSION['do_User']->getEmailId($idcoworker));
        $emailer->send(); 
   }

    
   /**
     * Method to send email after user registers using the invitation link
     * @param $idsender -- int
     * @param $User -- User object
    */ 

    function sendEmailOnCoWorkerRegistration($idsender,$User){
        $do_sender_info = new User();
        $do_sender_info->getId($idsender);
        
        $email_template = new EmailTemplate("ofuz_coworker_registered_notification");
        $email_data = Array('name' => $do_sender_info->getFullName($idsender),
                                            'coworker_name' => $User->getFullName()
                                            );
        
        $emailer = new Radria_Emailer();
        $emailer->setEmailTemplate($email_template);
        $emailer->mergeArray($email_data);
        $emailer->addTo($do_sender_info->email);
        $emailer->send(); 
    }

   /**
     * Function to generate the add Co-Worker form for opensource version
     * @return string form
   */
   function generateFromAddCoWorkerOS(){
      $nextPage = $_SERVER['PHP_SELF'];
      $this->setRegistry("ofuz_add_coworker_os"); 
      $co_worker_form =  $this->prepareSavedForm("ofuz_add_coworker_os");	 
      $co_worker_form->setFormEvent($this->getObjectName()."->eventAddCoWorkerOS",2000);
      $co_worker_form->addEventAction("User->eventRegistrationValidation", 101) ;
      $co_worker_form->addParam("goto",$nextPage);
      $co_worker_form->addParam("errPage", $nextPage);
      $co_worker_form->setForm();
      return $co_worker_form->executeToString();
   }


  
   /**
     * Event Method to add Co Worker as a new user and associate the 
     * user as Co-Worker relation 
    */
   function eventAddCoWorkerOS(EventControler $evtcl) {
    $fields = $evtcl->fields;
    $errorpage = $evtcl->errPage ;
    if($evtcl->validation_fail == 'No'){ // See eventRegistrationValidation() RegisteredUser.class
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from user where email = '".$fields["email"]."'");
        
        $q1 = new sqlQuery($this->getDbCon());
        $q1->query("select * from user where username = '".$fields["username"]."'");

        if ($q1->getNumRows() > 0) {
          $msg = "Username is already in use";
          //$errorpage = $evtcl->errorpage;
          $dispError = new Display($errorpage) ;
          $dispError->addParam("message", $msg) ;
          $evtcl->setDisplayNext($dispError) ;
        }elseif($q->getNumRows() > 0){
          $msg = "Email  already in use";
          //$errorpage = $evtcl->errorpage;
          $dispError = new Display($errorpage) ;
          $dispError->addParam("message", $msg) ;
          $evtcl->setDisplayNext($dispError) ;
        }else{
          // Add the new user
          $do_user_add = new User();
          $do_user_add->addNew();
          $do_user_add->firstname = $fields["firstname"];
          $do_user_add->lastname = $fields["lastname"];
          $do_user_add->email = $fields["email"];
          $do_user_add->username = $fields["username"];
          $do_user_add->password = $fields["password"];
          $do_user_add->status = "active";
          $do_user_add->add();
          $last_id = $do_user_add->getInsertId();
          //echo $last_id ;exit;
          $co_contact_view = new ContactView();
          $co_contact_view->rebuildContactUserTable($last_id);
          $this->addToCoWorkerRel($_SESSION['do_User']->iduser,$last_id);
          $this->addToCoWorkerRel($last_id,$_SESSION['do_User']->iduser);
          $this->addToAllCoWorker($last_id);

          $msg = $do_user_add->firstname." is now one of your Co-Workers";
          $dispError = new Display($errorpage) ;
          $dispError->addParam("message", $msg) ;
          $evtcl->setDisplayNext($dispError) ;

	    }
	}
    }
    
    
    /**
      * Method to add a Co-Worker to all the existing Co-Workers
      * @param $new_coworker -- int
    */
    function addToAllCoWorker($new_coworker){
	    $q = new sqlQuery($this->getDbCon());
	    $q->query("select * from ".$this->table." where iduser = ".$_SESSION['do_User']->iduser." AND idcoworker <> ".$new_coworker);
	    if($q->getNumRows()){
        while($q->fetch()){
              $this->addToCoWorkerRel($q->getData("idcoworker"),$new_coworker);
              $this->addToCoWorkerRel($new_coworker,$q->getData("idcoworker"));
        }
	    }
    }


    

}
