<?php
/*
 *
 *
 */

class GitHubAPI {
	private $api_endpoint = 'https://api.github.com/graphql';
	//private $authorization_key = 'bf543ffd1ad36b35b56a850365bee445f5b5642e';
	private $authorization_key = '6bc945ee127280b853c417fa70710b7804e905e3'; // ofuz dev
	private $queryJSON;

	/*
	 *
	 */
	function processQuery() {
		$authorization = 'Authorization: bearer '.$this->authorization_key;
		$header = array(
						'Content-Type: application/json',
						'User-Agent: GitHub API access',	
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
