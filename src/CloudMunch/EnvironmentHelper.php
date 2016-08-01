<?php

/*
 * (c) CloudMunch Inc.
 * All Rights Reserved
 * Un-authorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * Rosmi Chandy rosmi@cloudmunch.com
 */
namespace CloudMunch;

require_once ("CloudmunchConstants.php");
require_once ("AppErrorLogHandler.php");

/**
 * This is a helper class for environments. User can manage environments in cloudmunch using this helper.
 */
class EnvironmentHelper {
	private $appContext = null;
	private $cmDataManager = null;
	private $logHelper = null;
	private $roleHelper = null;
	private $defaultRole = "default";
	private $defaultStage = "dev";
	public function __construct($appContext, $logHandler) {
		$this->appContext = $appContext;
		$this->logHelper = $logHandler;
		$this->cmDataManager = new cmDataManager ( $this->logHelper, $appContext );
		$this->roleHelper = new RoleHelper ( $appContext, $this->logHelper );
	}
	
	/**
	 *
	 * @param
	 *        	Json Object $filterdata In the format {"filterfield":"=value"}
	 * @return json object environmentdetails
	 *        
	 */
	function getExistingEnvironments($filterdata = null) {
		$querystring = "";
		
		if ($filterdata !== null) {
			$querystring = "filter=" . json_encode ( $filterdata );
		}
		$serverurl = $this->appContext->getMasterURL () . "/applications/" . $this->appContext->getProject () . "/environments";
		
		$environmentArray = $this->cmDataManager->getDataForContext ( $serverurl, $this->appContext->getAPIKey (), $querystring );
		if ($environmentArray == false) {
			$this->logHelper->log ( DEBUG, "Could not retreive data from cloudmunch" );
			return false;
		}
		
		$environmentdata = $environmentArray->data;
		return $environmentdata;
	}
	
	/**
	 *
	 * @param String $environmentID        	
	 * @param
	 *        	Json Object $filterdata In the format {"filterfield":"=value"}
	 * @return json object environmentdetails
	 *        
	 */
	function getEnvironment($environmentID, $filterdata) {
		$querystring = "";
		
		if ($filterdata !== null) {
			$querystring = "filter=" . json_encode ( $filterdata );
		}
		
		$serverurl = $this->appContext->getMasterURL () . "/applications/" . $this->appContext->getProject () . "/environments/" . $environmentID;
		$environmentArray = $this->cmDataManager->getDataForContext ( $serverurl, $this->appContext->getAPIKey (), $querystring );
		
		if ($environmentArray == false) {
			$this->logHelper->log ( DEBUG, "Could not retreive data from cloudmunch" );
			return false;
		}
		
		$environmentdata = $environmentArray->data;
		if ($environmentdata == null) {
			$this->logHelper->log ( DEBUG, "Environment does not exist" );
			return false;
		}
		
		return $environmentdata;
	}
	
	/**
	 *
	 * @param string $environmentName
	 *        	Name of the environment
	 * @param string $environmentStatus
	 *        	Environment status ,valid values are success,failed,in-progress
	 * @param array $environmentData
	 *        	Array of environment properties
	 */
	function addEnvironment($environmentName, $environmentStatus, $environmentData) {
		if (empty ( $environmentName ) || (empty ( $environmentStatus ))) {
			$this->logHelper->log ( DEBUG, "Environment name and status need to be provided" );
			return false;
		}

		$stage = $this->setStage($environmentData);
		
		$environmentData[stage] = [];
		$environmentData[stage] = $stage;

		$statusconArray = array (
				STATUS_CREATION_IN_PROGRESS,
				STATUS_RUNNING,
				STATUS_STOPPED,
				STATUS_STOPPED_WITH_ERRORS,
				STATUS_RUNNING_WITH_WARNINGS,
				STATUS_ACTION_IN_PROGRESS 
		);
		;
		if (in_array ( $environmentStatus, $statusconArray )) {
		} else {
			$this->logHelper->log ( DEBUG, "Invalid status provided, valid values are " . STATUS_CREATION_IN_PROGRESS . ", " . STATUS_RUNNING . ", " . STATUS_STOPPED . ", " . STATUS_ACTION_IN_PROGRESS . ", " . STATUS_RUNNING_WITH_WARNINGS . " and " . STATUS_STOPPED_WITH_ERRORS );
			return false;
		}
		
		$environmentData [name] = $environmentName;
		$environmentData [status] = $environmentStatus;
		
		$comment = "Adding environment with name $environmentName";
		
		$serverurl = $this->appContext->getMasterURL () . "/applications/" . $this->appContext->getProject () . "/environments";
		$retArray = $this->cmDataManager->putDataForContext ( $serverurl, $this->appContext->getAPIKey (), $environmentData, $comment );
		
		if ($retArray === false) {
			return false;
		}
		
		$retdata = $retArray->data;
		$this->appContext->setEnvironment ( $retdata->id );
		return $retdata;
	}
	
	/**
	 *
	 * @param	array  data
	 */

