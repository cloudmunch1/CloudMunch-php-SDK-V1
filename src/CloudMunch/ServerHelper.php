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

require_once ("CloudmunchConstants.php");
require_once ("AppErrorLogHandler.php");


 
 /**
  * This is a helper class to perform actions on server like providing methods to add ,read and update 
  * servers.
  */
 class ServerHelper{

 private $appContext    = null;
 private $cmDataManager = null;
 private $logHelper     = null;

  public function __construct($appContext,$logHandler){
  	$this->appContext = $appContext;
  	$this->logHelper  = $logHandler;
	$this->cmDataManager = new cmDataManager($this->logHelper, $this->appContext);
 	
 }
 
 /**
  * This method retreives the details of server from cloudmunch.
  * @param  string $servername Name of the server as registered in cloudmunch.
  * @return \CloudMunch\Server
  */
 function getServer($servername){
 	$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/assets/".$servername;
 //	$this->logHelper->log(DEBUG,"serverurl from serverhelper:" . $serverurl);
 	$deployArray = $this->cmDataManager->getDataForContext($serverurl, $this->appContext->getAPIKey(),null);
	if($deployArray === false){
		return false;
	}
	//$deployArray = json_decode($deployArray);
	$detailArray=$deployArray->data;
	
	

	
			if(isset($detailArray->$servername->assetname) && $detailArray->$servername->assetname == "ElasticBeanStalk"){
				$server=new ElasticBeanStalkServer();
			}else{
			$server=new Server();
			}
			$server->setServerName($detailArray->id);
			$server->setDescription($detailArray->description);
			$server->setDNS($detailArray->dnsName);
			$server->setDomainName($detailArray->domainName);
			$server->setCI($detailArray->CI);
			$server->setDeploymentStatus($detailArray->deploymentStatus);
			$server->setInstanceId($detailArray->instanceId);
			$server->setImageID($detailArray->amiID);
			$server->setLauncheduser($detailArray->username);
			$server->setBuild($detailArray->build);
			$server->setAppName($detailArray->appName);
			$server->setDeployTempLoc($detailArray->deployTempLoc);
			$server->setBuildLocation($detailArray->buildLoc);
			$server->setPrivateKeyLoc($detailArray->privateKeyLoc);
			$server->setPublicKeyLoc($detailArray->publicKeyLoc);
			$server->setLoginUser($detailArray->loginUser);
			$server->setServerType($detailArray->serverType);
			$server->setAssettype($detailArray->assettype);
			$server->setStatus($detailArray->status);
			$server->setStarttime($detailArray->starttime);
			$server->setProvider($detailArray->provider);
			$server->setRegion($detailArray->region);
			$server->setCmserver($detailArray->cmserver);
			$server->setAssetname($detailArray->assetname);
			$server->setInstancesize($detailArray->instancesize);
			$server->setPassword($detailArray->password);
			$server->setSSHPort($detailArray->sshport);
			$server->setTier($detailArray->tier);
			if($server instanceof ElasticBeanStalkServer){
				$server->setEnvironmentName($detailArray->environmentName);
				$server->setBucketName($detailArray->bucketName);
				$server->setApplicationName($detailArray->applicationName);
				$server->setTemplateName($detailArray->templateName);
				
				
			}
			return $server;
		
//	trigger_error("Server does not exist", E_USER_ERROR);
	
 }
 
 
  /**
   * This method can be used to add or register a server to cloudmunch data .
   * @param \CloudMunch\Server $server
   * @param string $docker
   */
 function addServer($server,$serverstatus,$docker = false){
 	
 	if(empty($assetStatus)){
 		$this->logHelper->log (ERROR, "Server status need to be provided");
 		return false;
 	}
 	$statusconArray=array(STATUS_RUNNING,STATUS_STOPPED,STATUS_NIL);
 	if(in_array ( $serverstatus ,$statusconArray )){
 	
 	}else{
 		$this->logHelper->log (ERROR, "Invalid status");
 		return false;
 	}
 	
 	
	/* $serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/assets/".$servername;
 	loghandler(INFO,"serverurl from serverhelper:" . $serverurl);
 	$deployArray = $this->cmDataManager->getDataForContext($serverurl, $this->appContext->getAPIKey());
	
	$deployArray = json_decode($deployArray);
	$detailArray=$deployArray->data;
	
	if ($deployArray == null) {
		$deployArray = array ();
	} */
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
		"type" => "server",
		"status" => $server->getStatus(),
		"starttime" => $server->getStarttime(),
		"provider" => $server->getProvider(),
		"region" => $server->getRegion(),
		"cmserver" => $server->getCmserver(),
		"name" => $server->getServerName(),
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
	$dataArray[status]=$serverstatus;
	if($docker){
		$dataArray[projects] = array ($server->getAppName() => array ("buildNo" => $server->getBuild()));
	}


	$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/assets/";
 	$this->cmDataManager->putDataForContext($serverurl,$this->appContext->getAPIKey(),$dataArray);
 }
 
 /**
  * This method is used to update server data.
  * @param \CloudMunch\Server $server
  */
 function updateServer($server,$serverid){
 	
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
		"type" => "server",
		"status" => $server->getStatus(),
		"starttime" => $server->getStarttime(),
		"provider" => $server->getProvider(),
		"region" => $server->getRegion(),
		"cmserver" => $server->getCmserver(),
		"name" => $server->getServerName(),
		"instancesize" => $server->getInstancesize(),
		"password"=>$server->getPassword(),
		"sshport"=>$server->getSSHPort(),
 			"tier"=>$server->getTier()
	);
 	
 	

	$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/assets/".$serverid;
	
	return $this->cmDataManager->updateDataForContext($serverurl,$this->appContext->getAPIKey(),$dataArray);
	
 }
 
 /**
  * This method is to delete server from cloudmunch.
  * @param  $serverName Name of server.
  */
 function deleteServer($assetID){
 	$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/assets/".$assetID;
	
	return $this->cmDataManager->deleteDataForContext($serverurl,$this->appContext->getAPIKey());
 	
 }
 
 /**
  * This method checks if server exists or is registered in cloudmunch data.
  * @param  $servername Name of server.
  * @return boolean
  */
 function checkServerExists($servername){
 	$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/assets/".$servername;
 	$deployArray = $this->cmDataManager->getDataForContext($serverurl, $this->appContext->getAPIKey(),"");
	if($deployArray === false){
		return false;
	}
	//$deployArray = json_decode($deployArray);
	$detailArray=$deployArray->data;

	if ($detailArray == null) {
		return false;
	}else{
		return true;
	}

	
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
	        $this->logHelper->log(ERROR, "Invalid dns" . $dns);
	        return false;
	    }

	    $this->logHelper->log(INFO, "Checking connectivity to: " . $dns);

	    $connection = ssh2_connect($dns, $sshport);
	    if (!$connection) {
	        sleep(10);
	    }

	} while ((!$connection) && (time() < $connectionTimeout));

	if (!$connection) {
	    $this->logHelper->log(ERROR, "Failed to connect to " . $dns);
	    return false;
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
