<?php
// Copyright 2008 - 2011 all rights reserved, SQLFusion LLC, info@sqlfusion.com
/** Ofuz Open Source version is released under the GNU Affero General Public License, please read the full license at: http://www.gnu.org/licenses/agpl-3.0.html **/ 


    /**
      * Ofuz LeanKit Kanban API integration Utils class
      *
      * @author SQLFusion
      * @version 0.7
      * @date 2012-06-27
      * @see LeanKitKanban.class.php      
      */
   
class OfuzLeanKitKanban extends DataObject {
    
  public $table = "leankit_credentials";
  protected $primary_key = "idleankit_credentials";

 /**
  * An Ajax Event 
  * Records User's LeanKit Kanban login credentials.
  * @param object : EventControler
  * @return int : last inserted id (primary_key)
  */
  public function eventAjaxAddLoginCredentials(EventControler $evtcl) {

    $msg = "";

    //Check if User already set up his/her LeanKit Kanban login credentials.
    if($this->checkIfUserCredentialsExist($evtcl->un, $evtcl->pwd)) {

      $sql = "UPDATE leankit_credentials
	      SET `username` = '".$evtcl->un."', `password` = '".$evtcl->pwd."'
              WHERE iduser = ".$_SESSION['do_User']->iduser;
      $this->query($sql);
      $msg = "Your Login credentials have been successfully updated.";

    } else {

      $sql = "INSERT INTO leankit_credentials(`iduser`,`username`,`password`)
	      VALUES(".$_SESSION['do_User']->iduser.",'".$evtcl->un."','".$evtcl->pwd."')
	    ";
      $this->query($sql);
      $msg = "Your Login credentials have been successfully set up.";

    }

    if($msg == "") {
      $msg = "Database error! Please try again.";
    }

    echo $msg;

  }

  /**
   * Checks if User has already set up his/her LeanKit Kanban login Credentials.
   * @param string
   * @param string
   * @return boolean
   */
  public function checkIfUserCredentialsExist($username, $password) {

    $sql = "SELECT idleankit_credentials
            FROM leankit_credentials
            WHERE iduser = ".$_SESSION['do_User']->iduser;
    $this->query($sql);
    if($this->getNumRows()) {
      return true;
    } else {
      return false;
    }

  }

  /**
   * Gets User's LeanKit Kanban login details
   * @return object
   */
  public function getUserLoginCredentials() {
    $sql = "SELECT * 
	    FROM leankit_credentials
	    WHERE iduser = ".$_SESSION['do_User']->iduser."
	  ";
    $this->query($sql);
    $this->getValues();
  }

  /**
   * Creates a new Card in the Kanban Board.
   * This method has all the parameters specifying the board, lane and position where you want to add the new card. 
     The request body contains the JSON for a new card. You must specify a valid TypeId that matches one of the Card Types 
     for your board. See GetBoardIdentifiers for a listing of valid Card Type Ids and Class Of Service Ids for your board.
   * @param Object : EventControler
   * @see LeanKitKanban->addCard
   */
  public function eventAddTaskToBoard(EventControler $evtcl) {
    $msg = "";

    $this->getUserLoginCredentials();

    if($this->getNumRows()) {
      $username = $this->username;
      $password = $this->password;

      if($evtcl->board) {     
	//This is the default Type used for adding a Card.
	$card_type_id = $this->getCardTypeId($evtcl->board, "Task");
	//This is the default Board User for adding a Card.
	$assigned_user_id = $this->getBoardUserId($evtcl->board, $username);
	//This is the default Lane used for adding a Card.
	$lane_id = $this->getCardLaneId($evtcl->board, "Backlog");
	
	$task = new Task();
	$task->getId($evtcl->ofuz_task_id);

	if($task->due_date_dateformat == "" || $task->due_date_dateformat == "0000-00-00") {
	  $due_date = "";
	} else {
	  $due_date = $this->convertMysqlDateToMMDDYYY($task->due_date_dateformat, "/");
	}

	$array_card = array(
	    "Title" => $task->task_description,
	    "Description" => $task->task_description,
	    "TypeId" => $card_type_id,
	    "Priority" => 1,
	    "Size" => "",
	    "IsBlocked" => false,
	    "BlockReason" => "",
	    "DueDate" => $due_date,
	    "ExternalSystemName" => "http://www.ofuz.net/Task/".$evtcl->ofuz_idprojecttask,
	    "ExternalSystemUrl" => "http://www.ofuz.net/Task/".$evtcl->ofuz_idprojecttask,
	    "Tags" => "",
	    "ClassOfServiceId" => "",
	    "ExternalCardID" => $evtcl->ofuz_idprojecttask,
	    "AssignedUserIds" => array($assigned_user_id)
	);

	$leankitkanban = new LeanKitKanban($username,$password);
	$response = $leankitkanban->addCard($array_card, $evtcl->board, $lane_id, 1);
	if($response->ReplyCode == '201') {
	  $msg .= "The Card is added.";
	} else {
	  $msg .= $response->ReplyText;
	}     
      } else {
	$msg .= "You must select a Kanban Board to add this Task.";
      }
    } else {
      $msg .= "You have not set up your LeanKit Kanban Login Credentials.";
    }

    $_SESSION["ofuz_kanban_message"] = $msg;
  }

