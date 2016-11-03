<?php 
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 
// Copyright 2008 - 2010 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/

  /**
   * Class Note
   * This is the base Class to manage the note and the project discuss.
   * As well as the display note formating.
   * It contains the Undo delete note feature.
   *
   */

class Note extends DataObject {
    
    public $table = "";
    protected $primary_key = "";
    public $note_array = array();
    public $delete_note_array = array();
    public $is_truncated = false;


    /**
      * function to check if the delete is permitted.
      * @param string $context
      * @param integer $id
      * NOTE : $context is not used in this version but will keep it for future for other entitly deletes
    */
    function isdeletePermitted($id,$context="ContactNote"){
        $this->getId($id);
        if($this->iduser != $_SESSION['do_User']->iduser){
          return false ;
        }else{
          return true ;
        }
    }
    

    /**
       Event Method to delete a note from the db and keep the data in the session for undo
       calls to member method deleteNote()
       @param Intinger id : id of the note
       @param String context : can be now ContactNote or ProjectDiscuss
    */
    function eventTempDelNoteById(EventControler $evtcl){
       $idnote = $evtcl->id;
       $context = $evtcl->context;
       if($this->isdeletePermitted($idnote,$context) === true ){
          if(!is_object($_SESSION['NoteDeleted'])){
              $this->sessionPersistent("NoteDeleted", "logout.php", OFUZ_TTL);
          }
          $this->deleteNote($idnote,$context);
       }else{
          $_SESSION['errorMessage'] = _('Opps !! Looks like you are trying to delete a record which does not belong to you.');
          $evtcl->setDisplayNext(new Display("error.php"));
       }

    }
    
    /**
      Event Method undeleting a note. Calls member method undoDeletedNotes()
      @param Intinger id : id of the note
      @param String context : can be now ContactNote or ProjectDiscuss
      @param Intinger key_val : array key of the deleted note array
    */    

    function eventUndoNoteById(EventControler $evtcl){
      $idnote = $evtcl->id;
      $key = $evtcl->key_val;
      $context = $evtcl->context;
      $this->undoDeletedNotes($key,$context);
    }   
  
    /**
        Method undelete a note. Calls member nethod getDeletedNoteByArrayKey()
        & unsetNoteByKey()
        @param Intinger key_val : array key of the deleted note array
        @param String context : can be now ContactNote or ProjectDiscuss
    */
    function undoDeletedNotes($arr_key,$context = "ContactNote"){
       switch($context){
            case "ContactNote" : 
                $data = $this->getDeletedNoteByArrayKey($arr_key);
                $this->addNew();
                $this->idcontact = $data["idcontact"];
                $this->note = $data["note"];
                $this->date_added = $data["date_added"];
                $this->document = $data["document"];
                $this->idcompany = $data["idcompany"];
                $this->iduser = $data["iduser"];
                $this->priority = $data["priority"];
                $this->send_email = $data["send_email"];
                $this->hours_work = $data["hours_work"];
                $this->type = $data["type"];
                $this->add();
                $this->unsetNoteByKey($arr_key);
                break;

            case "ProjectDiscuss":
                  $data = $this->getDeletedNoteByArrayKey($arr_key);
                  $this->addNew();
                  $this->idproject_task = $data["idproject_task"];
                  $this->idtask = $data["idtask"];
                  $this->idproject = $data["idproject"];
                  $this->discuss = $data["discuss"];
                  $this->date_added = $data["date_added"];
                  $this->document = $data["document"];
                  $this->iduser = $data["iduser"];
                  $this->drop_box_sender = $data["drop_box_sender"];
                  $this->priority = $data["priority"];
                  $this->hours_work = $data["hours_work"];
                  $this->type = $data["type"];
                  $this->add();
                  $this->unsetNoteByKey($arr_key);
                  break;
       }
    }

