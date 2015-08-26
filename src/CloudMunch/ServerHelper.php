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


use CloudMunch\cmDataManager;
use CloudMunch\SSHConnection;
use CloudMunch\Server;
use CloudMunch\ElasticBeanStalkServer;

require_once ("AppErrorLogHandler.php");


 
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
 
 /**
  * This method retreives the details of server from cloudmunch.
  * @param  string $servername Name of the server as registered in cloudmunch.
  * @return \CloudMunch\Server
  */
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
			$server->setTier($detailArray->$servername->tier);
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
  /**
   * This method can be used to add or register a server to cloudmunch data .
   * @param \CloudMunch\Server $server
   * @param string $docker
   */
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
		"sshport" => $server->getSSHPort(),
			"tier"=>$server->getTier()
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
	
	$this->cmDataManager->updateContext($this->appContext->getMasterURL(), "server", $this->appContext->getDomainName(), $deployArray);
 }
 
 /**
  * This method is used to update server data.
  * @param \CloudMunch\Server $server
  */
 function updateServer($server){
 	$serverName=$server->getServerName();
 	$deployArray=$this->cmDataManager->getDataForContext($this->appContext->getMasterURL(), "server", $this->appContext->getDomainName());
		$deployArray=json_decode($deployArray);
		if ($deployArray == null) {
			loghandler(INFO,'Not able to read the server details');
		
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
		"sshport"=>$server->getSSHPort(),
 			"tier"=>$server->getTier()
	);

	$detailArray1[$server->getServerName()] = $dataArray;
	array_push($deployArray, $detailArray1);
	
	$this->cmDataManager->updateContext($this->appContext->getMasterURL(), "server", $this->appContext->getDomainName(), $deployArray);
		}
 }
 
 /**
  * This method is to delete server from cloudmunch.
  * @param  $serverName Name of server.
  */
 function deleteServer($serverName){
 	$deployArray=$this->cmDataManager->getDataForContext($this->appContext->getMasterURL(), "server", $this->appContext->getDomainName());
		$deployArray=json_decode($deployArray);
		if ($deployArray == null) {
			loghandler(INFO,'Not able to read the server details');
		
	} else {
		foreach ($deployArray as $i => $detailArray) {
			if (array_key_exists($serverName, $detailArray)) {
				unset ($deployArray[$i]);
				
			}
		}
		$deployArray = array_values($deployArray);
		$this->cmDataManager->updateContext($this->appContext->getMasterURL(), "server", $this->appContext->getDomainName(),$deployArray);
		
	}
 	
 }
 
 /**
  * This method checks if server exists or is registered in cloudmunch data.
  * @param  $servername Name of server.
  * @return boolean
  */
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
 /**
  * This method returns SSHConnection helper
  * @return \CloudMunch\sshConnection
  */
 function getSSHConnectionHelper(){
 	return new SSHConnection();
 }
 }
?>
