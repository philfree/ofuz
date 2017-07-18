<?php
/*
 *
 * key and description are from GitHub settings.
 */

class GitHubAPI {
	private $api_endpoint;
	private $authorization_key;
	private $authorization_key_description;
	private $queryJSON;

	function __construct() {
		$this->api_endpoint = "https://api.github.com/graphql";
		$this->authorization_key = $_SERVER['GitHub_API_ACCESS_TOKEN'];
		$this->authorization_key_description = "Time Tracking System";
	}

	/*
	 *
	 */
	function processQuery() {
		$authorization = 'Authorization: bearer '.$this->authorization_key;
		$header = array(
						'Content-Type: application/json',
						'User-Agent: '.$this->authorization_key_description,
						$authorization
					);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $this->api_endpoint);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS,$this->queryJSON);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($curl);
		curl_close($curl);
		return $result;
	}

	/*
	 *
	 */
	function setQuery($query) {
		$this->queryJSON = $query;
	}

	/*
	 *
	 */
	public function getAllOpenIssuesComments() {
		$query = '{"query": "query { repository(owner:\"htmlfusion\", name:\"gishwhes\") {id, issues(last:100, states:OPEN) { edges { node { id title url comments(first:100) { edges { node { id createdAt author{ login } bodyText } } } } } } } }"}'; 

		$this->setQuery($query);
		$data = $this->processQuery();
		return $data;
	}

	/*
	 *
	 */
	function jsonDecode($data) {
		$data = json_decode($data);
		return $data;
	}
 
}
?>
