<?php

/*
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

require_once ("AppErrorLogHandler.php");


/**
 * 
 * This is a helper class for environments. User can manage environments in cloudmunch using this helper.
 *
 */
class EnvironmentHelper{
	
	private $appContext    = null;
	private $cmDataManager = null;	
	private $logHelper     = null;
	private $roleHelper    = null;   
	private $defaultRole   = "Area_51";

	public function __construct($appContext,$logHandler){
		$this->appContext    = $appContext;
		$this->logHelper     = $logHandler;
		$this->cmDataManager = new cmDataManager($this->logHelper);
		$this->roleHelper    = new RoleHelper($appContext,$this->logHelper);
	}	
	
	/**
	 * 
	 * @param  Json Object $filterdata In the format {"filterfield":"=value"}
	 * @return json object environmentdetails
	 * 
	 */
	function getExistingEnvironments($filterdata = null)
	{
		$querystring = "";

		if ($filterdata !== null) {
			$querystring = "filter=".json_encode($filterdata);
		}
		$serverurl = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/environments";
		
		$environmentArray = $this->cmDataManager->getDataForContext($serverurl, $this->appContext->getAPIKey(), $querystring);
		if ($environmentArray == false) {
			trigger_error ( "Could not retreive data from cloudmunch", E_USER_ERROR );
		}
		
		$environmentdata = $environmentArray->data;	
		return	$environmentdata;
	}

	/**
	 * 
	 * @param  String  $environmentID
	 * @param  Json Object $filterdata In the format {"filterfield":"=value"}
	 * @return json object environmentdetails
	 * 
	 */
	function getEnvironment($environmentID, $filterdata)
	{
		$querystring = "";
	
		if ($filterdata !== null) {
			$querystring = "filter=" . json_encode($filterdata);
		}
	
		$serverurl        = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/environments/" . $environmentID;
		$environmentArray = $this->cmDataManager->getDataForContext($serverurl, $this->appContext->getAPIKey(), $querystring);
		
		if ($environmentArray == false) {
			trigger_error ( "Could not retreive data from cloudmunch", E_USER_ERROR );
		}
		
		$environmentdata = $environmentArray->data;
		if ($environmentdata == null) {
			trigger_error ( "Environment does not exist", E_USER_ERROR );
		}

		return $environmentdata;
	}

	/**
	 * 
	 * @param string $environmentName Name of the environment
	 * @param string $environmentStatus Environment status ,valid values are success,failed,in-progress
	 * @param array  $environmentData Array of environment properties
	 */
	function  addEnvironment($environmentName, $environmentStatus, $environmentData)
	{
		if (empty($environmentName) || (empty($environmentStatus))) {
			trigger_error ( "Environment name and status need to be provided", E_USER_ERROR );
		}
		$statusconArray = array("success", "failed", "in-progress");
		if (in_array ($environmentStatus, $statusconArray)) {
			
		} else {
			trigger_error ( "Invalid status provided, valid values are success, failed and in-progress", E_USER_ERROR );
		}
		
		$environmentData[name]   = $environmentName;
		$environmentData[status] = $environmentStatus;
		$serverurl = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/environments";
		$retArray  = $this->cmDataManager->putDataForContext($serverurl, $this->appContext->getAPIKey(), $environmentData);
		$retdata   = $retArray->data;
		return $retdata;
		
	}

	/**
	 * 
	 * @param String Environment ID
	 * @param JsonObject Environment Data
	 */
	function  updateEnvironment($environmentID, $environmentData)
	{
		$serverurl = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/environments/" . $environmentID;
		
		$this->cmDataManager->putDataForContext($serverurl, $this->appContext->getAPIKey(), $environmentData);

	}

	/**
	 * 
	 * @param String Environment ID
	 * @param URL    Environment Data
	 */
	function  updateEnvironmentURL($environmentID, $environmentURL)
	{
		if(is_null($environmentURL) || !isset($environmentURL) || empty($environmentURL)){
			trigger_error("Please environment URL is not provided to update environment details", E_USER_ERROR);
		}
		$data = array("application_url" => $environmentURL);
		$this->updateEnvironment($environmentID, $data);
	}

	/**
	 * 
	 * @param String Environment ID
	 * @param JsonObject Environment Data
	 */
	function  updateAsset($environmentID, $assetID, $roleID = null)
	{	
		if(is_null($assetID) || !isset($assetID) || empty($assetID)){
			trigger_error("Asset id is not provided for updating asset details to environment", E_USER_ERROR);
		}

		if(is_null($roleID) || empty($roleID)){
			$filter             = '{"name":"'.$this->defaultRole.'"}';
			$defaultRoleDetails = $this->roleHelper->getExistingRoles($filter);
	
			if(empty($defaultRoleDetails)){
				$this->logHelper->log(INFO, "Role is not provided, creating a default role with name $this->defaultRole");
				$new_role_details = $this->roleHelper->addRole($this->defaultRole);
				$roleID          = $new_role_details->id;
				$assetArray       = array('tiers' => array($roleID => array('id' => $roleID, 'name' => $this->defaultRole, 'assets' => array($assetID))));
			} else {
				$this->logHelper->log(INFO, "Role is not provided, linking with default role : $this->defaultRole");
				$roleID = $defaultRoleDetails[0]->id;
				$assetArray = array('tiers' => array($roleID => array('id' => $roleID, 'name' => $this->defaultRole, 'assets' => array($assetID))));
			}
		} else {
			$assetArray = array('tiers' => array($roleID => array('id' => $roleID, 'name' => '{$tiers/{id}->name}', 'assets' => array($assetID))));		
		}
		$this->updateEnvironment($environmentID, $assetArray);
	}


	/**
	 * 
	 * @param String Environment ID
	 * @param String Environment status
	 */
	function updateStatus($environmentID, $status)
	{
		$statusconArray = array("success", "in-progress", "failed");

		if (in_array ( $status ,$statusconArray )) {
		
		} else {
			trigger_error ( "Invalid status provided, valid values are success, failed and in-progress", E_USER_ERROR );
		}
		
		$statusArray = array("status" => $status);
		$this->updateEnvironment($environmentID, $statusArray);
	}

	/**
	 * Checks if Environment exists in cloudmunch.
	 * @param string $environmentID
	 * @return boolean
	 */
	function checkIfEnvironmentExists($environmentID)
	{
		$serverurl = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/environments/" . $environmentID;
		
		$environmentArray = $this->cmDataManager->getDataForContext($serverurl, $this->appContext->getAPIKey(), "");
		if ($environmentArray == false) {
			trigger_error ( "Could not retreive data from cloudmunch", E_USER_ERROR );
		}

		$environmentArray = json_decode(json_encode($environmentArray));
		$environmentdata  = $environmentArray->data;

		if ($environmentdata == null) {
			$this->logHelper->log(INFO, "Environment does not exist");
			return false;
		}
		return true;
	}
}