	function setStage($data){
		if (is_array($data) && isset($data['stage']) && !empty($data['stage']) && !is_null($data['stage'])) {
			// return the same if stage is already set with required format
			if (is_array($data['stage']) || is_object($data['stage'])){
				return $data['stage'];
			} else {
				$stage = $this->getStage("id", $data['stage']);
				// if name is set as value
				if (is_null($stage)) {
					$stage = $this->getStage("name", $data['stage']);
				}
				// set to default stage
				if (is_null($stage)) {
					$stage = $this->getStage("name", $this->defaultStage);
				}
				return $stage;
			}
		} else {
			// set to default stage
			return $this->getStage("name", $this->defaultStage);
		}
	}

	/**
	 *
	 * @param
	 *			String  key
	 * @param
	 *        	String 	value
	 */

    function getStage($key, $value){
        $url    = $this->appContext->getMasterURL () . "/applications/" . $this->appContext->getProject () . "/stages" ;
        $data   = $this->cmDataManager->getDataForContext($url, $this->appContext->getAPIKey(), null);
        $stages = $data->data;
        $stageDetails = [];
        if ($stages) {
            foreach ($stages as $keyName => $stage) {
                if ($stage->$key == $value) {
                    $stageDetails[name] = isset($stage->name)?$stage->name:"";
                    $stageDetails[id]   = isset($stage->id)?$stage->id:"";
	                return $stageDetails;
                }
            }
        } else {
        	return false;
        }
    }

	/**
	 *
	 * @param
	 *        	String Environment ID
	 * @param
	 *        	JsonObject Environment Data
	 */
	function updateEnvironment($environmentID, $environmentData, $comment = null) {
		$serverurl = $this->appContext->getMasterURL () . "/applications/" . $this->appContext->getProject () . "/environments/" . $environmentID;
		
		$this->cmDataManager->updateDataForContext ( $serverurl, $this->appContext->getAPIKey (), $environmentData, $comment );
	}
	
	/**
	 *
	 * @param
	 *        	String Environment ID
	 * @param
	 *        	URL Environment Data
	 */
	function updateEnvironmentURL($environmentID, $environmentURL) {
		if (is_null ( $environmentURL ) || ! isset ( $environmentURL ) || empty ( $environmentURL )) {
			$this->logHelper->log ( DEBUG, "Environment URL is not provided to update environment details" );
			return false;
		}
		
		$comment = "Setting application URL";
		$data = array (
				"application_url" => $environmentURL 
		);
		$this->updateEnvironment ( $environmentID, $data, $comment );
	}
	
	/**
	 *
	 * @param
	 *        	String Environment ID
	 * @param
	 *        	URL Environment Data
	 */
	function updateEnvironmentBuildVersion($environmentID, $buildNumber) {
		if (is_null ( $buildNumber ) || ! isset ( $buildNumber ) || empty ( $buildNumber )) {
			$this->logHelper->log ( DEBUG, "Build number is not provided to update environment details" );
			return false;
		}
		
		$comment = "Setting application build version";
		$data = array (
				"application" => array (
						"version" => "",
						"build" => $buildNumber 
				) 
		);
		$this->updateEnvironment ( $environmentID, $data, $comment );
	}
	
	/**
	 *
	 * @param
	 *        	String Environment ID
	 * @param
	 *        	Array AssetArray
	 * @param
	 *        	String Role Id
	 */
	function updateAsset($environmentID, $assetArray, $roleID = null) {
		if (is_null ( $assetArray ) || ! isset ( $assetArray ) || empty ( $assetArray )) {
			$this->logHelper->log ( DEBUG, "An array of asset ids are excpected for updating asset details to an environment" );
			return false;
		}
		
		if (! is_array ( $assetArray )) {
			$this->logHelper->log ( DEBUG, "An array of asset ids are expected for updating asset details to an environment" );
			return false;
		}
		
		if (is_null ( $roleID ) || empty ( $roleID )) {
			$filter = '{"default":"' . "YES" . '"}';
			$defaultRoleDetails = $this->roleHelper->getExistingRoles ( $filter );
			
			if (empty ( $defaultRoleDetails )) {
				$this->logHelper->log ( INFO, "Role is not provided, creating a default role with name $this->defaultRole" );
				$new_role_details = $this->roleHelper->addRole ( $this->defaultRole );
				$roleID = $new_role_details->id;
				$data = array (
						'tiers' => array (
								$roleID => array (
										'id' => $roleID,
										'name' => $this->defaultRole,
										'assets' => $assetArray 
								) 
						) 
				);
			} else {
				$this->logHelper->log ( INFO, "Role is not provided, linking with default role : $this->defaultRole" );
				$rolefound=false;
				foreach($defaultRoleDetails as $defaultRoleDetail){
					if($defaultRoleDetail->name == $this->defaultRole){
						$roleID = $defaultRoleDetail->id;
						$this->logHelper->log ( INFO,"Got the default role id");
						$rolefound=true;
					}
				}
				if(!$rolefound){
					$this->logHelper->log ( INFO,"Creating default role");
					$new_role_details = $this->roleHelper->addRole ( $this->defaultRole );
					$roleID = $new_role_details->id;
				}
				$this->logHelper->log ( INFO,"Role id is:".$roleID);
			//	$roleID = $defaultRoleDetails [0]->id;
				$data = array (
						'tiers' => array (
								$roleID => array (
										'id' => $roleID,
										'name' => $this->defaultRole,
										'assets' => $assetArray 
								) 
						) 
				);
			}
		} else {
			$name = '{$tiers/' . $roleID . '->name}';
			$data = array (
					'tiers' => array (
							$roleID => array (
									'id' => $roleID,
									'name' => $name,
									'assets' => $assetArray 
							) 
					) 
			);
		}
		$comment = "Updating role asset mapping";
		$this->updateEnvironment ( $environmentID, $data, $comment );
	}
	