  /**
   * Gets the Lane Name from the Board in which Card presents.
   * @param int, int
   * @return string : either message or Lane Name
   */
  public function getCardLaneName($board_id, $lane_id) {
    $msg = "";
    $this->getUserLoginCredentials();

    if($this->getNumRows()) {
      $username = $this->username;
      $password = $this->password;

      $leankitkanban = new LeanKitKanban($username,$password);
      $board = $leankitkanban->getBoardIdentifiers($board_id);
      if(is_object($board)) {
	if($board->ReplyCode == '200') {
	  foreach($board->ReplyData[0]->Lanes as $lane) {
	    if(trim($lane->Id) == trim($lane_id)) {
	      $msg = $lane->Name;
	      break;
	    }
	  }
	} else {
	  $msg = $board->ReplyText;
	}
      } else {
	$msg = "An Error occured while retreiving the Board.";
      }
    } else {
      $msg .= "You have not set up your LeanKit Kanban Login Credentials.";
    }

    return $msg;
  }

  /**
   * Gets the Lane Id from the Board in which Card presents.
   * @param int, string
   * @return string : either message or Lane Id
   */
  public function getCardLaneId($board_id, $lane_name) {
    $msg = "";
    $this->getUserLoginCredentials();

    if($this->getNumRows()) {
      $username = $this->username;
      $password = $this->password;

      $leankitkanban = new LeanKitKanban($username,$password);
      $board = $leankitkanban->getBoardIdentifiers($board_id);
      if(is_object($board)) {
	if($board->ReplyCode == '200') {
	  foreach($board->ReplyData[0]->Lanes as $lane) {
	    if(trim($lane->Name) == trim($lane_name)) {
	      $msg = $lane->Id;
	      break;
	    }
	  }
	} else {
	  $msg = $board->ReplyText;
	}
      } else {
	$msg = "An Error occured while retreiving the Board.";
      }
    } else {
      $msg .= "You have not set up your LeanKit Kanban Login Credentials.";
    }

    return $msg;
  }

  /**
   * Gets the Card Type Id from the Board for the given Type Name
   * @param int, string
   * @return string : either message or Card Type Id
   */
  public function getCardTypeId($board_id, $card_type_name) {
    $msg = "";
    $this->getUserLoginCredentials();

    if($this->getNumRows()) {
      $username = $this->username;
      $password = $this->password;

      $leankitkanban = new LeanKitKanban($username,$password);
      $board = $leankitkanban->getBoardIdentifiers($board_id);
      if(is_object($board)) {
	if($board->ReplyCode == '200') {
	  foreach($board->ReplyData[0]->CardTypes as $card_type) {
	    if(trim($card_type->Name) == trim($card_type_name)) {
	      $msg = $card_type->Id;
	      break;
	    }
	  }
	} else {
	  $msg = $board->ReplyText;
	}
      } else {
	$msg = "An Error occured while retreiving the Board.";
      }
    } else {
      $msg .= "You have not set up your LeanKit Kanban Login Credentials.";
    }

    return $msg;
  }

