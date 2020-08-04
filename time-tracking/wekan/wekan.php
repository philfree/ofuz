<?php
/*
 * wekan Outgoing Webhook: http://ofuz.localhost/wekan
 * 
 * JSON Request body when commented in card in wekan board
{"text":"ravi commented on card \"read mongoDB\": \"test 5 \nT{01:30}\" at list \"In Progress\" at swimlane \"Default\" at board \"ExploreWekan\"\nhttp://wekan.localhost/b/xG9j36C5ZwQuwZA3F/explorewekan/9JkufRaokf87jZrSz","cardId":"9JkufRaokf87jZrSz","listId":"sdDoLLGBPMxFzoxQw","boardId":"xG9j36C5ZwQuwZA3F","comment":"test 5 \nT{01:30}","user":"ravi","card":"read mongoDB","commentId":"9sJYi8PDxiQt98hSb","swimlaneId":"nhFbMAHr3ArEYXr88","description":"act-addComment"}

  * JSON to array
 Array
(
    [text] => ravi commented on card "create rest api": "four T{02:30}" at list "In Progress" at swimlane "Default" at board "ExploreWekan"
http://wekan.localhost/b/xG9j36C5ZwQuwZA3F/explorewekan/Gw4FdbKJjZ5u3ufRM
    [cardId] => Gw4FdbKJjZ5u3ufRM
    [listId] => sdDoLLGBPMxFzoxQw
    [boardId] => xG9j36C5ZwQuwZA3F
    [comment] => four T{02:30} 
    [user] => ravi
    [card] => create rest api
    [commentId] => HojRbiasS2CuSXzMJ
    [swimlaneId] => nhFbMAHr3ArEYXr88
    [description] => act-addComment
)
 */

include_once("../config.php");
include_once("Wekan.class.php");

$input = (array) json_decode(file_get_contents('php://input'), TRUE);

/*
 *
 */
if($input['description'] == "act-addComment") {
  $wekan = new Wekan($conn);
  $wekan->addComment($input);
}

/*
 *
 */
if($input['description'] == "act-editComment") {
  $wekan = new Wekan($conn);
  $wekan->editComment($input);
}

// return response to webhook 
echo "{'status':'ok'}";
?>
