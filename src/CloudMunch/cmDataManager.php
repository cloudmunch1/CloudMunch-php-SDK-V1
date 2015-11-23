<?php
/**
 *  (c) CloudMunch Inc.
 *  All Rights Reserved
 *  Un-authorized copying of this file, via any medium is strictly prohibited
 *  Proprietary and confidential
 *
 *  Rosmi Chandy rosmi@cloudmunch.com
 */
namespace CloudMunch;
require_once ("AppErrorLogHandler.php");


 /**
  * This class  connects to cloudmunch to update /retrieve data
  */

class cmDataManager{
	private $logHelper=null;
	public function __construct($logHandler) {
		
		$this->logHelper=$logHandler;
	}
	
	/**
	 * 
	 * @param string $url 
	 * @param string $apikey
	 * @param string $querystring
	 * @return boolean|jsonobject|string
	 */
function getDataForContext($url,$apikey,$querystring) {
	if (empty($querystring)) {
		$url = $url . "?apikey=" . $apikey;
	}else{
		$url = $url . "?apikey=" . $apikey . "&" . $querystring;
	}

	$result = $this->do_curl($url, null, "GET", null, null);
	
	$result = $result["response"];
	
	if (($result == null)) {
		return false;
	}

	$resultdecode = json_decode($result);
	
	if (is_null($resultdecode)) {		
		return $result;
	}
	
	if($resultdecode->request->status !== "SUCCESS") {			
		$this->logHelper->log(INFO, $resultdecode->request->message);
		return $resultdecode;
	}
		
	return $resultdecode; 
}

/**
 * @return {"data":{"id":"SER2015101311095292382","name":"SER2015101311095292382"},"request":{"status":"SUCCESS"}}
 */
 function putDataForContext($url,$apikey,$data) {
	$dat=array("data"=>$this->json_object($data));
	$dat=$this->json_string($this->json_object($dat));
	
	$url=$url."?apikey=".$apikey;
	$result=$this->do_curl($url, null, "POST", $dat, null);
	
	$result=$result["response"];
	$result=json_decode($result);
	
     if(($result==null) ||($result->request->status !== "SUCCESS")      ){
     	trigger_error ( "Not able to put data to cloudmunch", E_USER_ERROR );
     }
 
	return $result;
}


function updateDataForContext($url,$apikey,$data){
	$dat=array("data"=>$this->json_object($data));
	$url=$url."?apikey=".$apikey;
	$result=$this->do_curl($url, null, "PATCH", $dat, null);
	$result=$result["response"];
	$result=json_decode($result);
	if(($result==null) ||($result->request->status!="SUCCESS")      ){
		trigger_error ( "Not able to put data to cloudmunch", E_USER_ERROR );
	}
	
	return $result;
}
function deleteDataForContext($url,$apikey){
	$url=$url."?apikey=".$apikey;
	$result=$this->do_curl($url, null, "DELETE", null, null);
	$result=$result["response"];
	$result=json_decode($result);
	if(($result==null) ||($result->request->status!="SUCCESS")      ){
		trigger_error ( "Not able to put data to cloudmunch", E_USER_ERROR );
	}
	
	return $result;
}

function startDeployAction($servername, $project, $job_from_which_deploy_triggered, $env, $stage, $deploy_params, $step_config, $domain ) {
	$url = $servername . "/cbdata.php?action=TRIGGERDEPLOYJOB&projectName=$project&jobName=$job_from_which_deploy_triggered&envName=$env&category=deploy&deploytype=$deploy_type&username=CI&domain=$domain&deployParams=".urlencode($deploy_params)."&stepConfig=".urlencode($step_config);
	$options = array (
		CURLOPT_HEADER => 0,
		CURLOPT_HTTPHEADER => array (
			"Content-Type:application/json"
		),
		CURLOPT_URL => $url,
		CURLOPT_FRESH_CONNECT => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_FORBID_REUSE => 1,
		CURLOPT_FOLLOWLOCATION => 1,
		CURLOPT_TIMEOUT => 200,
		CURLOPT_HTTPAUTH => CURLAUTH_ANY,
		CURLOPT_USERPWD => "",
		CURLOPT_POST => 0,
		CURLOPT_VERBOSE => 0,
		CURLOPT_SSL_VERIFYHOST => 0, //2,
		CURLOPT_SSL_VERIFYPEER => false
	);
	$post = curl_init();
	curl_setopt_array($post, $options);
	if (!$result = curl_exec($post)) {
		trigger_error ( "Not able to retrieve data from cloudmunch", E_USER_ERROR );
	}
	curl_close($post);
	return $result;
}

function updateContext($masterurl, $context, $domain, $serverArray) {
	//$serverArray=json_encode($serverArray);
	//	$url =$masterurl . "/cbdata.php?context=".$context."&username=CI&mode=update&domain=".$domain."&data=".$serverArray;
	// global $curl_verbose;
	$curl_verbose = 0;
	//var_dump($serverArray);
	$data = "data=" . json_encode($serverArray);
	$url = $masterurl . "/cbdata.php?context=" . $context . "&username=CI&mode=update&domain=" . $domain;
	//$url=urlencode($url);
	//echo "\nurl is:" . $url.PHP_EOL;

	$options = array (
		CURLOPT_HEADER => 0,
		CURLOPT_HTTPHEADER => array (
			'Content-Type: application/x-www-form-urlencoded'
		),
		CURLOPT_URL => $url,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_FRESH_CONNECT => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_FORBID_REUSE => 1,
		CURLOPT_TIMEOUT => 20,
		CURLOPT_FAILONERROR => 1,
		CURLOPT_POSTFIELDS => $data,
		CURLOPT_POST => 1,
		CURLOPT_VERBOSE => $curl_verbose,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false
	);

	$post = curl_init();
	curl_setopt_array($post, $options);
	$result = curl_exec($post);
	$response_code = curl_getinfo($post, CURLINFO_HTTP_CODE);
if(($result === FALSE) && ($response_code != 100)) {
	$this->logHelper->log(INFO,"result:" . $response_code);
	trigger_error ( "Error in updating to cloudmunch", E_USER_ERROR );
}else{
	$this->logHelper->log(INFO,"Updated:" . $result);
	$this->logHelper->log(INFO,"result:" . $result);
	//echo "\nresult:" . $result.PHP_EOL;
}

}
function updateCustomContext($masterurl, $context, $domain, $serverArray,$id) {
	//$serverArray=json_encode($serverArray);
	//	$url =$masterurl . "/cbdata.php?context=".$context."&username=CI&mode=update&domain=".$domain."&data=".$serverArray;
	global $curl_verbose;
	$curl_verbose = 0;
	
	$data = "data=" . json_encode($serverArray["data"]);
	$url = $masterurl . "/cbdata.php?action=updatecustomcontext&customcontext=" . $context . "&username=CI&mode=update&domain=" . $domain."&id=".$id;
	//$url=urlencode($url);
	echo "\nurl is:" . $url.PHP_EOL;

	$options = array (
			CURLOPT_HEADER => 0,
			CURLOPT_HTTPHEADER => array (
					'Content-Type: application/x-www-form-urlencoded'
			),
			CURLOPT_URL => $url,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_FRESH_CONNECT => 1,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_FORBID_REUSE => 1,
			CURLOPT_TIMEOUT => 20,
			CURLOPT_FAILONERROR => 1,
			CURLOPT_POSTFIELDS => $data,
			CURLOPT_POST => 1,
			CURLOPT_VERBOSE => $curl_verbose,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false
	);

	$post = curl_init();
	curl_setopt_array($post, $options);
	$result = curl_exec($post);
	$response_code = curl_getinfo($post, CURLINFO_HTTP_CODE);
	if(($result === FALSE) && ($response_code != 100)) {
		$this->logHelper->log(INFO,"result:" . $response_code);
		trigger_error ( "Error in updating to cloudmunch", E_USER_ERROR );
	}else{
		$this->logHelper->log(INFO,"Updated:" . $result);
		$this->logHelper->log(INFO,"result:" . $result);
		//echo "\nresult:" . $result.PHP_EOL;
	}

}

function getDataForCustomContext($servername, $context) {

	$context = http_build_query($context);
	$context = urldecode($context);
	$url = $servername . "/cbdata.php?" . $context;

	$options = array (
		CURLOPT_HEADER => 0,
		CURLOPT_HTTPHEADER => array (
			"Content-Type:application/json"
		),
		CURLOPT_URL => $url,
		CURLOPT_FRESH_CONNECT => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_FORBID_REUSE => 1,
		CURLOPT_TIMEOUT => 200,
		CURLOPT_HTTPAUTH => CURLAUTH_ANY,
		CURLOPT_USERPWD => "",
		CURLOPT_POST => 0,
		CURLOPT_VERBOSE => 0,
		CURLOPT_SSL_VERIFYHOST => 0, //2,
	CURLOPT_SSL_VERIFYPEER => false
	);

	$post = curl_init();
	curl_setopt_array($post, $options);
	if (!$result = curl_exec($post)) {
		trigger_error ( "Not able to retrieve data from cloudmunch", E_USER_ERROR );
	}
	curl_close($post);
	
	return $result;
}

function updateServerDetailsList($dnsName = "", $instanceId = "", $amiName = "", $projectId = "", $serverName = "", $CI = false, $emailId = "", $domainName = "", $KeyName = "", $launchParam = array (), $serverdescription = "", $serverType = "", $cloudprovidername = "", $region = "") {

	$deployUtil = new DeployUtil();
	$deployArray = $deployUtil->readDeployConfigFile();

	$toBeAddedArray = array ();
	if (is_array($dnsName) && is_assoc($dnsName)) {
		array_push($toBeAddedArray, $dnsName);
	} else
		if (!is_array($dnsName)) {
			$inputServerDetails = array (
				"dnsName" => $dnsName,
				"instanceId" => $instanceId,
				"amiName" => $amiName,
				"projectId" => $projectId,
				"serverName" => $serverName,
				"CI" => $CI,
				"emailId" => $emailId,
				"domainName" => $domainName,
				"KeyName" => $KeyName,
				"launchParam" => $launchParam,
				"serverdescription" => $serverdescription,
				"serverType" => $serverType,
				"cloudprovidername" => $cloudprovidername,
				"region" => $region
			);
			array_push($toBeAddedArray, $inputServerDetails);
		} else
			if (is_array($dnsName) && !is_assoc($dnsName)) {
				$toBeAddedArray = $dnsName;
			}

	foreach ($toBeAddedArray as $index => $serverDetails) {
		$deployArray = updateServerUtilityMethod($serverDetails, $deployArray, $deployUtil);
	}

	$deployUtil->writeToDeployFile($deployArray);
}
function notifyUsersInCloudmunch($serverurl,$message,$contextarray,$domain){
	//	$url =$masterurl . "/cbdata.php?context=".$context."&username=CI&mode=update&domain=".$domain."&data=".$serverArray;
	// global $curl_verbose;
	$curl_verbose = 0;
	//var_dump($serverArray);
	
	
	$dataarray=json_encode($contextarray);
	$dataarray=urlencode($dataarray);
	$message=urlencode($message);
	//cbdata.php?action=NOTIFY&to=*&message=whatever message&usercontext={�project�:project name,�job�:jobname,�context�:�servers�,�id�:server name�}
	//$data = "data=" . json_encode($serverArray);
	$usercontext = "usercontext=" . $dataarray;
//	$url = $serverurl . "/cbdata.php?action=NOTIFY&to=*&message=".$message."&usercontext=".$dataarray."&domain=" . $domain."&username=CI";
	$url = $serverurl . "/cbdata.php?action=NOTIFY&to=*&message=".$message."&domain=" . $domain."&username=CI";
	//$url=urlencode($url);
	//echo "\nurl is:" . $url.PHP_EOL;

	$options = array (
		CURLOPT_HEADER => 0,
		CURLOPT_HTTPHEADER => array (
			'Content-Type: application/x-www-form-urlencoded'
		),
		CURLOPT_URL => $url,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_FRESH_CONNECT => 1,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_FORBID_REUSE => 1,
		CURLOPT_TIMEOUT => 20,
		CURLOPT_FAILONERROR => 1,
			CURLOPT_POSTFIELDS => $usercontext,
		CURLOPT_POST => 1,
		CURLOPT_VERBOSE => $curl_verbose,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_SSL_VERIFYHOST => false
	);

	$post = curl_init();
	curl_setopt_array($post, $options);
	$result = curl_exec($post);
	$response_code = curl_getinfo($post, CURLINFO_HTTP_CODE);
if($result === FALSE) {
	trigger_error ( "Error in notifying to cloudmunch", E_USER_ERROR );
}else{
	$this->logHelper->log(INFO,"result:" . $result);
	$this->logHelper->log(INFO, "Notification send");
	//echo "\nresult:" . $result.PHP_EOL;
}
}

function do_curl($url, $headers = null, $requestType = null, $data = null, $curlOpts = null)
{
	if (!is_scalar($url)) {
		$parm = json_object($url);
		if (!empty($parm)) {
			$url = json_value($parm, "url");
			$headers = json_value($parm, "headers");
			$requestType = json_value($parm, "method");
			$data = json_value($parm, "data");
			$curlOpts = json_value($parm, "curl_options");
		}
	}
	$userAgent = 'curl/7.24.0 (x86_64-redhat-linux-gnu)';
	$userAgent .= ' libcurl/7.24.0 NSS/3.13.5.0 zlib/1.2.5 libidn/1.18 libssh2/1.2.2';
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_VERBOSE, true);
	if (!empty($headers)) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	if (!empty($requestType)) {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $requestType);
		if (!empty($data)) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->json_string($data));
		}
	}

	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_CERTINFO, true);
	curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	$curlOpts = $this->json_object($curlOpts);
	if (!empty($curlOpts)) {
		foreach ($curlOpts as $curlOption => $curlOptionValue) {
			switch ($curlOption) {
				case 'CURLOPT_USERPWD':
					$curlOption = CURLOPT_USERPWD;
					break;

				default:
					# code...
					break;
			}
			curl_setopt($ch, $curlOption, $curlOptionValue);
		}
	}

	$results = curl_exec($ch);
	
	if (!$results) {
		$curlMsg = curl_error($ch);
		$msg =  "ERROR: Could not request provider " . $curlMsg ;
		$hostDown = "503 Service Unavailable";
		$this->logHelper->log("INFO", "Request to provider ended in error. Response:" . $curlMsg);
		if (strstr($msg, $hostDown)) {
			$this->logHelper->log("INFO", "Provider service is not available now. Please retry after some time.");
		} elseif (strstr($msg, " 404 ")) {
			$this->logHelper->log("INFO", "Provider service is not found or not configured correctly. Please contact support");
		} elseif (strstr($msg, "Operation timed out")) {
			$this->logHelper->log("INFO", "Provider service operation timed out. Please retry after some time.");
		}
	} else {
		$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$headerSent = curl_getinfo($ch, CURLINFO_HEADER_OUT);
		$msg =  $results;
		$this->logHelper->log("DEBUG", "Request to provider ended.: Details below");
		$this->logHelper->log("DEBUG", str_pad("|-", 120, "-"));
	//	$this->logHelper->log("INFO", "|URL......... :" . $url);
	//	$this->logHelper->log("INFO", "|Method...... :" . $requestType);
	//	$this->logHelper->log("INFO", "|Header sent. :" . $headerSent);
	//	$this->logHelper->log("INFO", "|Data sent... :" . $this->json_string($data));
		$this->logHelper->log("DEBUG", "|Response code :" . $responseCode);
		$responseText = $this->startsWith($results, "<") ? $this->html2txt($results) : $results;
	//	$this->logHelper->log("INFO", "|Response text :" . $responseText);
	//	$this->logHelper->log("INFO", str_pad("-", 120, "-"));
	//	$this->logHelper->log("INFO", "Response :" . $responseText);
	}
	$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$headerSent = curl_getinfo($ch, CURLINFO_HEADER_OUT);
	curl_close($ch);
	if ($responseCode != 200) {
		if ($responseCode === 0) {
			$this->logHelper->log("ERROR", "Interface system url host could not be resolved. Please check configurations/settings");
		} elseif ($responseCode === 401) {
			$this->logHelper->log("ERROR", "Interface system url host could not be accessed due to authentication failure");
		} else {
		//	$commonMessage = array();
		//	$commonMessage["503"] = "Service is unavailable now";
		//	$responseMessage = $this->json_value($commonMessage, $responseCode, "Interface service is experiencing issues");
			//$this->logHelper->log("ERROR", "Call to  interface ended in error [" . $responseCode . "] " . $responseMessage);
			$this->logHelper->log("ERROR", "Service is not available");
		}
	}
	$response = array();
	$response["code"] = $responseCode;
	$response["header"] = $headerSent;
	$response["response"] = $results;

	
	return $response;
}
function json_object($data) {
	if (is_scalar($data)) {
		return json_decode($data);
	}
	else {
		if (is_array($data)) {
			return json_decode(json_encode($data,JSON_UNESCAPED_SLASHES));
		}
		else {
			return $data;
		}
	}
}
function json_string($data) {
	if (is_scalar($data)) {
		return $data;
	}
	else {
		return json_encode($data,JSON_UNESCAPED_SLASHES);
	}
}

