<?php

/**
 * PHP Wrapper class for LeanKit Kanban API integration
 *
 * @author SQLFusion
 * @date 06-25-2012
 * @see http://support.leankitkanban.com/forums/20153741-api
 */

class LeanKitKanban {

  private $account = ''; // Your Account
  private $host = ''; // Your Account Url
  private $api_url = ''; // Your API Url
  private $username = ''; // User's username (email) for LeanKit Kanban login
  private $password = ''; // User's password for LeanKit Kanban login

  private $request_method = ''; // verbs : GET, POST, PUT, DELETE
  private $request_url = ''; // The request url to LeanKit Kanban API
  private $response_data = array(); // The response data from LeanKit Kanban API call
  private $request_data = '';

  // Possible Reply Codes from Kanban API
  public $reply_codes = array(
			      100 => 'NoData',
			      200 => 'DataRetrievalSuccess',
			      201 => 'DataInsertSuccess',
			      202 => 'DataUpdateSuccess',
			      203 => 'DataDeleteSuccess',
			      500 => 'SystemException',
			      501 => 'MinorException',
			      502 => 'UserException',
			      503 => 'FatalException',
			      800 => 'ThrottleWaitResponse',
			      900 => 'WipOverrideCommentRequired',
			      902 => 'ResendingEmailRequired',
			      1000 => 'UnauthorizedAccess'
			 );


  public function __construct($username='', $password='') {
    $this->account = 'sqlfusion';
    $this->host = 'https://'.$this->account.'.leankit.com';
    $this->api_url = $this->host.'/Kanban/Api/';
    $this->username = $username;
    $this->password = $password;
  }

 /**
  * Sets the Request Method : GET, POST, PUT, DELETE
  * If nothing mentioned,it will take GET
  */
  public function setRequestMethod($request_method='') {
    $this->request_method = ($request_method=='') ? 'GET' : $request_method;
  }

 /**
  * Gets the Request Method
  */
  public function getRequestMethod() {
    return $this->request_method;
  }

 /**
  * Sets the Request API url
  * @param string
  */
  public function setRequestURL($request_url) {
    $this->request_url = $request_url;
  }

 /**
  * Gets the Request API url
  */
  public function getRequestURL() {
    return $this->request_url;
  }

 /**
  * Sets the response data returned from Kanban API
  * @param json
  */
  public function setResponseData($response_data) {
    $this->response_data = $response_data;
  }

 /**
  * Gets the response data returned from Kanban API
  */
  public function getResponseData() {
    return $this->response_data;
  }

  /**
  * Sets the data to be posted to Kanban API
   */
  public function setRequestData($request_data) {
    $this->request_data = $request_data;
  }

  /**
  * Gets the data to be posted to Kanban API
   */
  public function getRequestData() {
    return $this->request_data;
  }

 /**
  * Method to use for all the GET requests
  * load/retrieve
  */
  public function get() {
    $this->processRequest();    
  }

 /**
  * Method to use for all the POST requests
  * Create
  */
  public function post() {
    $this->processRequest();
  }

 /**
  * Method to use for all the PUT requests
  * Update
  */
  public function put() {
    $this->processRequest();
  }

 /**
  * Method to use for all the DELETE requests
  * Delete
  */
  public function delete() {
    $this->processRequest();
  }

