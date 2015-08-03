<?php
namespace CloudMunch;
require_once ("AppErrorLogHandler.php");

/*
 * Created on 19-Sep-2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 /**
  * This class  connetcs to cloudmunch to update /retrieve data
  */

class cmDataManager{
function getDataForContext($servername, $context, $domain) {

	$url = $servername . "/cbdata.php?context=" . $context . "&username=CI&domain=" . $domain;
	//echo "\nurl is:" . $url.PHP_EOL;
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
		//echo "\nNot able to retrieve data from cloudmunch" . (curl_error($post));
	}
	curl_close($post);

	
	
	return $result;
}

function getDataForContextLatest($servername, $context) {

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
	global $curl_verbose;
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
	loghandler(INFO,"result:" . $response_code);
	trigger_error ( "Error in updating to cloudmunch", E_USER_ERROR );
}else{
	loghandler(INFO,"Updated:" . $result);
	loghandler(INFO,"result:" . $result);
	//echo "\nresult:" . $result.PHP_EOL;
}

}
function updateCustomContext($masterurl, $context, $domain, $serverArray,$id) {
	//$serverArray=json_encode($serverArray);
	//	$url =$masterurl . "/cbdata.php?context=".$context."&username=CI&mode=update&domain=".$domain."&data=".$serverArray;
	global $curl_verbose;
	$curl_verbose = 0;
	//var_dump($serverArray);
	$data = "data=" . json_encode($serverArray);
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
		loghandler(INFO,"result:" . $response_code);
		trigger_error ( "Error in updating to cloudmunch", E_USER_ERROR );
	}else{
		loghandler(INFO,"Updated:" . $result);
		loghandler(INFO,"result:" . $result);
		//echo "\nresult:" . $result.PHP_EOL;
	}

}

function getDataForCustomContext($servername, $context, $domain) {


	$url = $servername . "/cbdata.php?action=getcustomcontext&customcontext=" . $context . "&username=CI&domain=" . $domain;
	//echo "\nurl is:" . $url.PHP_EOL;
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
		//echo "\nNot able to retrieve data from cloudmunch" . (curl_error($post));
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
	global $curl_verbose;
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
	loghandler(INFO,"result:" . $result);
	//echo "\nresult:" . $result.PHP_EOL;
}
}
}
?>