  /**
    Method deleting note
    @param Intinger id : id of the note
    @param String context : can be now ContactNote or ProjectDiscuss
    After delete keep the deleted data in the delete_note_array
  */
  function deleteNote($id,$context="ContactNote"){
         switch($context){
            case "ContactNote" :
                  $this->getId($id);
                  //$obj = $this->getId($id);
                  //$obj1 = serialize($this);
                  //print_r($obj1);exit;
                 // print_r(unserialize($obj1));exit;
                  $note_array["idcontact_note"] = $id;
                  $note_array["idcontact"] = $this->idcontact;
                  $note_array["note"] = $this->note;
                  $note_array["date_added"] = $this->date_added;
                  $note_array["document"] = $this->document;
                  $note_array["idcompany"] = $this->idcompany;
                  $note_array["iduser"] = $this->iduser;
                  $note_array["priority"] = $this->priority;
                  $note_array["send_email"] = $this->send_email;
                  $note_array["hours_work"] = $this->hours_work;
                  $note_array["type"] = $this->type;
                  $_SESSION['NoteDeleted']->delete_note_array[] = $note_array;
                  //$this->delete();
				  $this->deleteContactNotes($id);
                  break;
            case "ProjectDiscuss":
                 $this->getId($id);
                 $note_array["idproject_task"] = $this->idproject_task;
                 $note_array["idtask"] = $this->idtask;
                 $note_array["idproject"] = $this->idproject;
                 $note_array["discuss"] = $this->discuss;
                 $note_array["date_added"] = $this->date_added;
                 $note_array["document"] = $this->document;
                 $note_array["iduser"] = $this->iduser;
                 $note_array["drop_box_sender"] = $this->drop_box_sender;
                 $note_array["priority"] = $this->priority;
                 $note_array["hours_work"] = $this->hours_work;
                 $this->type = $data["type"];
                 $_SESSION['NoteDeleted']->delete_note_array[] = $note_array;
                 //$this->delete();

				 $this->deleteProjectDiscussionNote($id);
                 break;
         } 
    }

    /**
      Function to get the deleted note from the array delete_note_array
      @param Intinger ref_primary_key : idcontact,idcompany,idproject_discuss
      @param String context : can be now ContactNote or ProjectDiscuss
      @param String lookfor : contact,company,project_discuss
      calls member method parseDeleteArray()
    */
    function getNotesDataFromDeleted($ref_primary_key,$lookfor,$context="ContactNote"){
         if(is_array($_SESSION['NoteDeleted']->delete_note_array) && count($_SESSION['NoteDeleted']->delete_note_array)>0){
            return $this->parseDeleteArray( $_SESSION['NoteDeleted']->delete_note_array,$ref_primary_key,$lookfor,$context);
         }else{ return false; }
    }
   
    /**
      Method to parse the array delete_note_array 
      returns array of data
      @param Array array : delete_note_array in the session object
      @param Intinger ref_primary_key : idcontact,idcompany,idtask
      @param String context : can be now ContactNote or ProjectDiscuss
      @param String lookfor : contact,company,project_discuss
    */
    function parseDeleteArray($array,$ref_primary_key,$lookfor,$context="ContactNote"){
         $note_array = array();
         $data = array();
         switch($context){
            case "ContactNote" :
                 foreach($array as $key=>$notes){
                    if($notes["idcontact"] == $ref_primary_key && $lookfor == "contact"){
                        $note_array["idcontact_note"] = $notes["idcontact_note"] ;
                        $note_array["idcontact"] = $notes["idcontact"] ;
                        $note_array["note"] = $notes["note"] ;
                        $note_array["date_added"] = $notes["date_added"] ;
                        $note_array["document"] = $notes["document"] ;
                        $note_array["idcompany"] = $notes["idcompany"] ;
                        $note_array["iduser"] = $notes["iduser"] ;
                        $note_array["priority"] = $notes["priority"] ;
                        $note_array["send_email"] = $notes["idcontact_note"] ;
                        $note_array["hours_work"] = $notes["hours_work"] ;
                        $note_array["type"] = $notes["type"] ;
                        $note_array["key_val"] = $key ;
                        $data[] = $note_array;
                     }elseif($notes["idcompany"] == $ref_primary_key && $lookfor == "company"){
                        $note_array["idcontact_note"] = $notes["idcontact_note"] ;
                        $note_array["idcontact"] = $notes["idcontact"] ;
                        $note_array["note"] = $notes["note"] ;
                        $note_array["date_added"] = $notes["date_added"] ;
                        $note_array["document"] = $notes["document"] ;
                        $note_array["idcompany"] = $notes["idcompany"] ;
                        $note_array["iduser"] = $notes["iduser"] ;
                        $note_array["priority"] = $notes["priority"] ;
                        $note_array["send_email"] = $notes["idcontact_note"] ;
                        $note_array["hours_work"] = $notes["hours_work"] ;
                        $note_array["key_val"] = $key ;
                        $data[] = $note_array;
                     }
                  }
                  return $data;
                  break; 
               case "ProjectDiscuss" :
                    foreach($array as $key=>$notes){
                        if($notes["idproject_task"] == $ref_primary_key && $lookfor == "project_discuss"){
                            $note_array["idproject_task"] = $notes["idproject_task"] ;
                            $note_array["idtask"] = $notes["idtask"] ;
                            $note_array["idproject"] = $notes["idproject"] ;
                            $note_array["discuss"] = $notes["discuss"] ;
                            $note_array["date_added"] = $notes["date_added"] ;
                            $note_array["document"] = $notes["document"] ;
                            $note_array["iduser"] = $notes["iduser"] ;
                            $note_array["drop_box_sender"] = $notes["drop_box_sender"] ;
                            $note_array["priority"] = $notes["priority"] ; 
                            $note_array["hours_work"] = $notes["hours_work"] ;
                            $this->type = $data["type"];
                            $note_array["key_val"] = $key ;
                            $data[] = $note_array;
                        }
                    }
                  return $data;
                  break;
             }
     }

