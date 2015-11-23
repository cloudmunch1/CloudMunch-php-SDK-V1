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
 * 
 * This is a helper class for assets. User can manage assets in cloudmunch using this helper.
 *
 */
class AssetHelper{
	
	private $appContext = null;
	private $cmDataManager = null;
	private $logHelper = null;
	
	public function __construct($appContext,$logHandler){
		$this->appContext = $appContext;
		$this->logHelper=$logHandler;
		$this->cmDataManager = new cmDataManager($this->logHelper);
	
	}
	/**
	 * 
	 * @param String  $assetID
	 * $param Json Object $filerdata In the format {"filterfield":"=value"}
	 * @return  json object assetdetails
	 * 
	 */
function getAsset($assetID,$filerdata){
	$querystring="";
	if($filerdata !== null){
		$querystring="filter=".json_encode($filerdata);
	}
	$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/assets/".$assetID;
	
	$assetArray = $this->cmDataManager->getDataForContext($serverurl, $this->appContext->getAPIKey(),$querystring);
	if($assetArray == false){
		trigger_error ( "Could not retreive data from cloudmunch", E_USER_ERROR );
	}
	
	//$assetArray = json_decode($assetArray);
	$assetdata=$assetArray->data;
	if($assetdata == null){
		trigger_error ( "Asset does not exist", E_USER_ERROR );
	}
	return $assetdata;
}

/**
 * 
 * @param string $assetname Name of the asset
 * @param string $assettype Type of asset
 * * @param string $assetStatus Asset status ,valid values are STATUS_RUNNING,STATUS_STOPPED,STATUS_NIL
 * @param array $assetData Array of asset properties
 */
function  addAsset($assetname,$assettype,$assetStatus,$assetExternalRef,$assetData){
	if(empty($assetname)||(empty($assettype))||(empty($assetStatus))){
		trigger_error ( "Asset name ,status and type need to be provided", E_USER_ERROR );
	}
	$statusconArray=array(STATUS_RUNNING,STATUS_STOPPED,STATUS_NIL);
	if(in_array ( $assetStatus ,$statusconArray )){
		
	}else{
		trigger_error ( "Invalid status", E_USER_ERROR );
	}
	
	$assetData[name]=$assetname;
	$assetData[type]=$assettype;
	$assetData[status]=$assetStatus;
	$assetData[external_reference]=$assetExternalRef;
	$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/assets/";
	$retArray=$this->cmDataManager->putDataForContext($serverurl,$this->appContext->getAPIKey(),$assetData);
	$retdata=$retArray->data;
	return $retdata;
	
}

/**
 * 
 * @param String Asset ID
 * @param JsonObject Asset Data
 */
function  updateAsset($assetID,$assetData){
	$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/assets/".$assetID;
	
	$this->cmDataManager->updateDataForContext($serverurl,$this->appContext->getAPIKey(),$assetData);

}

/**
 * 
 * @param String Asset ID
 */
function deleteAsset($assetID){
	$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/assets/".$assetID;
	
	$this->cmDataManager->deleteDataForContext($serverurl,$this->appContext->getAPIKey());
}

/**
 * 
 * @param String Asset ID
 * @param String Asset status
 */
function updateStatus($assetID,$status){
	$statusconArray=array(STATUS_RUNNING,STATUS_STOPPED,STATUS_NIL);
	if(in_array ( $status ,$statusconArray )){
	
	}else{
		trigger_error ( "Invalid status", E_USER_ERROR );
	}
	$statusArray=array("status"=>$status);
	$this->updateAsset($assetID,$statusArray);
}

/**
 * Checks if Asset exists in cloudmunch.
 * @param string $assetID
 * @return boolean
 */
function checkIfAssetExists($assetID){
	$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/assets/".$assetID;
	
	$assetArray = $this->cmDataManager->getDataForContext($serverurl, $this->appContext->getAPIKey(),"");
	if($assetArray == false){
		trigger_error ( "Could not retreive data from cloudmunch", E_USER_ERROR );
	}
	
	$assetArray = json_decode($assetArray);
	$assetdata=$assetArray->data;
	if($assetdata == null){
		$this->logHelper->log(INFO,"Asset does not exist");
		return false;
	}
	return true;
}
}