function startsWith($haystack, $needle){
	if (($haystack === null)||($needle === null)) {
		return ($haystack === $needle);
	}
	if (!is_scalar($haystack)) {
		$haystack = $this->json_string($haystack);
	}
	if (!is_scalar($needle)) {
		$needle = $this->json_string($needle);
	}
	return $needle === "" || strpos($haystack, $needle) === 0;
}

function html2txt($document){
	$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
			'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
			'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
			'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
	);
	$text = preg_replace($search, '_$_', $document);
	$textx = explode('_$_', $text);
	$text = "";
	foreach($textx as $idx => $line) {
		$text .= " " . $line;
	}
	return $text;
}

function json_value($json, $key = null, &$path = null, &$save_key = null) {
	if (is_scalar($json)) {
		$json = json_object($json);
	}
	if (empty($json)) {
		return null;
	}
	if ($key === null) {
		$key_list = null;
		foreach($json as $json_key => $json_data) {
			if ($key_list === null) {
				$key_list = array();
			}
			array_push($key_list, $json_key);
		}
		return $key_list;
	}

	$node_path = null;
	$wildcard_key = null;
	$key = trim($key);
	if (trim($key) === "*") {
		$result = $json;
	}
	elseif ($key === "?") {
		$first_key = null;
		foreach($json as $data_key => $data_value) {
			$first_key = $data_key;
			break;
		}
		return $first_key;
	}
	elseif ($key === "??") {
		$first_key = null;
		foreach($json as $data_key => $data_value) {
			if (empty($first_key)) {
				$first_key = $data_key;
			}
			else {
				if (is_scalar($first_key)) {
					$first_key = array($first_key);
				}
				array_push($first_key,$data_key);
			}
		}
		return $first_key;
	}
	else {
		$lvl = 0;
		$result = get_json_value($json, $key, null, $node_path, $wildcard_key, $lvl);
		$path = $node_path;
		$save_key = $wildcard_key;
	}
	return $result;
}
}
?>
