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
		$this->cmDataManager = new cmDataManager($this->logHelper, $appContext);
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
			$this->logHelper->log (DEBUG, "Could not retreive data from cloudmunch");
			return false;
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
			$this->logHelper->log (DEBUG, "Could not retreive data from cloudmunch");
			return false;
		}
		
		$environmentdata = $environmentArray->data;
		if ($environmentdata == null) {
			$this->logHelper->log (DEBUG, "Environment does not exist");
			return false;
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
			 $this->logHelper->log ( DEBUG, "Environment name and status need to be provided");
			 return false;
		}
		$statusconArray = array("success", "failed", "in-progress");
		if (in_array ($environmentStatus, $statusconArray)) {
			
		} else {
			$this->logHelper->log ( DEBUG,"Invalid status provided, valid values are success, failed and in-progress");
			return false;
		}
		
		$environmentData[name]   = $environmentName;
		$environmentData[status] = $environmentStatus;
		$serverurl = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/environments";
		$retArray  = $this->cmDataManager->putDataForContext($serverurl, $this->appContext->getAPIKey(), $environmentData);

		if($retArray === false){
			return false;
		}

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
		
		$this->cmDataManager->updateDataForContext($serverurl, $this->appContext->getAPIKey(), $environmentData);

	}

	/**
	 * 
	 * @param String Environment ID
	 * @param URL    Environment Data
	 */
	function  updateEnvironmentURL($environmentID, $environmentURL)
	{
		if(is_null($environmentURL) || !isset($environmentURL) || empty($environmentURL)){
			$this->logHelper->log(DEBUG, "Please environment URL is not provided to update environment details");
		}
		$data = array("application_url" => $environmentURL);
		$this->updateEnvironment($environmentID, $data);
	}

	/**
	 * 
	 * @param String Environment ID
	 * @param Array  AssetArray
	 * @param String Role Id
	 */
	function  updateAsset($environmentID, $assetArray, $roleID = null)
	{	
		if(is_null($assetArray) || !isset($assetArray) || empty($assetArray)){
			$this->logHelper->log(DEBUG ,"An array of asset ids are excpected for updating asset details to an environment");
			return false;
		}

		if(!is_array($assetArray)){
			$this->logHelper->log(DEBUG ,"An array of asset ids are expected for updating asset details to an environment");
			return false;			
		}

		if(is_null($roleID) || empty($roleID)){
			$filter             = '{"name":"'.$this->defaultRole.'"}';
			$defaultRoleDetails = $this->roleHelper->getExistingRoles($filter);
	
			if(empty($defaultRoleDetails)){
				$this->logHelper->log(INFO, "Role is not provided, creating a default role with name $this->defaultRole");
				$new_role_details = $this->roleHelper->addRole($this->defaultRole);
				$roleID = $new_role_details->id;
				$data   = array('tiers' => array($roleID => array('id' => $roleID, 'name' => $this->defaultRole, 'assets' => $assetArray)));
			} else {
				$this->logHelper->log(INFO, "Role is not provided, linking with default role : $this->defaultRole");
				$roleID = $defaultRoleDetails[0]->id;
				$data   = array('tiers' => array($roleID => array('id' => $roleID, 'name' => $this->defaultRole, 'assets' => $assetArray)));
			}
		} else {
			$name = '{$tiers/' . $roleID . '->name}';
			$data = array('tiers' => array($roleID => array('id' => $roleID, 'name' => $name, 'assets' => $assetArray)));		
		}
		$this->updateEnvironment($environmentID, $data);
	}

	/**
	 * 
	 * @param String environmentID
	 * @param array  key value pairs to be updated to environment details
	 */
	function updateVariables($environmentID, $variables)
	{
		if(is_null($environmentID)){
			$this->logHelper->log (DEBUG, "Environment id value is needed for variables update on an environment");
			return false;
		}		
		$variablesArray = array( 'variables' => $variables);
		$this->updateEnvironment($environmentID, $variablesArray);
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
			$this->logHelper->log (DEBUG, "Invalid status provided, valid values are success, failed and in-progress");
			return false;
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
			$this->logHelper->log (DEBUG, "Could not retreive data from cloudmunch");
			return false;
		}

		$environmentArray = json_decode(json_encode($environmentArray));
		$environmentdata  = $environmentArray->data;

		if ($environmentdata == null) {
			$this->logHelper->log (INFO, "Environment does not exist");
			return false;
		}
		return true;
	}
}