     /**
        function to get the array data from delete_note_array in session object
        @param Intiger key : array key of delete_note_array
     */
     function getDeletedNoteByArrayKey($key){
        if($key == 'No'){ $key = 0; }
        return $_SESSION['NoteDeleted']->delete_note_array[$key];
     }

     /**
        Function to unset the array element for the supplied key
        @param Intiger key : array key of delete_note_array
     */
     function unsetNoteByKey($key){
        if($key == 'No'){ $key = 0; }
        unset($_SESSION['NoteDeleted']->delete_note_array[$key]);
     }
    
     function viewDeletedNote($array,$context="ContactNote"){
          $html = '<div class="dottedline"></div><br />';
          $e_note_undo = new Event($this->getObjectName()."->eventUndoNoteById");
          foreach($array as $deleted_note){
              if($context == "ProjectDiscuss"){
                  $note_text = '<b><i>'.substr($deleted_note["discuss"], 0, 100).' ...</b></i> '._('has been removed');
                  $e_note_undo->addParam("id", $deleted_note["idproject_task"]);
                  $e_note_undo->addParam("context", "ProjectDiscuss");
              }elseif($context == "ContactNote" ){
                  $note_text = '<b><i>'.substr($deleted_note["note"], 0, 100).' ...</b></i> '._('has been removed');
                  $e_note_undo->addParam("id", $deleted_note["idcontact_note"]);
                  $e_note_undo->addParam("context", "ContactNote");
              }
              if($deleted_note["key_val"] == 0 ){$key = "No";}else{ $key = $deleted_note["key_val"]; } 
              $e_note_undo->addParam("key_val", $key);
              $html .= '<div id="note_ctlbar">';
              $html .= '<div class="note_ctlbar_text">';
              $html .= $note_text;
              $html .= '</div>';
              $html .= '<div class="note_undo">'. $e_note_undo->getLink('Undo').'</div>';
              $html .= ' <div class="spacerblock_2"></div></div>';
           }
           return $html;
     }

