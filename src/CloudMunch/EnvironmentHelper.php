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
	 * @param  String  $environment_id
	 * @param  Json Object $filterdata In the format {"filterfield":"=value"}
	 * @return json object environmentdetails
	 * 
	 */
	function getEnvironment($environment_id, $filterdata)
	{
		$querystring = "";
	
		if ($filterdata !== null) {
			$querystring = "filter=" . json_encode($filterdata);
		}
	
		$serverurl        = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/environments/" . $environment_id;
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
	 * @param string $environment_name Name of the environment
	 * @param string $environment_status Environment status ,valid values are success,failed,in-progress
	 * @param array  $environmentData Array of environment properties
	 */
	function  addEnvironment($environment_name, $environment_status, $environmentData)
	{
		if (empty($environment_name) || (empty($environment_status))) {
			trigger_error ( "Environment name and status need to be provided", E_USER_ERROR );
		}
		$statusconArray = array("success", "failed", "in-progress");
		if (in_array ($environment_status, $statusconArray)) {
			
		} else {
			trigger_error ( "Invalid status provided, valid values are success, failed and in-progress", E_USER_ERROR );
		}
		
		$environmentData[name]   = $environment_name;
		$environmentData[status] = $environment_status;
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
	function  updateEnvironment($environment_id, $environmentData)
	{
		$serverurl = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/environments/" . $environment_id;
		
		$this->cmDataManager->putDataForContext($serverurl, $this->appContext->getAPIKey(), $environmentData);

	}

	/**
	 * 
	 * @param String Environment ID
	 * @param JsonObject Environment Data
	 */
	function  updateAsset($environment_id, $asset_id, $role_id = null)
	{	
		if(is_null($asset_id) || !isset($asset_id) || empty($asset_id)){
			trigger_error("Asset id is not provided for updating asset details to environment", E_USER_ERROR);
		}

		if(is_null($role_id) || empty($role_id)){
			$filter             = '{"name":"'.$this->defaultRole.'"}';
			$defaultRoleDetails = $this->roleHelper->getExistingRoles($filter);
	
			if(empty($defaultRoleDetails)){
				$this->logHelper->log(INFO, "Role is not provided, creating a default role with name $this->defaultRole");
				$new_role_details = $this->roleHelper->addRole($this->defaultRole);
				$role_id          = $new_role_details->id;
				$assetArray       = array('tiers' => array($role_id => array('id' => $role_id, 'name' => $this->defaultRole, 'assets' => array($asset_id))));
			} else {
				$this->logHelper->log(INFO, "Role is not provided, linking with default role : $this->defaultRole");
				$role_id = $defaultRoleDetails[0]->id;
				$assetArray = array('tiers' => array($role_id => array('id' => $role_id, 'name' => $this->defaultRole, 'assets' => array($asset_id))));
			}
		} else {
			$assetArray = array('tiers' => array($role_id => array('id' => $role_id, 'name' => '{$tiers/{id}->name}', 'assets' => array($asset_id))));		
		}
		$this->updateEnvironment($environment_id, $assetArray);
	}


	/**
	 * 
	 * @param String Environment ID
	 * @param String Environment status
	 */
	function updateStatus($environment_id, $status)
	{
		$statusconArray = array("success", "in-progress", "failed");

		if (in_array ( $status ,$statusconArray )) {
		
		} else {
			trigger_error ( "Invalid status provided, valid values are success, failed and in-progress", E_USER_ERROR );
		}
		
		$statusArray = array("status" => $status);
		$this->updateEnvironment($environment_id, $statusArray);
	}

	/**
	 * Checks if Environment exists in cloudmunch.
	 * @param string $environment_id
	 * @return boolean
	 */
	function checkIfEnvironmentExists($environment_id)
	{
		$serverurl = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/environments/" . $environment_id;
		
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