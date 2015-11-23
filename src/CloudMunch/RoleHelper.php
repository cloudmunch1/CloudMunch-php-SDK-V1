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
		$this->cmDataManager = new cmDataManager($this->logHelper);
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
			trigger_error ( "Could not retreive data from cloudmunch", E_USER_ERROR );
		}
		
		$roledata = $roleArray->data;	
		return	$roledata;
	}

	/**
	 * 
	 * @param  String  $role_id
	 * @param  Json Object $filterdata In the format {"filterfield":"=value"}
	 * @return json object roledetails
	 * 
	 */
	function getRole($role_id, $filterdata)
	{
		$querystring = "";
	
		if ($filterdata !== null) {
			$querystring = "filter=" . json_encode($filterdata);
		}
	
		$serverurl = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/tiers/" . $role_id;
		$roleArray = $this->cmDataManager->getDataForContext($serverurl, $this->appContext->getAPIKey(), $querystring);
		
		if ($roleArray == false) {
			trigger_error ( "Could not retreive data from cloudmunch", E_USER_ERROR );
		}
		
		$roledata = $roleArray->data;
		if ($roledata == null) {
			trigger_error ( "Role does not exist", E_USER_ERROR );
		}

		return $roledata;
	}

	/**
	 * 
	 * @param string $role_name Name of the role
	 * @param string $role_status Role status ,valid values are success,failed,in-progress
	 * @param array  $roleData Array of role properties
	 */
	function addRole($role_name, $roleData = null)
	{
		if (empty($role_name)) {
			trigger_error ( "Role name need to be provided", E_USER_ERROR );
		}
		
		$roleData[name] = $role_name;
		$serverurl      = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/tiers";
		$retArray       = $this->cmDataManager->putDataForContext($serverurl, $this->appContext->getAPIKey(), $roleData);
		$retdata        = $retArray->data;
		return $retdata;		
	}

	/**
	 * 
	 * @param String Role ID
	 * @param JsonObject Role Data
	 */
	function  updateRole($role_id, $roleData = null)
	{
		$serverurl = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/tiers/" . $role_id;
		
		$this->cmDataManager->putDataForContext($serverurl, $this->appContext->getAPIKey(), $roleData);

	}

	/**
	 * Checks if Role exists in cloudmunch.
	 * @param string $role_id
	 * @return boolean
	 */
	function checkIfRoleExists($role_id)
	{
		$serverurl = $this->appContext->getMasterURL() . "/applications/" . $this->appContext->getProject() . "/tiers/" . $role_id;		
		$roleArray = $this->cmDataManager->getDataForContext($serverurl, $this->appContext->getAPIKey(), "");

		if ($roleArray == false) {
			trigger_error ( "Could not retreive data from cloudmunch", E_USER_ERROR );
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