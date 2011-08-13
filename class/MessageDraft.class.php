<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

class MessageDraft extends DataObject {

    public $table = 'message_draft';
    protected $primary_key = 'idmessage_draft';

    /*
      Get the Draft with the userid and the type
    */
    function getDraft($type = "contact_email"){
      $q = new sqlQuery($this->getDbCon());
      $q->query("select * from ".$this->table. " Where type = '".$type."' 
                AND iduser = ".$_SESSION['do_User']->iduser );
      if($q->getNumRows()){
          $data = array();
            while($q->fetch()){
                $data["idmessage_draft"] = $q->getData("idmessage_draft");
                $data["message_subject"] = $q->getData("message_subject");
                $data["message_content"] = htmlentities($q->getData("message_content"));
                $data["timestamp"] = $q->getData("timestamp");
            }
        return $data;
      }else{
        return false;
      }
    }

    /*
      Add or update a content as draft. If there is no draft added with the user then add a new
      else update the existing one.
    */
    function eventAddUpdateDraft(EventControler $evtcl){
      $type = $evtcl->type;
      $text = $evtcl->text;
      $sub = $evtcl->sub;
      if($text !=''){
          $do_note = new Note();
          $text = $do_note->htmlCleanUp($text);
          $q = new sqlQuery($this->getDbCon());
          $q->query("select * from ".$this->table. " Where type = '".$type."' 
                AND iduser = ".$_SESSION['do_User']->iduser );

          if($q->getNumRows()){
              // Update
              while($q->fetch()){
                  $idmessage_draft = $q->getData("idmessage_draft");
              }
              $this->getId($idmessage_draft);
              $this->message_content = $text;
              $this->message_subject = $sub;
              $this->timestamp = time();
              $this->update();
          }else{
            //Insert
              $this->type = $type;
              $this->iduser = $_SESSION['do_User']->iduser;
              $this->message_content = $text;
              $this->message_subject = $sub;
              $this->timestamp = time();
              $this->add();
          }
        }
     }

    /*
      Check if a draft exists for an entity and the type. If it exists then 
      return the draft id else return false.
    */
      
    function isDraftExist($type="contact_email"){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from ".$this->table. " Where type = '".$type."' 
                AND iduser = ".$_SESSION['do_User']->iduser );

        if($q->getNumRows()){
          $q->fetch();
          return $q->getData("idmessage_draft");
        }else{
          return false;
        }
    }

    /*
      Event method to delete a saved draft of a given type for the user
    */
    function eventDeleteSavedDraft(EventControler $evtcl){
        $type = $evtcl->type;
        $q = new sqlQuery($this->getDbCon());
        $q->query("delete from ".$this->table." where type = '".$type ."' AND iduser = ".$_SESSION['do_User']->iduser); 
    }

    /*
      Event method to delete a draft. Is triggered during the draft discard or when the opreation on 
      the entity is done and there is an existing draft associated.
    */
    function eventDeleteDraft(EventControler $evtcl){
        $id = $evtcl->id;
        $this->deleteDraft($id);
    }

    /*
      Method to delete a draft
    */

    function deleteDraft($id){
        if($id){
            $q = new sqlQuery($this->getDbCon());
            $q->query("delete from ".$this->table." where idmessage_draft = ".$id ." limit 1"); 
        }
    }

}
?>