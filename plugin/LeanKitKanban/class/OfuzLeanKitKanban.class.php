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
    
  public $table = "";
  protected $primary_key = "";

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
	$lane_id = $this->getCardLaneId($evtcl->board, "In Queue");
	$today = date("d/m/Y");
	$task = new Task();
	$task->getId($evtcl->ofuz_task_id);
	$array_card = array(
	    "Title" => $task->task_description,
	    "Description" => $task->task_description,
	    "TypeId" => $card_type_id,
	    "Priority" => 1,
	    "Size" => "",
	    "IsBlocked" => false,
	    "BlockReason" => "",
	    "DueDate" => $today,
	    "ExternalSystemName" => "http://www.ofuz.net/Task/".$evtcl->ofuz_task_id,
	    "ExternalSystemUrl" => "http://www.ofuz.net/Task/".$evtcl->ofuz_task_id,
	    "Tags" => "",
	    "ClassOfServiceId" => "",
	    "ExternalCardID" => $evtcl->ofuz_task_id,
	    "AssignedUserIds" => array($assigned_user_id)
	);

	$leankitkanban = new LeanKitKanban($username,$password);
	$response = $leankitkanban->addCard($array_card, $evtcl->board, $lane_id, 1);      
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

}
?>