  /**
   * Gets the User Id from the Board for the given username (email)
   * @param int, string
   * @return string : either message or User Id
   */
  public function getBoardUserId($board_id, $username) {
    $msg = "";

    $this->getUserLoginCredentials();

    if($this->getNumRows()) {
      $username = $this->username;
      $password = $this->password;

      $leankitkanban = new LeanKitKanban($username,$password);
      $board = $leankitkanban->getBoardIdentifiers($board_id);
      if(is_object($board)) {
	if($board->ReplyCode == '200') {
	  foreach($board->ReplyData[0]->BoardUsers as $board_user) {
	    if(trim($board_user->Name) == trim($username)) {
	      $msg = $board_user->Id;
	      break;
	    }
	  }
	} else {
	  $msg = $board->ReplyText;
	}
      } else {
	$msg = "An Error occured while retreiving the Board.";
      }
    } else {
      $msg .= "You have not set up your LeanKit Kanban Login Credentials.";
    }

    return $msg;
  }

  /**
   * Makes the Card "Blocked". (In other words, this method blocks the card)
   * This method takes the card JSON in the request body and updates the card (using the cardId in the card JSON) with 
     the provided values. The cardId is not added to the url...just in the card JSON in the body.
   * @param Object : EventControler
   * @see LeanKitKanban->updateCard
   */
  public function eventBlockTheCard(EventControler $evtcl) {

    $msg = "";

    $this->getUserLoginCredentials();

    if($this->getNumRows()) {
      $username = $this->username;
      $password = $this->password;

      if(trim($evtcl->block_unblock_reason)) {     
	$assigned_user_id = $this->getBoardUserId($evtcl->board_id, $username);
	$array_card = array(
	    "Id" => $evtcl->card_id,
	    "LaneId" => $evtcl->lane_id,
	    "Title" => $evtcl->title,
	    "Description" => $evtcl->description,
	    "TypeId" => $evtcl->type_id,
	    "Priority" => $evtcl->priority,
	    "Size" => $evtcl->size,
	    "AssignedUserId" => $evtcl->assigned_user_id,
	    "IsBlocked" => true,
	    "BlockReason" => $evtcl->block_unblock_reason,
	    "Index" => $evtcl->index,
	    "DueDate" => $evtcl->due_date,
	    "UserWipOverrideComment" => $evtcl->user_wip_override_comment,
	    "ExternalSystemName" => "http://www.ofuz.net/Task/".$evtcl->ofuz_idprojecttask,
	    "ExternalSystemUrl" => "http://www.ofuz.net/Task/".$evtcl->ofuz_idprojecttask,
	    "Tags" => $evtcl->tags,
	    "ClassOfServiceId" => $evtcl->class_of_service_id,
	    "ExternalCardID" => $evtcl->ofuz_idprojecttask,
	    "AssignedUserIds" => array($assigned_user_id)
	);

	$leankitkanban = new LeanKitKanban($username,$password);
	$response = $leankitkanban->updateCard($array_card, $evtcl->board_id);
	if($response->ReplyCode == '202') {
	  $msg .= "The Card is blocked.";
	} else {
	  $msg .= $response->ReplyText;
	}
      } else {
	$msg .= "You must enter the reason to block the card.";
      }
    } else {
      $msg .= "You have not set up your LeanKit Kanban Login Credentials.";
    }

    $_SESSION["ofuz_kanban_message"] = $msg;
  }