	/**
	 *
	 * @param
	 *        	String environmentID
	 * @param
	 *        	array key value pairs to be updated to environment details
	 */
	function updateVariables($environmentID, $variables) {
		if (is_null ( $environmentID )) {
			$this->logHelper->log ( DEBUG, "Environment id value is needed for variables update on an environment" );
			return false;
		}
		$variablesArray = array (
				'variables' => $variables 
		);
		$comment = "Updating variables";
		$this->updateEnvironment ( $environmentID, $variablesArray, $comment );
	}
	
	/**
	 *
	 * @param
	 *        	String Environment ID
	 * @param
	 *        	String Environment status
	 */
	function updateStatus($environmentID, $status) {
		$statusconArray = array (
				STATUS_CREATION_IN_PROGRESS,
				STATUS_RUNNING,
				STATUS_STOPPED,
				STATUS_STOPPED_WITH_ERRORS,
				STATUS_RUNNING_WITH_WARNINGS,
				STATUS_ACTION_IN_PROGRESS 
		);
		
		if (in_array ( $status, $statusconArray )) {
		} else {
			$this->logHelper->log ( DEBUG, "Invalid status provided, valid values are " . STATUS_CREATION_IN_PROGRESS . ", " . STATUS_RUNNING . ", " . STATUS_STOPPED . ", " . STATUS_ACTION_IN_PROGRESS . ", " . STATUS_RUNNING_WITH_WARNINGS . " and " . STATUS_STOPPED_WITH_ERRORS );
			return false;
		}
		
		$statusArray = array (
				"status" => $status 
		);
		$comment = "Updating status to $status";
		$this->updateEnvironment ( $environmentID, $statusArray, $comment );
	}
	
	/**
	 * Checks if Environment exists in cloudmunch.
	 * 
	 * @param string $environmentID        	
	 * @return boolean
	 */
	function checkIfEnvironmentExists($environmentID) {
		$serverurl = $this->appContext->getMasterURL () . "/applications/" . $this->appContext->getProject () . "/environments/" . $environmentID;
		
		$environmentArray = $this->cmDataManager->getDataForContext ( $serverurl, $this->appContext->getAPIKey (), "" );
		if ($environmentArray == false) {
			$this->logHelper->log ( DEBUG, "Could not retreive data from cloudmunch" );
			return false;
		}
		
		$environmentArray = json_decode ( json_encode ( $environmentArray ) );
		$environmentdata = $environmentArray->data;
		
		if ($environmentdata == null) {
			$this->logHelper->log ( INFO, "Environment does not exist" );
			return false;
		}
		return true;
	}
	/**
	 *
	 * @param string $environmentID        	
	 * @return array Assetdetails
	 */
	function getAssets($environmentID) {
		$envdetails = $this->getEnvironment ( $environmentID, null );
		$tiers = $envdetails->tiers;
		$assetNames = array ();
		foreach ( $tiers as $tier ) {
			foreach ( $tier as $key => $value ) {
				$assets = $value->assets;
				
				array_merge ( $assetNames, $assets );
			}
		}
		$assetthelper = new AssetHelper ( $this->appContext, $this->logHelper );
		$assetsDetail = array ();
		foreach ( $assetNames as $assetName ) {
			//$this->logHelper->log ( DEBUG, "Retrieve asset:" . $assetName );
			$data = $assetthelper->getAsset ( $assetName, null );
			array_push ( $assetsDetail, $data );
		}
		return $assetsDetail;
	}
	/**
	 *
	 * @param string $environmentID        	
	 * @param string $assetID
	 *        	Deletes the given asset from environment
	 */
	function deleteAsset($environmentID, $assetID) {
		//$this->logHelper->log ( INFO, "Asset id:" . $assetID );
		$envdetails = $this->getEnvironment ( $environmentID, null );
		$tiers = $envdetails->tiers;
		
		$tierbackup = array ();
		$assetNames = array ();
		foreach ( $tiers as $tier ) {
			foreach ( $tier as $key => $value ) {
				$assets = $value->assets;
				if (($key = array_search ( $assetID, $assets )) !== false) {
					unset ( $assets [$key] );
					//$this->logHelper->log ( INFO, "Tier unset:" . $tier );
					$value->assets = $assets;
					
					//$this->logHelper->log ( INFO, "Assets unset" );
				}
				
			}
		}
		$data = array (
				"tiers" => $tiers 
		);
		
		$this->updateEnvironment ( $environmentID, $data, "Deleted asset" . $assetID );
	}
}
