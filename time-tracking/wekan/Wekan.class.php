<?php
/*
 * A class file
 *
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

    if(!empty($time_taken)) {
      $comment_created_at = date("Y-m-d h:i:s");
      //$comment = mysqli_real_escape_string($this->conn, $input['comment']);

      $query = "INSERT INTO wekan_time_tracking 
      (`board_id`,`list_id`,`swimlane_id`,`card_id`,`card`,`board`,`comment_id`,`description`,`user`,
      `comment_created_at`,`time_taken`)
      VALUES ('".$input['boardId']."','".$input['listId']."','".$input['swimlaneId']."','".$input['cardId']."','".$input['card']."','".$input['board']."','".$input['commentId']."','".$input['description']."','".$input['user']."','".$comment_created_at."','".$time_taken."'
      )";

      //$this->logEventToFile($query);
      mysqli_query($this->conn, $query);
    }
     
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

    if(!empty($time_taken)) {
      $query = "UPDATE wekan_time_tracking 
              SET `description` = '".$input['description']."',`time_taken` = '".$time_taken."'
              WHERE `board_id` = '".$input['boardId']."' AND `card_id`='".$input['cardId']."' AND `comment_id`='".$input['commentId']."'"
              ;

      //$this->logEventToFile($query);
      mysqli_query($this->conn, $query);
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
    $time_taken = "";

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