  /**
   * Makes the Card "Unblocked". (In other words, this method unblocks the card)
   * This method takes the card JSON in the request body and updates the card (using the cardId in the card JSON) with 
     the provided values. The cardId is not added to the url...just in the card JSON in the body.
   * @param Object : EventControler
   * @see LeanKitKanban->updateCard
   */
  public function eventUnblockTheCard(EventControler $evtcl) {

    $msg = "";

    $this->getUserLoginCredentials();

    if($this->getNumRows()) {
      $username = $this->username;
      $password = $this->password;

      if(trim($evtcl->block_unblock_reason)) {     
	$assigned_user_id = $this->getBoardUserId($evtcl->board_id, $username);
	$array_card = array(
	    "Id" => $evtcl->card_id,
	    "LaneId" => $evtcl->lane_id,
	    "Title" => $evtcl->title,
	    "Description" => $evtcl->description,
	    "TypeId" => $evtcl->type_id,
	    "Priority" => $evtcl->priority,
	    "Size" => $evtcl->size,
	    "AssignedUserId" => $evtcl->assigned_user_id,
	    "IsBlocked" => false,
	    "BlockReason" => $evtcl->block_unblock_reason,
	    "Index" => $evtcl->index,
	    "DueDate" => $evtcl->due_date,
	    "UserWipOverrideComment" => $evtcl->user_wip_override_comment,
	    "ExternalSystemName" => "http://www.ofuz.net/Task/".$evtcl->ofuz_idprojecttask,
	    "ExternalSystemUrl" => "http://www.ofuz.net/Task/".$evtcl->ofuz_idprojecttask,
	    "Tags" => $evtcl->tags,
	    "ClassOfServiceId" => $evtcl->class_of_service_id,
	    "ExternalCardID" => $evtcl->ofuz_idprojecttask,
	    "AssignedUserIds" => array($assigned_user_id)
	);

	$leankitkanban = new LeanKitKanban($username,$password);
	$response = $leankitkanban->updateCard($array_card, $evtcl->board_id); 
	if($response->ReplyCode == '202') {
	  $msg .= "The Card is unblocked.";
	  //$evtcl->fields['discuss'] = $evtcl->block_unblock_reason;
	} else {
	  $msg .= $response->ReplyText;
	}
      } else {
	$msg .= "You must enter the reason to unblock the card.";
      }
    } else {
      $msg .= "You have not set up your LeanKit Kanban Login Credentials.";
    }

    $_SESSION["ofuz_kanban_message"] = $msg;
  }

  /**
   * Formats MySql date to DDMMYYYY
   * @param string, string
   * @return string
   */
  public function convertMysqlDateToMMDDYYY($mysql_date, $separator) {
    $date = explode("-", $mysql_date);
    $date = $date[1].$separator.$date[2].$separator.$date[0];
    return $date;
  }

  /**
   * This method adds the Block/Unblock Reason (from Kanban Plugin) as Task Discussion note.
   * @param object : EventControler
   */
  public function eventAddReasonAsTaskNote(EventControler $evtcl) {
    $prefix_note = ($evtcl->block_unblock_flag == "Block") ? "<b>Task Block </b>" : "<b>Task Unblock </b>" ;
    $do_pd = new ProjectDiscuss();
    $do_pd->idproject_task = $evtcl->ofuz_idprojecttask;
    $do_pd->discuss = $prefix_note.$evtcl->block_unblock_reason;
    $do_pd->date_added = date("Y-m-d");
    $do_pd->document = "";
    $do_pd->hours_work = "0.00";
    $do_pd->iduser = $_SESSION['do_User']->iduser;
    $do_pd->discuss_edit_access = 'user';
    $do_pd->type = 'Note';
    $do_pd->add();

    $evtcl->idproject_discuss = $do_pd->getPrimaryKeyValue();
    
    // Generating fields array for EventControler because the existing method ProjectDiscuss->eventSendDiscussMessageByEmail
    // uses it.
    $fields = array();
    $fields['document'] = "";
    $fields['discuss'] = $prefix_note.$evtcl->block_unblock_reason;
    $evtcl->fields = $fields;

  }

}
?>