 /**
  * Common method to process the HTTP request (GET,POST,PUT,DELETE).
  * Making Requests With cURL
  */
  public function processRequest() {
    $curl = curl_init();
    $headers = array('Content-Type: application/json'); 
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC) ;
    curl_setopt($curl, CURLOPT_USERPWD, $this->username.":".$this->password);
    if($this->getRequestMethod() == 'POST') {
      curl_setopt ($curl, CURLOPT_POST, true); // Tell curl to use HTTP POST
      curl_setopt ($curl, CURLOPT_POSTFIELDS, $this->getRequestData()); // Tell curl that this is the body of the POST
    }
    curl_setopt($curl, CURLOPT_HEADER, false); // Tell curl not to return headers, but do return the response
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Return the response
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_URL, $this->getRequestURL());
    $response_data = curl_exec($curl);
    $this->setResponseData(json_decode($response_data));
    curl_close($curl);
  }

 /**
  * Gets a listing of all the Boards that the API user has access to.
    It will be a list of all boards that the API user has at least Reader access to.
  *
  * HTTP: GET
  * @param string $request_url : Boards
  * @return JSON
  * @example http://myaccount.leankitkanban.com/Kanban/Api/Boards
  * @see http://support.leankitkanban.com/entries/20264797-get-boards
  */
  public function getBoards($request_url) {
    $this->setRequestMethod('GET');
    $this->setRequestURL($this->api_url.$request_url);
    $this->get();
    return $this->getResponseData();
  }

  /**
   * This method is used to get all the necessary Identifiers for a Board used within the other API calls. 
     If, for example, you need the list of lanes and their Ids, card types, or users of the board, you would 
     use this method to retrieve that data.
   *
   * HTTP: GET
   * @param int
   * @return JSON
   *
   * @example http://myaccount.leankitkanban.com/Kanban/Api/Board/{boardId}/GetBoardIdentifiers
     @example http://myaccount.leankitkanban.com/Kanban/Api/Board/101000/GetBoardIdentifiers
   *
   * @see http://support.leankitkanban.com/entries/20267921-getboardidentifiers
   */
  public function getBoardIdentifiers($board_id) {
    $this->setRequestMethod('GET');
    $this->setRequestURL($this->api_url."Board/{$board_id}/GetBoardIdentifiers");
    $this->get();
    return $this->getResponseData();
  }

 /**
  * This method retrieves information about a single board,including all of the Board attributes, 
    the Lanes within the Board and all the Cards within those lanes.It includes all the cards in the Backlog, 
    but does not include the cards within the Archive.
  *
  * HTTP: GET
  * @param int
  * @return JSON
  *
  * @example http://myaccount.leankitkanban.com/Kanban/Api/Boards/{boardId}
    @example http://myaccount.leankitkanban.com/Kanban/Api/Boards/101000
  *
  * @see http://support.leankitkanban.com/entries/20267956-get-board
  */
  public function getBoard($board_id) {
    $this->setRequestMethod('GET');
    $this->setRequestURL($this->api_url."Boards/{$board_id}");
    $this->get();
    return $this->getResponseData();
  }

  /**
   * Creates a new Card in the Board.
   * This method has all the parameters specifying the board, lane and position where you want to add the new card. 
     The request body contains the JSON for a new card. You must specify a valid TypeId that matches one of the Card Types 
     for your board. See GetBoardIdentifiers for a listing of valid Card Type Ids and Class Of Service Ids for your board.
   * 
   * HTTP: POST
   * @param array, int, int, int
   * @return JSON
   *
   * @example http://myaccount.leankitkanban.com/Kanban/Api/Board/{boardId}/AddCard/Lane/{laneId}/Position/{position}
     @example http://myaccount.leankitkanban.com/Kanban/Api/Board/101000/AddCard/Lane/101108/Position/2
   *
   * @see http://support.leankitkanban.com/entries/20265078-addcard
   */
  public function addCard($array_card, $board_id, $lane_id, $position=1) {
    $this->setRequestMethod('POST');
    $this->setRequestURL($this->api_url."Board/{$board_id}/AddCard/Lane/{$lane_id}/Position/{$position}");
    $request_data = $this->arrayToJSON($array_card);
    $this->setRequestData($request_data);
    $this->post();
    return $this->getResponseData();
  }

 /**
  * This method simply retrieves the detail for a single card when you request it by card Id.
  *
  * HTTP: GET
  * @param int, int
  * @return JSON
  *
  * @example http://myaccount.leankitkanban.com/Kanban/Api/Board/{boardId}/GetCard/{cardId}
    @example http://myaccount.leankitkanban.com/Kanban/Api/Board/101000/GetCard/101622
  *
  * @see http://support.leankitkanban.com/entries/20267991-getcard
  */
  public function getCard($board_id, $card_id) {
    $this->setRequestMethod('GET');
    $this->setRequestURL($this->api_url."Board/{$board_id}/GetCard/{$card_id}");
    $this->get();
    return $this->getResponseData();
  }

 /**
  * When the ExternalCardId field is enabled for your board, you can use this API call to look up 
    a card by the external Id. If you are using a predefined prefix in your ExternalCardId field 
    settings you can either use the prefix or not with this API call. If any card is found that 
    matches the {externalCardId} passed in the url either with or without the prefix, the details 
    for that card will be retrieved.
  *
  * HTTP: GET
  * @param int, string
  * @return JSON
  *
  * @example http://myaccount.leankitkanban.com/Kanban/Api/Board/{boardId}/GetCardByExternalId/{externalCardId}
    @example http://myaccount.leankitkanban.com/Kanban/Api/Board/101000/GetCardByExternalId/101622
    @example http://myaccount.leankitkanban.com/Kanban/Api/Board/101000/GetCardByExternalId/BZ101622
  *
  * @see http://support.leankitkanban.com/entries/20268001-getcardbyexternalid
  */
  public function getCardByExternalId($board_id, $external_card_id) {
    $this->setRequestMethod('GET');
    $this->setRequestURL($this->api_url."Board/{$board_id}/GetCardByExternalId/{$external_card_id}");
    $this->get();
    return $this->getResponseData();
  }

  /**
   * This method takes the card JSON in the request body and updates the card (using the cardId in the card JSON) with 
     the provided values. The cardId is not added to the url...just in the card JSON in the body.
   * 
   * HTTP: POST
   * @param array, int
   * @return JSON
   *
   * @example http://myaccount.leankitkanban.com/Kanban/Api/Board/{boardId}/UpdateCard
     @example http://myaccount.leankitkanban.com/Kanban/Api/Board/101000/UpdateCard
   *
   * @see http://support.leankitkanban.com/entries/20264822-updatecard
   */
  public function updateCard($array_card, $board_id) {
    $this->setRequestMethod('POST');
    $this->setRequestURL($this->api_url."Board/{$board_id}/UpdateCard");
    $request_data = $this->arrayToJSON($array_card);
    $this->setRequestData($request_data);
    $this->put();
    return $this->getResponseData();
  }

  /**
   * This method deletes a single card. The cardId is specified in the url.
   * 
   * HTTP: POST
   * @param int, int
   * @return JSON
   *
   * @example http://myaccount.leankitkanban.com/Kanban/Api/Board/{boardId}/DeleteCard/{cardId}
     @example http://myaccount.leankitkanban.com/Kanban/Api/Board/101000/DeleteCard/101614
   *
   * @see http://support.leankitkanban.com/entries/20264807-deletecard
   */
  public function deleteCard($board_id, $card_id) {
    $this->setRequestMethod('POST');
    $this->setRequestURL($this->api_url."Board/{$board_id}/DeleteCard/{$card_id}");
    $this->delete();
    return $this->getResponseData();
  }

  /**
   * Converts array to JSON string
   * @param array
   * @return json
   */
  public function arrayToJSON($array_data) {
    return json_encode($array_data);
  }

 /**
  * Archive
  * This method retrieves the archive, all its sub-lanes, and the cards contained in the archive.
  *
  * HTTP: GET
  * @param string
  * @return JSON
  * @example http://myaccount.leankitkanban.com/Kanban/Api/Board/101000/Archive
  * @see http://support.leankitkanban.com/entries/20267951-archive
  */

  public function getArchive($board_id) {
    $this->setRequestMethod('GET');
    $this->setRequestURL($this->api_url."Board/{$board_id}/Archive");
    $this->get();
    return $this->getResponseData();
  }


}//End of class

?>
