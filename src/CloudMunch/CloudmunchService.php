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
// require_once ("cmDataManager.php");
require_once ("AppErrorLogHandler.php");

/**
 * Class CloudmunchService
 * 
 * @package CloudMunch
 * @author Rosmi
 *         This class provides the service methods for the apps to invoke action on cloudmunch
 */
class CloudmunchService {
	private $appContext = null;
	private $cmDataManager;
	private $keyArray = array ();
	private $logHelper=null;
	public function __construct($appContext,$logHandler) {
		$this->appContext = $appContext;
		$this->logHelper=$logHandler;
		$this->cmDataManager = new cmDataManager ($this->logHelper);
	}
	/**
	 * This method is to invoke notification on cloudmunch.
	 * 
	 * @param string $message
	 *        	: Notification message.
	 * @param string $context
	 *        	: Context for which user is notified.
	 * @param string $id
	 *        	: Name of the object.
	 */
	public function notifyUsers($message, $context, $id) {
		$dataarray = array (
				
				"project" => $this->appContext->getProject (),
				"job" => $this->appContext->getJob (),
				"context" => $context,
				"id" => $id 
		);
		return $this->cmDataManager->notifyUsersInCloudmunch ( $this->appContext->getMasterURL (), $message, $dataarray, $this->appContext->getDomainName () );
	}
	
	/**
	 * Updates data in cloudmunch for the context.
	 * 
	 * @param string $context
	 *        	: Context for which data is to be updated.
	 * @param array $dataArray
	 *        	: Array of data to be updated.
	 */
	public function updateDataContext($context, $dataArray) {
		return $this->cmDataManager->updateContext ( $this->appContext->getMasterURL (), $context, $this->appContext->getDomainName (), $dataArray );
	}
	
	/**
	 * Returns context object.
	 * 
	 * @param string $context
	 *        	: Context for which data is to be retreived.
	 */
	public function getDataFromContext($context) {
		return $this->cmDataManager->getDataForContext ( $this->appContext->getMasterURL (), $context, $this->appContext->getDomainName () );
	}
	
	/**
	 * 
	 * @param string $context Context for which data has to be retrieved.
	 * @param string $contextid ID of the context.
	 * @param array $filterdata Filter data
	 * @return array data
	 */
	public function getCloudmunchData($context,$contextid,$filterdata){
		$querystring="";
		if($filerdata !== null){
			$querystring="filter=".json_encode($filerdata);
		}
		$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/".$context."/".$contextid;
		
		$dataArray = $this->cmDataManager->getDataForContext($serverurl, $this->appContext->getAPIKey(),$querystring);
		if($dataArray == false){
			trigger_error ( "Could not retreive data from cloudmunch", E_USER_ERROR );
		}
		
		//$assetArray = json_decode($assetArray);
		$data=$dataArray->data;
		if($data == null){
			trigger_error ( "Data does not exist", E_USER_ERROR );
		}
		return $data;
		
	}
	
	/**
	 * 
	 * @param string $context Context for which data has to be updated.
	 * @param string $contextid ID of the context.
	 * @param array $data Data to be updated
	 * @return array data
	 */
	public function updateCloudmunchData($context,$contextid,$data){
		$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/".$context."/";
		if(empty($contextid)){
			$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/".$context."/";
		}else{
			$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/".$context."/".$contextid;
		}
		$retArray=$this->cmDataManager->updateDataForContext($serverurl,$this->appContext->getAPIKey(),$data);
		$retdata=$retArray->data;
		return $retdata;
	}
	
	/**
	 *
	 * @param string $context Context for which data has to be added.
	 * @param array $data Data to be updated
	 * @return array data
	 */
	public function addCloudmunchData($context,$data){
		$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/".$context."/";
		
			
		$retArray=$this->cmDataManager->putDataForContext($serverurl,$this->appContext->getAPIKey(),$data);
		$retdata=$retArray->data;
		return $retdata;
	}
	
	/**
	 * 
	 * @param string $context Context for which data has to be deleted.
	 * @param string $contextid ID of the context.
	 */
	public function deleteCloudmunchData($context,$contextid){
		$serverurl=$this->appContext->getMasterURL()."/applications/".$this->appContext->getProject()."/".$context."/".$contextid;
		$result=$this->cmDataManager->deleteDataForContext($serverurl,$this->appContext->getAPIKey());
	}
	
	
	/**
	 * 
	 * @param string $filekey name of the key field
	 * @param string $context context of the key
	 * @param string $contextid id of the context
	 * @return string location of the downloaded file
	 */
	public function downloadKeys($filekey, $context, $contextid) {
		$url = $serverurl = $this->appContext->getMasterURL () . "/applications/" . $this->appContext->getProject () . "/" . $context . "/" . $contextid;
		$querystring = "file=" . $filekey;
		
		$keyString = $this->cmDataManager->getDataForContext ( $url, $this->appContext->getAPIKey (), $querystring );
		$filename = "keyfile" . rand ();
		$this->appContext->getWorkSpaceLocation ();
		// echo $filename;
		$file = $this->appContext->getWorkSpaceLocation () . "/" . $filename;
		file_put_contents ( $file, $keyString );
		system ( 'chmod 400 ' . $file, $retval );
		array_push ( $this->keyArray, $file );
		return $file;
	}
	
	/**
	 * This method is invoked on app completion to delete teh downloaded keys
	 */
	public function deleteKeys() {
		foreach ( $this->keyArray as $file ) {
			system ( "rm " . $file );
		}
	}
	
	/**
	 * Method to update custom context.
	 * 
	 * @param string $context
	 *        	: custom context name.
	 * @param array $dataArray        	
	 * @param string $id        	
	 */
	public function updateCustomContext($context, $dataArray, $id) {
		return $this->cmDataManager->updateCustomContext ( $this->appContext->getMasterURL (), $context, $this->appContext->getDomainName (), $dataArray, $id );
	}
	
	/**
	 * This function accepts data in array format and converts to url string
	 *
	 * Example :
	 *
	 * array(
	 * 'action' => 'listcustomcontext',
	 * 'domain' => 'test',
	 * 'project' => 'projectname',
	 * 'customcontext' => 'projectname_stories',
	 * 'fields' => 'sum(story_points)',
	 * 'username' => 'CI',
	 * 'group_by' => 'fix_versions',
	 * 'count' => '*',
	 * 'filter' => "{\"fix_versions\":\"10\"}"
	 * );
	 * 
	 * @param $context Data
	 *        	to be passed.
	 */
	public function getDataFromCustomContext($context) {
		return $this->cmDataManager->getDataForCustomContext ( $this->appContext->getMasterURL (), $context );
	}
}
?>
