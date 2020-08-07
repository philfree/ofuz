<?php
/*
 * wekan Outgoing Webhook: http://ofuz.localhost/wekan
 * The Rewrite rule is written in Virtual Host and .htaccess file
 * Current setting sends following webhook-attributes:
   text,cardId,listId,oldListId,boardId,commentId,card,board,comment,user,swimlaneId,description
   To include/remove webhook-attributes, we can set it in snap:
   https://github.com/wekan/wekan-snap/wiki/Supported-settings-keys
 *  
 *  
 * JSON Request body sent by Outgoing Webhook when commented in the card on wekan board
{"text":"ravi commented on card \"research webhook\": \"Test comment T{02:45}<br>\" at list \"In Progress\" at swimlane \"Default\" at board \"ExploreWekan\"\nhttp://wekan.localhost/b/xG9j36C5ZwQuwZA3F/explorewekan/TfWLfMjPJLBLgoi2E","cardId":"TfWLfMjPJLBLgoi2E","listId":"sdDoLLGBPMxFzoxQw","boardId":"xG9j36C5ZwQuwZA3F","commentId":"5MLPSTfKNT4QCaFaH","card":"research webhook","board":"ExploreWekan","comment":"Test comment T{02:45}<br>","user":"ravi","swimlaneId":"nhFbMAHr3ArEYXr88","description":"act-addComment"}

  * JSON to array
 Array
(
    [text] => ravi commented on card "research webhook": "Test comment T{02:45}" at list "In Progress" at swimlane "Default" at board "ExploreWekan" http://wekan.localhost/b/xG9j36C5ZwQuwZA3F/explorewekan/TfWLfMjPJLBLgoi2E
    [cardId] => TfWLfMjPJLBLgoi2E
    [listId] => sdDoLLGBPMxFzoxQw
    [boardId] => xG9j36C5ZwQuwZA3F
    [commentId] => 5MLPSTfKNT4QCaFaH
    [card] => research webhook
    [board] => ExploreWekan
    [comment] => Test comment T{02:45} 
    [user] => ravi
    [swimlaneId] => nhFbMAHr3ArEYXr88
    [description] => act-addComment
  )

 * @author <AfterNow Team>
 */

include_once("../config.php");
include_once("Wekan.class.php");

$input = (array) json_decode(file_get_contents('php://input'), TRUE);

// To test Request Body data and write to a file
/*
$input = file_get_contents('php://input');
$wekan = new Wekan($conn);
$wekan->logEventToFile($input);
exit();
*/

/*
 * When user enters comment on the card
 * The Request Body sent by Webhook is explained on the beginning of this
   script
 */
if($input['description'] == "act-addComment") {
  $wekan = new Wekan($conn);
  $wekan->addComment($input);
}

/*
 * When user edits comment on the card
 * The Request Body sent by Webhook is explained on the beginning of this
   script
 */
if($input['description'] == "act-editComment") {
  $wekan = new Wekan($conn);
  $wekan->editComment($input);
}

/*
 * When user deletes a comment on the card
 * The Request Body sent by Webhook is explained on the beginning of this
   script
 */
if($input['description'] == "act-deleteComment") {
  $wekan = new Wekan($conn);
  $wekan->deleteComment($input);
}

// return response to webhook 
echo "{'status':'ok'}";
?>
