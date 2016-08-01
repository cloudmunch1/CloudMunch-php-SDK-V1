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

/**
 * 
 * This is a helper class for roles. User can manage roles in cloudmunch using this helper.
 *
 */
class RoleHelper{
	
	private $appContext    = null;
	private $cmDataManager = null;	
	private $logHelper     = null;

	public function __construct($appContext,$logHandler){
		$this->appContext    = $appContext;
		$this->logHelper     = $logHandler;
		$this->cmDataManager = new cmDataManager($this->logHelper, $this->appContext);
	}	
	
   /**
    *   Check if given name of the role is unique with existing ones
    *   @param  string  roleName       :  name of the environment name to be created
    *   @param  string  existingRoles  :  list of existing environments
    *   @return boolean true if name is unique
    */
    public function isRoleNameUnique($existingRoles, $roleName)
    {
        foreach ($existingRoles as $key => $value) {
            if($value->name === $roleName){
                return false;
            }
        }
        return true;
    }

	/**
	 * 
	 * @param  Json Object $filterdata In the format {"filterfield":"=value"}
	 * @return json object roledetails
	 * 
	 */
	function getExistingRoles($filterdata = null)
	{
		$querystring = "";
		if ($filterdata !== null) {
			$querystring = "filter=".json_encode($filterdata);
		}

		$serverurl = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/tiers";
		$roleArray = $this->cmDataManager->getDataForContext($serverurl, $this->appContext->getAPIKey(), $querystring);

		if ($roleArray == false) {
			$this->logHelper->log ( ERROR, "Could not retreive data from cloudmunch");
			return false;
		}
		
		$roledata = $roleArray->data;	
		return	$roledata;
	}

	/**
	 * 
	 * @param  String  $roleID
	 * @param  Json Object $filterdata In the format {"filterfield":"=value"}
	 * @return json object roledetails
	 * 
	 */
	function getRole($roleID, $filterdata)
	{
		$querystring = "";
	
		if ($filterdata !== null) {
			$querystring = "filter=" . json_encode($filterdata);
		}
	
		$serverurl = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/tiers/" . $roleID;
		$roleArray = $this->cmDataManager->getDataForContext($serverurl, $this->appContext->getAPIKey(), $querystring);
		
		if ($roleArray == false) {
			return false;
		}
		
		$roledata = $roleArray->data;
		if ($roledata == null) {
			$this->logHelper->log ( ERROR, "Role does not exist");
			return false;
		}

		return $roledata;
	}

	/**
	 * 
	 * @param string $roleName Name of the role
	 * @param string $role_status Role status ,valid values are success,failed,in-progress
	 * @param array  $roleData Array of role properties
	 */
	function  addRole($roleName, $roleData = null)
	{
		if (empty($roleName)) {
			$this->logHelper->log ( ERROR, "Role name need to be provided");
			return false;
		}
		
		$roleData[name] = $roleName;
		$serverurl      = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/tiers";
		$retArray       = $this->cmDataManager->putDataForContext($serverurl, $this->appContext->getAPIKey(), $roleData);
		if ($retArray === false) {
			return false;
		}
		$retdata = $retArray->data;
		return $retdata;		
	}

	/**
	 * 
	 * @param String Role ID
	 * @param JsonObject Role Data
	 */
	function  updateRole($roleID, $roleData = null)
	{
		$serverurl = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/tiers/" . $roleID;
		
		$this->cmDataManager->putDataForContext($serverurl, $this->appContext->getAPIKey(), $roleData);

	}

	/**
	 * Checks if Role exists in cloudmunch.
	 * @param string $roleID
	 * @return boolean
	 */
	function checkIfRoleExists($roleID)
	{
		$serverurl = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/tiers/" . $roleID;		
		$roleArray = $this->cmDataManager->getDataForContext($serverurl, $this->appContext->getAPIKey(), "");

		if ($roleArray == false) {
			return false;
		}
		
		$roleArray = json_decode(json_encode($roleArray));
		$roleData  = $roleArray->data;

		if ($roleData == null) {
			$this->logHelper->log(INFO, "Role does not exist");
			return false;
		}
		return true;
	}
}