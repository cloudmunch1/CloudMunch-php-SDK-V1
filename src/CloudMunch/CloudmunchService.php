<?php

namespace CloudMunch;
use CloudMunch\cmDataManager;
//require_once ("cmDataManager.php");
require_once ("AppErrorLogHandler.php");



/**
 * Class CloudmunchService
 * @package CloudMunch
 * @author Rosmi
 * This class provides the service methods for the apps to invoke action on cloudmunch
 */

class CloudmunchService {
	private $appContext = null;
	private $cmDataManager;
	public function __construct($appContext) {
		$this->appContext = $appContext;
		$this->cmDataManager = new cmDataManager();
	}

	public function notifyUsers($message, $context, $id) {
		$dataarray = array (
		
			"project" => $this->appContext->getProject(),
			"job" => $this->appContext->getJob(),
			"context" => $context,
			"id" => $id
		);
		notifyUsersInCloudmunch($this->appContext->getMasterURL(), $message, $dataarray, $this->appContext->getDomainName());

		loghandler(INFO, "Notification send");

	}
	
public function updateDataContext( $context, $dataArray){
		updateContext($this->appContext->getMasterURL(), $context, $this->appContext->getDomainName(), $dataArray);
	}
	
	public function getDataFromContext($context){
		return getDataForContext($this->appContext->getMasterURL(), $context, $this->appContext->getDomainName());
		
	}
	
	public function updateCustomContext($context, $dataArray,$id){
		updateCustomContext($this->appContext->getMasterURL(), $context, $this->appContext->getDomainName(), $dataArray,$id);
	}

	/*
	* This function accepts data in array format and converts to url string
	*
	* Example : 
	*
	* 	array(
	*		'action' => 'listcustomcontext',
	*		'domain' => 'test',
	*		'project' => 'projectname',
	*		'customcontext' => 'projectname_stories',
	*		'fields' => 'sum(story_points)',
	*		'username' => 'CI',
	*		'group_by' => 'fix_versions',
	*		'count' => '*',
	*		'filter' => "{\"fix_versions\":\"10\"}"
	*	);
	* 
	*/
	public function getDataFromCustomContext($context) {
		return $this->cmDataManager->getDataForContext($this->appContext->getMasterURL(), $context);
	}
}
?>
