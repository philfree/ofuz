<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

    /**
     * Contact class
     * Using the DataObject
     */
   
class ContactSharing extends DataObject {
    
    public $table = "contact_sharing";
    protected $primary_key = "idcontact_sharing";
   

    /**
      * Event method sharing the contacts with the Co-Workers
      * @param object $evtcl
    */
    function eventShareContactsMultiple(EventControler $evtcl) {
      $contacts = $evtcl->getParam("idcontacts");
      $co_workers = $evtcl->getParam("cwid");      
      $count = 0;
      $no_coworker = 0;

      if (is_array($contacts) && is_array($co_workers)){
          $do_tag = new Tag();
          foreach($co_workers as $co){
             foreach($contacts as $cont){
                 if(!$this->checkCoWorkerContactRel($cont,$co)){
                   $this->addContactSharings($cont,$co);
                   $do_tag->addTagOnContactSharing($cont,$co);
                   $count++;
                 }
             }
          }
       }
       if($count){
          $msg = 'Sharing Updated succesfully';
       }else{
          $msg = 'No Data updated,you may be trying to duplicate some contact access';
       }

       $goto = $evtcl->goto;
       $dispError = new Display($goto) ;
       $dispError->addParam("message", $msg) ;
       $evtcl->setDisplayNext($dispError) ;
    }


    /**
     * Method checking if a contact is already being shared with a specific user.
     * @param integer $idcontact 
     * @param interger $idcoworker
    */
    function checkCoWorkerContactRel($idcontact,$idcoworker){
      $q = new sqlQuery($this->getDbCon()) ;
      $q->query("select * from ".$this->table." where idcontact = ".$idcontact. " AND idcoworker = ".$idcoworker." AND iduser= ".$_SESSION['do_User']->iduser);
      if ($q->getNumRows() > 0){
        return true;
      }else{
        return false;
      }
    }


    /**
      * Method	adding contact sharing
      * @param integer $idcontact
      * @param integer $idcoworker
    */
    function addContactSharings($idcontact,$idcoworker){
       $q = new sqlQuery($this->getDbCon()) ;
       $iduser = $_SESSION['do_User']->iduser;
       $ins_qry = "INSERT INTO ".$this->table. "(idcontact,idcoworker,iduser) VALUES (
                  '$idcontact','$idcoworker','$iduser')";

        $q->query($ins_qry);

        $do_contact = new Contact();
        $do_contact->getId($idcontact);
        $do_contact_view = new ContactView();
        $do_contact_view->setUser($idcoworker);
        $do_contact_view->addFromContact($do_contact);
    }

    function countSharedContacts($idcoworker){
      $q = new sqlQuery($this->getDbCon()) ;
      $q->query("select * from ".$this->table." Inner Join contact on contact.idcontact = contact_sharing.idcontact Where contact_sharing.iduser = ".$_SESSION['do_User']->iduser." AND contact_sharing.idcoworker =".$idcoworker);
      return $q->getNumRows();
    }

    function countSharedContactsByCoWorker($idcoworker){
      $q = new sqlQuery($this->getDbCon()) ;
      $q->query("select * from ".$this->table." Inner Join contact on contact.idcontact = contact_sharing.idcontact Where contact_sharing.iduser = ".$idcoworker." AND contact_sharing.idcoworker =".$_SESSION['do_User']->iduser);
      return $q->getNumRows();
    }
    
    function getSharedContacts($idcoworker){
        $q = new sqlQuery($this->getDbCon()) ;
        $q->query("select * from ".$this->table." Where iduser = ".$_SESSION['do_User']->iduser." AND idcoworker =".$idcoworker);
        if($q->getNumRows()){
            $data = array();
            while($q->fetch()){
              $data[] = $q->getData("idcontact");
            } 
        return $data;
        }else{ return false;}
    }
	
    /**
      *  getCoWorkerByContact()
      *  Return the list of coworkers for a contact.
      *  @param  interger idcontact id of the contact
      *  @param  interfer $iduser
      *  @return array of coworkers
      */
    function getCoWorkerByContact($idcontact) {
        $do_contact = new Contact();
       // $iduser = $do_contact->getIdUser($idcontact);
        $q = new sqlQuery($this->getDbCon()) ;
        //$q->query("select idcoworker from ".$this->table." where idcontact = ".$idcontact." and iduser={$iduser}");
        $q->query("select * from ".$this->table. " where idcontact = ".$idcontact);
        if($q->getNumRows()){
            $data = array();
            while($q->fetch()){
              if($_SESSION['do_User']->iduser !=  $q->getData("idcoworker") )
                  $data[] = $q->getData("idcoworker");
              if($_SESSION['do_User']->iduser !=  $q->getData("iduser") )
                  $data[] = $q->getData("iduser");
            } 
        return array_unique($data);
        }else{ return false;}		
    }
	


    /**
      * Function to get all the users who are sharing the contact
      * @param integer $idcontact
    */
    function selectAllUsersFromContactSharing($idcontact){
        $q = new sqlQuery($this->getDbCon()) ;
        $q->query("select * from ".$this->table." where idcontact = ".$idcontact);
        if($q->getNumRows()){
            $data = array();
            while($q->fetch()){
              $data[] = $q->getData("idcoworker");
              $data[] = $q->getData("iduser");
            } 
        return array_unique($data);
        }else{ return false;}
    }
  
    /**
      * Function get shred contacts by coworker
      * @param integer $idcoworker
    */
    function getSharedContactsByCoWorker($idcoworker){
        $q = new sqlQuery($this->getDbCon()) ;
        $q->query("select * from ".$this->table." Where iduser = ".$idcoworker." AND idcoworker =".$_SESSION['do_User']->iduser);
        if($q->getNumRows()){
            $data = array();
            while($q->fetch()){
              $data[] = $q->getData("idcontact");
            }
        return $data;
        }else{ return false;}
    }


    /**
      * Function to unshare a contact
      * @param integer $idcontact
      * @param interger $idcoworker
    */
    function unshareContact($idcontact,$idcoworker){
        $q = new sqlQuery($this->getDbCon()) ;
        $q->query("delete from ".$this->table." where idcontact = ".$idcontact." AND idcoworker = ".$idcoworker." AND iduser =".$_SESSION['do_User']->iduser." LIMIT 1");

        $do_contact_view = new ContactView();
        $do_contact_view->setUser($idcoworker);
        $do_contact_view->deleteFromContact($idcontact);
    }

    /**
      * Function to update the contact tables for the co-workers at the time of contact merging if the contacts are shared
      * @param integer $id_to_keep 
      * @param integer $contact_id_del
    */
    function resetContactSharingOnMerging($id_to_keep,$contact_id_del){
        $q = new sqlQuery($this->getDbCon()) ;
        $q_update = new sqlQuery($this->getDbCon()) ;
        $q->query("select * from ".$this->table." where iduser = ".$_SESSION['do_User']->iduser. " AND idcontact = ".$contact_id_del);
        if($q->getNumRows() > 0 ){
            while($q->fetch()){
                $q_update->query("update ".$this->table." set idcontact = ".$id_to_keep." where idcontact_sharing   = ".$q->getData("idcontact_sharing"). " Limit 1");
                $co_worker_view = "userid".$q->getData("idcoworker")."_contact";
                $q_update->query("update ".$co_worker_view." set idcontact = ".$id_to_keep." where idcontact   = ".$contact_id_del);
            }
        }
    }

   
}
