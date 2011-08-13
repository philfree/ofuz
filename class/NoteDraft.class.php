<?php
/**COPYRIGHTS**/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/**COPYRIGHTS**/

class NoteDraft extends DataObject {

    public $table = 'note_draft';
    protected $primary_key = 'idnote_draft';

    /*
      Get the Draft with the entity id and the type
    */
    function getDraft($id,$type){
      $q = new sqlQuery($this->getDbCon());
      $q->query("select * from ".$this->table. " Where id= ".$id." AND id_type = '".$type."' 
                AND iduser = ".$_SESSION['do_User']->iduser );
      //echo "select * from ".$this->table. " Where id= ".$id." AND type = '".$type."' 
        //        AND iduser = ".$_SESSION['do_User']->iduser;
      if($q->getNumRows()){
          $data = array();
            while($q->fetch()){
                $data["id"] = $q->getData("id");
                $data["idnote_draft"] = $q->getData("idnote_draft");
                $data["note_content"] = htmlentities($q->getData("note_content"));
                $data["timestamp"] = $q->getData("timestamp");
            }
        return $data;
      }else{
        return false;
      }
    }

    /*
      Add or update a content as draft. If there is no draft added with the entity then add a new
      else update the existing one.
    */
    function eventAddUpdateDraft(EventControler $evtcl){
      $id = $evtcl->id;
      $type = $evtcl->id_type;
      $text = $evtcl->text;
      if($text !=''){
          $do_note = new Note();
          $text = $do_note->htmlCleanUp($text);
          $q = new sqlQuery($this->getDbCon());
          $q->query("select * from ".$this->table. " Where id= ".$id." AND id_type = '".$type."' 
                AND iduser = ".$_SESSION['do_User']->iduser );

          if($q->getNumRows()){
              // Update
              while($q->fetch()){
                  $idnote_draft = $q->getData("idnote_draft");
              }
              $this->getId($idnote_draft);
              $this->note_content = $text;
              $this->timestamp = time();
              $this->update();
          }else{
            //Insert
              $this->id = $id;
              $this->id_type = $type;
              $this->iduser = $_SESSION['do_User']->iduser;
              $this->note_content = $text;
              $this->timestamp = time();
              $this->add();
          }
        }
     }

    /*
      Check if a draft exists for an entity and the type. If it exists then 
      return the draft id else return false.
    */
      
    function isDraftExist($id,$type){
        $q = new sqlQuery($this->getDbCon());
        $q->query("select * from ".$this->table. " Where id= ".$id." AND id_type = '".$type."' 
                AND iduser = ".$_SESSION['do_User']->iduser );

        if($q->getNumRows()){
          $q->fetch();
          return $q->getData("idnote_draft");
        }else{
          return false;
        }
    }

    /*
      Event method to delete a draft. Is triggered during the draft discard or when the opreation on 
      the entity is done and there is an existing draft associated.
    */
    function eventDeleteDraft(EventControler $evtcl){
        $id = $evtcl->id;
        if($id){
            $q = new sqlQuery($this->getDbCon());
            $q->query("delete from ".$this->table." where idnote_draft = ".$id ." limit 1"); 
        }
    }

    function deleteDraftWithType($id,$type){
        $q = new sqlQuery($this->getDbCon());
        $q->query("delete from ".$this->table." where id = ".$id ." and id_type = '".$type."' limit 1"); 
    }

}
?>