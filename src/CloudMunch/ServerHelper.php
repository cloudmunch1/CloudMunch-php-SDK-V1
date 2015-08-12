<?php
namespace CloudMunch;

require_once ("cmDataManager.php");
require_once ("AppErrorLogHandler.php");
require_once ("Server.php");
require_once ("Cloud/AWS/ElasticBeanStalkServer.php");
/*
 * Created on 04-Feb-2015
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /**
  * This is a helper class to perform actions on server like providing methods to add ,read and update 
  * servers.
  */
 class ServerHelper{

 private $appContext=null;
 private $cmDataManager = null;

  public function __construct($appContext){
  	$this->appContext = $appContext;
  	$this->cmDataManager = new cmDataManager();
 	
 }
 function getServer($servername){
 	
 	$deployArray = $this->cmDataManager->getDataForContext($this->appContext->getMasterURL(), "server", $this->appContext->getDomainName());
	
	$deployArray = json_decode($deployArray);
	
	if ($deployArray == null) {
		$deployArray = array ();
	}

	foreach ($deployArray as $i => $detailArray) {
		if (array_key_exists($servername, $detailArray)) {
			if(isset($detailArray->$servername->assetname) && $detailArray->$servername->assetname == "ElasticBeanStalk"){
				$server=new ElasticBeanStalkServer();
			}else{
			$server=new Server();
			}
			$server->setServerName($servername);
			$server->setDescription($detailArray->$servername->description);
			$server->setDNS($detailArray->$servername->dnsName);
			$server->setDomainName($detailArray->$servername->domainName);
			$server->setCI($detailArray->$servername->CI);
			$server->setDeploymentStatus($detailArray->$servername->deploymentStatus);
			$server->setInstanceId($detailArray->$servername->instanceId);
			$server->setImageID($detailArray->$servername->amiID);
			$server->setLauncheduser($detailArray->$servername->username);
			$server->setBuild($detailArray->$servername->build);
			$server->setAppName($detailArray->$servername->appName);
			$server->setDeployTempLoc($detailArray->$servername->deployTempLoc);
			$server->setBuildLocation($detailArray->$servername->buildLoc);
			$server->setPrivateKeyLoc($detailArray->$servername->privateKeyLoc);
			$server->setPublicKeyLoc($detailArray->$servername->publicKeyLoc);
			$server->setLoginUser($detailArray->$servername->loginUser);
			$server->setServerType($detailArray->$servername->serverType);
			$server->setAssettype($detailArray->$servername->assettype);
			$server->setStatus($detailArray->$servername->status);
			$server->setStarttime($detailArray->$servername->starttime);
			$server->setProvider($detailArray->$servername->provider);
			$server->setRegion($detailArray->$servername->region);
			$server->setCmserver($detailArray->$servername->cmserver);
			$server->setAssetname($detailArray->$servername->assetname);
			$server->setInstancesize($detailArray->$servername->instancesize);
			$server->setPassword($detailArray->$servername->password);
			$server->setSSHPort($detailArray->$servername->sshport);
			if($server instanceof ElasticBeanStalkServer){
				$server->setEnvironmentName($detailArray->$servername->environmentName);
				$server->setBucketName($detailArray->$servername->bucketName);
				$server->setApplicationName($detailArray->$servername->applicationName);
				$server->setTemplateName($detailArray->$servername->templateName);
				
				
			}
			return $server;
		}
	}
	trigger_error("Server does not exist", E_USER_ERROR);
	
 }
 
 function addServer($server,$docker = false){
 	
	$deployArray = $this->cmDataManager->getDataForContext($this->appContext->getMasterURL(), "server", $this->appContext->getDomainName());
	//echo $deployArray;
	$deployArray = json_decode($deployArray);
	
	if ($deployArray == null) {
		$deployArray = array ();
	}
	$dataArray = array (
	
		"description" => $server->getDescription(),
		"dnsName" => $server->getDNS(),
		"domainName" => $server->getDomainName(),
		"emailID" => $server->getEmailId(),
		"CI" => $server->getCI() ? 'y' : 'n',
		"deploymentStatus" => $server->getDeploymentStatus(),
		"instanceId" => $server->getInstanceId(),
		"amiID" => $server->getImageID(),
		"username" => $server->getLauncheduser(),
		"build" => $server->getBuild(),
		"appName" =>$server->getAppName(),
		"deployTempLoc" => $server->getDeployTempLoc(), //need to check
		"buildLoc" => $server->getBuildLocation(),
		"privateKeyLoc" => $server->getPrivateKeyLoc(),
		"publicKeyLoc" => $server->getPublicKeyLoc(),
		"loginUser" => $server->getLoginUser(),
		"serverType" => $server->getServerType(),
		"assettype" => $server->getAssettype(),
		"status" => $server->getStatus(),
		"starttime" => $server->getStarttime(),
		"provider" => $server->getProvider(),
		"region" => $server->getRegion(),
		"cmserver" => $server->getCmserver(),
		"assetname" => $server->getAssetname(),
		"instancesize" => $server->getInstancesize(),
		"password" => $server->getPassword(),
		"sshport" => $server->getSSHPort()
	);
	if($server instanceof ElasticBeanStalkServer){
		$dataArray[applicationName]=$server->getApplicationName();
		$dataArray[templateName]=$server->getTemplateName();
		$dataArray[environmentName]=$server->getEnvironmentName();
		$dataArray[bucketName]=$server->getBucketName();	
	}

	if($docker){
		$dataArray[projects] = array ($server->getAppName() => array ("buildNo" => $server->getBuild()));
	}


	$detailArray[$server->getServerName()] = $dataArray;
	array_push($deployArray, $detailArray);
	
	updateContext($this->appContext->getMasterURL(), "server", $this->appContext->getDomainName(), $deployArray);
 }
 
 
 function updateServer($server){
 	$serverName=$server->getServerName();
 	$deployArray=$this->cmDataManager->getDataForContext($this->appContext->getMasterURL(), "server", $this->appContext->getDomainName());
		$deployArray=json_decode($deployArray);
		if ($deployArray == null) {
			loghandler(INFO,'Not able to read the server details');
		//sysout("ERROR", 'Not able to read the server details');
	} else {
		foreach ($deployArray as $i => $detailArray) {
			if (array_key_exists($serverName, $detailArray)) {
				unset ($deployArray[$i]);
				
			}
		}
		$deployArray = array_values($deployArray);
 	$dataArray = array (
	
			"description" => $server->getDescription(),
		"dnsName" => $server->getDNS(),
		"domainName" => $server->getDomainName(),
		"emailID" => $server->getEmailId(),
		"CI" => $server->getCI() ? 'y' : 'n',
		"deploymentStatus" => $server->getDeploymentStatus(),
		"instanceId" => $server->getInstanceId(),
		"amiID" => $server->getImageID(),
		"username" => $server->getLauncheduser(),
		"build" => $server->getBuild(),
		"appName" =>$server->getAppName(),
		"deployTempLoc" => $server->getDeployTempLoc(), //need to check
	"buildLoc" => $server->getBuildLocation(),
		"privateKeyLoc" => $server->getPrivateKeyLoc(),
		"publicKeyLoc" => $server->getPublicKeyLoc(),
		"loginUser" => $server->getLoginUser(),
		"serverType" => $server->getServerType(),
		"assettype" => $server->getAssettype(),
		"status" => $server->getStatus(),
		"starttime" => $server->getStarttime(),
		"provider" => $server->getProvider(),
		"region" => $server->getRegion(),
		"cmserver" => $server->getCmserver(),
		"assetname" => $server->getAssetname(),
		"instancesize" => $server->getInstancesize(),
		"password"=>$server->getPassword(),
		"sshport"=>$server->getSSHPort()
	);

	$detailArray1[$server->getServerName()] = $dataArray;
	array_push($deployArray, $detailArray1);
	
	updateContext($this->appContext->getMasterURL(), "server", $this->appContext->getDomainName(), $deployArray);
		}
 }
 
 function deleteServer($serverName){
 	$deployArray=$this->cmDataManager->getDataForContext($this->appContext->getMasterURL(), "server", $this->appContext->getDomainName());
		$deployArray=json_decode($deployArray);
		if ($deployArray == null) {
			loghandler(INFO,'Not able to read the server details');
		//sysout("ERROR", 'Not able to read the server details');
	} else {
		foreach ($deployArray as $i => $detailArray) {
			if (array_key_exists($serverName, $detailArray)) {
				unset ($deployArray[$i]);
				
			}
		}
		$deployArray = array_values($deployArray);
		updateContext($this->appContext->getMasterURL(), "server", $this->appContext->getDomainName(),$deployArray);
		
	}
 	
 }
 
 function checkServerExists($servername){
 	$deployArray = $this->cmDataManager->getDataForContext($this->appContext->getMasterURL(), "server", $this->appContext->getDomainName());
	
	$deployArray = json_decode($deployArray);

	if ($deployArray == null) {
		$deployArray = array ();
	}

	foreach ($deployArray as $i => $detailArray) {
		if (array_key_exists($servername, $detailArray)) {
			return true;
		}
	}
	return false;
 }
 
/**
* Checks if server is up and running
*
* @param 	string dns 		: 	dns of target server 
* @param    number sshport 	: 	ssh port to be used to check for connection
* @return 	string Success 	: 	displays an appropriate message
*			       Failure 	: 	exits with a failure status with an appropriate message
*/
function checkConnect($dns,$sshport = 22) {
	$connectionTimeout = time();
	$connectionTimeout = $connectionTimeout + (10 * 10);

	do {
	    if (($dns == null) || ($dns == '')) {
	        trigger_error("Invalid dns" . $dns, E_USER_ERROR);
	    }

	    loghandler(INFO, "Checking connectivity to: " . $dns);

	    $connection = ssh2_connect($dns, $sshport);
	    if (!$connection) {
	        sleep(10);
	    }

	} while ((!$connection) && (time() < $connectionTimeout));

	if (!$connection) {
	    trigger_error("Failed to connect to " . $dns, E_USER_ERROR);
	}
}
 
 function checkConnectionToServer($servername){
 	
 }
 
 function getConnectionToServer($servername){
 	
 	
 }
 }
?>