	 /**
	  *  Format the display of the notes
	  *  add nl2br and change the urls into links
	  *  @param text of the note to format
	  *  @return formated text note for html display
	  */
     function formatNoteDisplayShort($text='', $num_char=500){
       if (empty($text)) { $text = $this->note; }
        if (strlen($text) > $num_char) {
            $text = substr($text, 0, $num_char);
            $this->is_truncated = true;
        } else { 
            $this->is_truncated = false;
        }
        $ret = ' ' . $text;
        $ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", "'\\1<a href=\"\\2\" target = \"_blank\">\\2</a>'", $ret);
        $ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://\\2\" target = \"_blank\">\\2</a>'", $ret);
        $ret = preg_replace("#(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
        $ret = substr($ret, 1);
        $ret = nl2br($ret);
        return ($ret) ;
    }

	 /**
	  * format document link
	  * Create a link for the documents attached to a note.
	  * @return html link to the document
	  */

	 function formatDocumentLink($type="") {
      if($this->document != ''){
      $doc_name = str_replace("  ","%20%20",$this->document);
      $doc_name = str_replace(" ","%20",$doc_name);
      $file_url = "/files/".$doc_name;
      //$file_url = '/files/'.$do_discuss->document;
      //$file = '<br /><a href="'.$file_url.'" target="_blank">'.$this->document.'</a>';
                            
      $file = '<br /><a href="/files_download.php?filename='.$this->document.'" target="_blank">'.$this->document.'</a>';
                            
      return $file;
      } else { return ''; }
	 }

    /**
      Function to delete the attached file from the disk.
      @param String document : file name
      @param String folder : folder name
    */
    function deleteAttachmentFromDisk($document, $folder="files") {
            if($document){
                $doc_path = $folder.'/'.$document;
                if(file_exists($doc_path)){
                    unlink($doc_path);
                }
            }
    }

  //using htmLawed lib, it cleans up the bad HTML, correct the mismatched tags etc.
    function eventHTMLCleanUp(EventControler $event_controler) {
        $fields = $event_controler->fields;
        if($fields['discuss']) {
          $note = $fields['discuss'];
          $from = 'discuss';
        }
        if($fields['note']) {
          $note = $fields['note'];
          $from = 'note';
        }

        $processed = $this->htmlCleanUp($note);
        $fields[$from] = $processed;
        $event_controler->fields = $fields;
    }

    /**
      * Function cleaning the HTML content using htmlLawed
      * @param string $content
      * @return the cleaned content
      * @see class/htmlLawed.php
    */
    function htmlCleanUp($content){
        $output = $content ;
        $regex = "/\<sourcecode(\b[^>]*)\>(.*?)\<\/sourcecode\>/ism";
        $source_code = Array();
        while (preg_match($regex, $output, $match)) {
           // foreach($match[1] as $data){
                 $source_code[md5($match[0])] = Array('language' => $match[1], 'code'=> $match[2]);                
                 $output = str_replace($match[0],md5($match[0]),$output);            
        }    
        $config = array('safe'=>1, 'elements'=>'a, strong, b, i, u, ul, li, strike, pre, code'); 
        $processed = htmLawed($output, $config);
        if (!empty($source_code)) { 
          foreach ($source_code as $key_hash => $code_saved) {
            $processed = str_replace($key_hash, '<sourcecode '.$code_saved['language'].'>'.htmlentities($code_saved['code']).'</sourcecode>', $processed);
          }
        }
        
        return $processed;
    }
    

     /**
      *  Format the display of the notes
      *  add nl2br, change the urls into links
      *  and add syntax highlight.
      *  @param text of the note to format
      *  @return formated text note for html display
      */
     function formatNoteDisplayFull($text=''){
      if (empty($text)) { $text = $this->note; }
  
      $source_code = Array();
      $regex = "/\<sourcecode(\b[^>]*)\>(.*?)\<\/sourcecode\>/ism";
      $source_code = Array();
      while (preg_match($regex, $text, $match)) {
                 $source_code[md5($match[0])] = Array('language' => $match[1], 'code'=> html_entity_decode($match[2]));                
                 $text = str_replace($match[0],md5($match[0]),$text);            
      }       
      $ret = ' ' . $text;
      $ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", "'\\1<a href=\"\\2\" target = \"_blank\">\\2</a>'", $ret);
      $ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://\\2\" target = \"_blank\">\\2</a>'", $ret);
      $ret = preg_replace("#(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
      $processed = substr($ret, 1);
      $processed = nl2br($processed);
      if (!empty($source_code)) {
          include_once("geshi.php");
          foreach ($source_code as $key_hash => $code_saved) {
              if (strlen(trim($code_saved['language'])) > 0) { 
                $highlight = new GeSHi($code_saved['code'], trim($code_saved['language']));
                $processed = str_replace($key_hash, '<sourcecode '.trim($code_saved['language']).'>'.$highlight->parse_code().'</sourcecode>', $processed);  
              } else {
                $processed = str_replace($key_hash, '<sourcecode>'.nl2br(htmlentities($code_saved['code'])).'</sourcecode>', $processed);  
              }
          }
      }
      return ($processed) ;
    }
    
 
}
?>
