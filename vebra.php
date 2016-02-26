<?php

/**
 * 	@author		Hadi Tajallaei	<hadi@impixel.com>
 * 	@license	Apache
 * 	@abstract	This is a Adapter Connection for Vebra API
 */
Class VebraApi {
	
	private $_datafeedid = "yourdatafeedid";
	private $_username = "yourusername";
	private $_password = "yourpassword";
	private $_version  = 9;
	
	
	public function __construct() {
		if (!session_id()) {
			session_start();
		}
		
		$auth = $this->apiAuth();
		if (!isset($auth)) {
			$this->getToken();
			$status = $this->apiAuth();
			if (isset($status)) {
				//echo "It's connected";
				return true;
			} else {
				//echo "Problem connecting";
				return false;
			}
		} else {
			//echo "It's already authenticated";
			return true;
		}
	}
	
	
	/**
	 * 	Will generate tokken if tokken doesn't exist or expired
	 * 	@return	<STRING> Token
	 */
	private function getToken() {
		$url = "http://webservices.vebra.com/export/".$this->_datafeedid."/v".$this->_version."/branch";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		$username = $this->_username;
		$password = $this->_password;
		$userpass = "$username:$password";
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic '.base64_encode($userpass) ));
		
		$response = curl_exec($ch);
		curl_close($ch);
		
		list($headers, $body) = explode("\r\n\r\n", $response);
		$headers = nl2br($headers);
		$headers = explode('<br />', $headers);
		
		foreach($headers as $header) {
		    $components = explode(': ', trim($header));
		    $headers[$components[0]] = $components[1];
		}
		$token_storage = fopen('token.txt', 'w') or die("Unable to open file!");
		fwrite($token_storage, "");
		fwrite($token_storage, $headers['Token']);
		fclose($token_storage);
		
		return $headers['Token'];
	}
	
	
	/**
	 * 	Attempts to Connect to API with existing Token from token.txt
	 * 	@return	<BOOL>
	 */
	private function apiAuth() {
		$token_storage = fopen('token.txt', 'r') or die("Unable to open file!");
		$token = trim(fread($token_storage,filesize("token.txt")));
		fclose($token_storage);
		$url = "http://webservices.vebra.com/export/".$this->_datafeedid."/v".$this->_version."/branch";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic '.base64_encode($token) ));
		$response = curl_exec($ch);
		curl_close($ch);
		
		list($headers, $body) = explode("\r\n\r\n", $response);
		$headers = nl2br($headers);
		$headers = explode('<br />', $headers);
		
		foreach($headers as $header) {
		    $components = explode(': ', trim($header));
		    $headers[$components[0]] = $components[1];
		}
		
		$headers = explode(" ",$headers[0]);
		$status_code = strtoupper($headers[2]);
		// it prints OK or UNAUTHORIZED
		if ($status_code == "OK") {
			return true;
		}
	}

	/**
	 *  It gets all available branches, please note branch ID is in URL after /branch/
	 * 	@return	XML
	 */
	public function getBranches() {
		$token_storage = fopen('token.txt', 'r') or die("Unable to open file!");
		$token = trim(fread($token_storage,filesize("token.txt")));
		fclose($token_storage);
		$url = "http://webservices.vebra.com/export/".$this->_datafeedid."/v".$this->_version."/branch";
		
		$headers = array (
		    "Content-type: application/xml",
		    "Connection: close",
		    "Authorization: Basic ".base64_encode($token)
		);
		
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$data = curl_exec($ch);
		return $data;
	}
	
	
	/**
	 * 	Lists properties in specified branch
	 * 	@param	Branch ID <STRING>
	 * 	@return XML
	 */
	public function getProperties($branch_id) {
		$token_storage = fopen('token.txt', 'r') or die("Unable to open file!");
		$token = trim(fread($token_storage,filesize("token.txt")));
		fclose($token_storage);
		$url = "http://webservices.vebra.com/export/".$this->_datafeedid."/v".$this->_version."/branch/{$branch_id}/property";
		
		$headers = array (
		    "Content-type: application/xml",
		    "Connection: close",
		    "Authorization: Basic ".base64_encode($token)
		);
		
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$data = curl_exec($ch);
		return $data;
	}
	
	
	/**
	 * 	Brings a record of a property
	 * 	@param	Branch ID <STRING>
	 * 	@param	Property ID <STRING>
	 * 	@return XML
	 */
	public function getProperty($branch_id,$property_id) {
		$token_storage = fopen('token.txt', 'r') or die("Unable to open file!");
		$token = trim(fread($token_storage,filesize("token.txt")));
		fclose($token_storage);
		$url = "http://webservices.vebra.com/export/".$this->_datafeedid."/v".$this->_version."/branch/{$branch_id}/property/{$property_id}";
		
		$headers = array (
		    "Content-type: application/xml",
		    "Connection: close",
		    "Authorization: Basic ".base64_encode($token)
		);
		
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		$data = curl_exec($ch);
		return $data;
	}
	
	
	
	public function __destruct() {
		if (session_id()) {
			session_destroy();
		}
	}
}

// Usage
$vebra = new VebraApi;
$dummy = $vebra->getBranches();
//$dummy = $vebra->getProperties('24205');
//$dummy = $vebra->getProperty('24205','25900216');
header("Content-type: application/xml");
die($dummy);

