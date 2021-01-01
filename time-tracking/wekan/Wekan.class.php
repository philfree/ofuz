<?php
/*
 * A class file to interact with DB for all the operations
 *
 * @author <ravi@afternow.io>
 */

class Wekan {
  private $conn;

	function __construct($conn) {
		// mysqli connection
		$this->conn = $conn;
  }

  /*
   * When an event is triggered in wekan, this method writes the "Request Body"
     data to a text file
   * 
   * @input string : $content
   */
  public function logEventToFile($content) {
    $file = "wekan.txt";
    $Saved_File = fopen($file, 'w');
    fwrite($Saved_File, $content);
    fclose($Saved_File);
  }

  /*
   * When a User adds a comment on Wekan Board, an event act-addComment is triggered 
     and this method is executed which inserts data(comment details) in the table.
   *
   * @param array : $input
   */
  public function addComment($input) {
    $time_taken = $this->parseTimeOnComment($input['comment']);
    $comment_created_at = date("Y-m-d h:i:s");

    $query = "INSERT INTO wekan_time_tracking 
    (`board_id`,`list_id`,`swimlane_id`,`card_id`,`card`,`board`,`comment_id`,`description`,`user`,
    `comment_created_at`,`time_taken`)
    VALUES ('".$input['boardId']."','".$input['listId']."','".$input['swimlaneId']."','".$input['cardId']."','".$input['card']."','".$input['board']."','".$input['commentId']."','".$input['description']."','".$input['user']."','".$comment_created_at."','".$time_taken."'
    )";

    //$this->logEventToFile($query);
    mysqli_query($this->conn, $query);
     
  }

 /*
   * When a User edits a comment on Wekan Board, an event act-editComment is triggered 
     and this method is executed which updates data(comment details) in the table.
  *
  * @param array : $input
  *
  */ 
  public function editComment($input) {
    $time_taken = $this->parseTimeOnComment($input['comment']);
    $idwekan_time_tracking = $this->commentExists($input['boardId'], $input['cardId'], $input['commentId']);

    if($idwekan_time_tracking) {
      $query = "UPDATE wekan_time_tracking 
              SET `description` = '".$input['description']."',`time_taken` = '".$time_taken."'
              WHERE `idwekan_time_tracking` = '".$idwekan_time_tracking."'"
              ;

      mysqli_query($this->conn, $query);
    }
  }


  public function commentExists($boardId, $cardId, $commentId) {
    $idwekan_time_tracking = "";
    $query = "SELECT `idwekan_time_tracking` 
              FROM `wekan_time_tracking`
              WHERE `board_id` = '".$boardId."' AND `card_id` = '".$cardId."' 
              AND `comment_id`= '".$commentId."'";
    $result = mysqli_query($this->conn, $query);

    if(mysqli_num_rows($result)) {
      $row = mysqli_fetch_object($result);
      $idwekan_time_tracking = $row->idwekan_time_tracking;
    }

    return $idwekan_time_tracking;
  }

  /*
   * When a User deletes a comment from current month on Wekan Board, an event act-addComment is triggered 
     and this method is executed which deletes record from the table.
   * User can not delete comment from previous month/s.
   * @param array : $input
   */
  public function deleteComment($input) {

    $query = "SELECT `idwekan_time_tracking`,`comment_created_at` 
              FROM `wekan_time_tracking`
              WHERE `board_id` = '".$input['boardId']."' AND `card_id` = '".$input['cardId']."' 
              AND `comment_id`= '".$input['commentId']."'";
    $result = mysqli_query($this->conn, $query);
    if(mysqli_num_rows($result)) {
      $row = mysqli_fetch_object($result);
      // timestamps
      $ts_comment_created_at = strtotime($row->comment_created_at);
      $ts_first_day_this_month = strtotime('first day of this month');
      $ts_last_day_this_month = strtotime('last day of this month'); 

      if(($ts_comment_created_at >= $ts_first_day_this_month) && ($ts_comment_created_at <= $ts_last_day_this_month)) {
        $query = "DELETE FROM wekan_time_tracking 
                  WHERE `idwekan_time_tracking` = ".$row->idwekan_time_tracking;

         mysqli_query($this->conn, $query);
      }
    }  
  }

  /*
   * This method parses the time format T{hh:mm} from the comment, validates,
     formats and returns.
   *
   * @param string : $comment
   * @return string : $time_taken
   */
  public function parseTimeOnComment($comment) {
    $time_on_comment = preg_match('#\T{(.*?)\}#', $comment, $matches) ? $matches[1] : "";
    $time_taken = "0.00";

    if($time_on_comment) {
      $arr_time_taken = explode(":", $time_on_comment);

      if(isset($arr_time_taken[1])) {
              $time_taken = $arr_time_taken[0].".".$arr_time_taken[1];
      } else {
              $time_taken = $arr_time_taken[0];
      }
    }

    return $time_taken;
  }

}// end of Class
?>
