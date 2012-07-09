<?php
// Copyright 2010 SQLFusion LLC  info@sqlfusion.com
// All rights reserved
/**COPYRIGHTS**/
/**
  * LeanKit Kanban Block in Task Discussion
  * The class must extends the BaseBlock
  * setTitle() will set the Block Title
  * setContent() will set the content
  * displayBlock() call will display the block
  * isActive() is set to true by default so to inactivate the block uncomment the method isActive();
  * @package LeanKitKanban
  * @author SQLFusion
  * @license ##License##
  * @version 0.1
  * @date 2012-06-29
  */


class BlockLeanKitKanbanTaskDiscussion extends BaseBlock{
  public $short_description = 'LeanKit Kanban API Integration with Ofuz';
  public $long_description = 'LeanKit Kanban API Integration with Ofuz';

    /**
    * processBlock() , This method must be added  
    * Required to set the Block Title and The Block Content Followed by displayBlock()
    * Must extend BaseBlock
    */
  function processBlock(){
    $this->setTitle("LeanKit Kanban");
    $content = $this->getBlockConent();
    $this->setContent($content);
    $this->displayBlock();
  }

  function getBlockConent() {
    $content = "";    
    $do_pt = new ProjectTask();
    $idtask = $do_pt->getTaskId($_GET['idprojecttask']);

    $do_olk = new OfuzLeanKitKanban();
    $do_olk->getUserLoginCredentials();
    if($do_olk->getNumRows()) {
      $leankitkanban = new LeanKitKanban($do_olk->username,$do_olk->password);

      //Gets all the Boards from Kanban the API user has access to.
      $boards = $leankitkanban->getBoards('Boards');
      if(is_object($boards)) {
	// 200 => Board(s) successfully retrieved
	if($boards->ReplyCode == '200') {
	  $count_boards = count($boards->ReplyData[0]);
	  if($count_boards) {
	    $board_id = "";
	    $data = array();
	    $arr_boards = array();
	    $card_presents = false;

	    foreach($boards->ReplyData[0] as $obj_board) {
	      $data["board_id"] = $obj_board->Id;
	      $data["board_title"] = $obj_board->Title;
	      $arr_boards[] = $data;

	      $card = $leankitkanban->getCardByExternalId($obj_board->Id, $_GET['idprojecttask']);
	      //Card found in the Board
	      if($card->ReplyCode == '200') {
		$card_presents = true;
		$board_id = $obj_board->Id;
		$board_title = $obj_board->Title;
		$card_exists = $card->ReplyData[0];
	      }
	    }

	    $content .= '<script type="text/javascript">';
	    $content .= '$(document).ready(function() {';
	    //"Save" button is clicked
	    $content .= '$("#btnBlockSave").click(function() {var form = $(this).parents("form:first"); if ($.trim($("#block_unblock_reason").val()) == "Why is the card blocked?" || $.trim($("#block_unblock_reason").val()) == "Why is the card unblocked?" || $.trim($("#block_unblock_reason").val()) == "") {$("#msg").html("Please enter the reason.");$("#msg").slideDown("slow");e.preventDefault();return false;} else { form.submit(); } });';
	    //"Block it" link is clicked
	    $content .= '$("#block_it").click(function() {$("#block_unblock_reason").text("Why is the card blocked?"); $("#block_reason").slideDown("slow");});';
	    //"Unblock it" link is clicked
	    $content .= '$("#unblock_it").click(function() {$("#block_unblock_reason").text("Why is the card unblocked?"); $("#block_reason").slideDown("slow");});';
	    //Block reason textarea is clicked.
	    $content .= '$("#block_unblock_reason").click(function() {if($.trim($(this).text()) == "Why is the card unblocked?" || $.trim($(this).text()) == "Why is the card blocked?") {$("#block_unblock_reason").text("");}});';
	    //"Add to Kanban" submit button is clicked (the form is submitted)
	    $content .= '$("#OfuzLeanKitKanban__eventAddTaskToBoard").submit(function(e){if ($("#board").val() == "") {$("#msg").html("Please select the Board.");$("#msg").slideDown("slow");e.preventDefault();return false;}});';
	    $content .= '});';
	    $content .= '</script>';

	    //Server-side message display block
	    if($_SESSION["ofuz_kanban_message"] != "") {
	      $content .= "<div style='color:#E81313;'>".$_SESSION["ofuz_kanban_message"]."</div>";
	    }
	    //Client-side message display block
	    $content .= "<div id='msg' style='color:#E81313;display:none;'></div>";

	    //If card presents in a Board
	    if($card_presents) {

	      $do_olk = new OfuzLeanKitKanban();
	      $lane_name = $do_olk->getCardLaneName($board_id, $card_exists->LaneId);

	      //Card presents in a Board
	      $content .= "The Task presents in: <br /> <b>Board</b>: ".$board_title."<br /><b>Lane</b>: ".$lane_name."<br />";
	      if($card_exists->IsBlocked) {
		$e_block = new Event("OfuzLeanKitKanban->eventUnblockTheCard");
		$content .= "<b>Blocked</b>: Yes <a id='unblock_it' href='javascript:void(0);'>Unblock it</a>";
	      } else {
		$e_block = new Event("OfuzLeanKitKanban->eventBlockTheCard");
		$content .= "<b>Blocked</b>: No <a id='block_it' href='javascript:void(0);'>Block it</a>";
	      }

	      $e_block->addParam("ofuz_task_id", $idtask);
	      $e_block->addParam("ofuz_idprojecttask", $_GET['idprojecttask']);
	      $e_block->addParam("card_id", $card_exists->Id);
	      $e_block->addParam("lane_id", $card_exists->LaneId);
	      $e_block->addParam("title", $card_exists->Title);
	      $e_block->addParam("description", $card_exists->Description);
	      $e_block->addParam("type_id", $card_exists->TypeId);
	      $e_block->addParam("priority", $card_exists->Priority);
	      $e_block->addParam("size", $card_exists->Size);
	      $e_block->addParam("assigned_user_id",$card_exists->AssignedUserId);
	      $e_block->addParam("index",$card_exists->Index);
	      $e_block->addParam("due_date", $card_exists->DueDate);
	      $e_block->addParam("user_wip_override_comment", $card_exists->UserWipOverrideComment);
	      $e_block->addParam("tags", $card_exists->Tags);
	      $e_block->addParam("class_of_service_id", $card_exists->ClassOfServiceId);
	      $e_block->addParam("assigned_user_ids", $card_exists->AssignedUserIds);
	      $e_block->addParam("board_id", $board_id);

	      $content .= $e_block->getFormHeader();
	      $content .= $e_block->getFormEvent();

	      $content .= "<div id='block_reason' style='display:none;'><textarea name='block_unblock_reason' id='block_unblock_reason' rows='2' cols='28'></textarea> <br />";
	      $content .= "<input type='button' name='btnBlockSave' id='btnBlockSave' value='Save' /> </form>"; //$e_block->getFormFooter("Save");
	      $content .= "</div>";

	    } else {
	      //Card does not present in a Board
	      if(count($arr_boards)) {
		$e_board = new Event("OfuzLeanKitKanban->eventAddTaskToBoard");
		$e_board->addParam("ofuz_task_id", $idtask);
		$e_board->addParam("ofuz_idprojecttask", $_GET['idprojecttask']);
		$content .= $e_board->getFormHeader();
		$content .= $e_board->getFormEvent();
		$content .= "<div>This Task is not added to Kanban Board.</div>";
		$content .= "<div class='spacerblock_5'></div>";
		$content .= "<div><select name='board' id='board'>";
		$content .= "<option value=''>Select Board</option>";
		foreach($arr_boards as $brd) {
		  $content .= "<option value='".$brd["board_id"]."'>".$brd["board_title"]."</option>";
		}
		$content .= "</select></div>";
		$content .= "<div class='spacerblock_5'></div>";
		$content .= $e_board->getFormFooter("Add to Kanban");
	      }
	    }
	  } else {
	    //There is no Board available in Kanban
	    $content .= "There is no Board available in Kanban.";
	  }
	} else {
	  // User does not have access to any Kanban Board.
	  $content .= $boards->ReplyText;
	}
      } else {
	  // User does not have access to any Kanban Board.
	  $content .= "You do not have access to LeanKit Kanban Board.";
      }
      
    } else {
      $content .= "You have not set up your LeanKit Kanban Login Credentials.<br />Please <a href='/Setting/LeanKitKanban/leankit_kanban_authentication'>click here</a> to set up your Kanban Credentials.";
    }

    unset($_SESSION["ofuz_kanban_message"]);
    return $content;
  }
}

?>