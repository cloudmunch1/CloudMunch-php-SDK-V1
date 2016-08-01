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
		$this->cmDataManager = new cmDataManager ($this->logHelper, $this->appContext);
	}
	/**
	 * This method is to send notification on a selected channel
	 * 
	 * @param string $message
	 *        	: Notification message.
 	 * @param string $channel
	 *        	: Channel to send notification to
	 * @param string $to 
	 *        	: To addresses to be notified
	 * @param string $subject - optional
	 *        	: Subject of the notification (incase of mail)
	 * @param string $attachment - optional
	 *        	: Attachment of the notification
	 */
	public function sendNotification($message, $channel, $to, $subject = "", $attachment = "") {
		if(empty($message) || empty($channel) || empty($to) ){
			$this->logHelper->log ( ERROR, "Message, channel and a to list is mandatory to send a notification" );
			return false;
		}
		$dataarray = array (
			"body" => $message,
			"channel" => $channel,
			"to" => $to,
			"subject" => $subject,
			"attachment" => $attachment 
		);
		return $this->cmDataManager->sendNotification ( $this->appContext->getMasterURL (), $this->appContext->getAPIKey(), $dataarray );
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
	 * @param array $contextArray associative array with key as context and value as its id
	 * @param array $data Data to be updated
	 * @return array data
	 */
	public function updateCustomContextData($contextArray, $data = null, $method = "PATCH"){
		if (is_null($data)) {
			$this->logHelper->log ( ERROR, "Data needs to be provided to update a context" );
			return false;
		}
		//echo "inside update context data";
		//var_dump($contextArray);

		if (is_array($contextArray) && count($contextArray) > 0) {
			$serverurl = $this->appContext->getMasterURL()."/applications/".$this->appContext->getProject();

			foreach($contextArray as $key => $value) {
				if(!is_null($value) && !empty($value)) {
					$serverurl = $serverurl."/".$key."/".$value;
				} else {
					$serverurl = $serverurl."/".$key;
					break;
				}
			}
		} else {
			$this->logHelper->log ( ERROR, "First parameter is expected to be an array with key value pairs" );
			return false;
		}

		if ($method === "POST") {
			$retArray = $this->cmDataManager->putDataForContext($serverurl,$this->appContext->getAPIKey(),$data);
		} else {
			$retArray = $this->cmDataManager->updateDataForContext($serverurl,$this->appContext->getAPIKey(),$data);
		}
		
		if($retArray === false){
			return false;
		}

		//var_dump($retArray);
		return $retArray->data;
	}

	/**
	 * 
	 * @param array $contextArray associative array with key as context and its id as value
	 * @param array $queryParams query paramters 
	 * @return array data
	 */
	public function getCustomContextData($contextArray, $queryParams){
		$querystring = "";
		//echo "inside get context data";
		//var_dump($contextArray);
		if (is_array($contextArray) && count($contextArray) > 0){
			$serverurl = $this->appContext->getMasterURL()."/applications/".$this->appContext->getProject();
			
			foreach ($contextArray as $key => $value) {
				if( !is_null($value) && !empty($value))
				{
					$serverurl = $serverurl."/".$key."/".$value;
				} else {
					$serverurl = $serverurl."/".$key;
					break;
				}
			}

			if (is_array($queryParams) && ($querySize = count($queryParams)) > 0) {
				$i = 1;

				foreach ($queryParams as $key => $value) {
					if($key === "filter"){
						$value = urlencode(json_encode($value));

					}

					if($querystring !== ""){
						$querystring = $key."=".$value."&".$querystring;
					} else {
						$querystring = $key."=".$value;
					}
				}
			} 
		} else {
			$this->logHelper->log ( ERROR, "First parameter is expected to be an array with key value pairs" );
			return false;
		}
		//echo "\nserverurl : $serverurl\n";
		//echo "\nquerystring : $querystring\n";
		$dataArray = $this->cmDataManager->getDataForContext($serverurl, $this->appContext->getAPIKey(),$querystring);
		
		if($dataArray == false){
			$this->logHelper->log ( ERROR, "Could not retreive data from cloudmunch" );
			return false;
		}
		
		//var_dump($dataArray);

		return $dataArray->data;
		
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
			$this->logHelper->log ( ERROR, "Could not retreive data from cloudmunch" );
			return false;
		}
		
		//$assetArray = json_decode($assetArray);
		$data=$dataArray->data;
		if($data == null){
			$this->logHelper->log (ERROR, "Data does not exist" );
			return false;
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
		$retArray = $this->cmDataManager->updateDataForContext($serverurl,$this->appContext->getAPIKey(),$data);
		
		if($retArray === false){
			return false;
		}

		$retdata  = $retArray->data;
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

		if($retArray === false){
			return false;
		}

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
		if($result === false){
			return false;
		}
		return $result;
	}
	
	/**
	 *
	 * @param string $filekey name of the key field
	 * @param string $context context of the key
	 * @param string $contextid id of the context
	 * @return string location of the downloaded file
	 */
	public function downloadGCRKeys($filekey, $context, $contextid) {
		$url = $serverurl = $this->appContext->getMasterURL () . "/applications/" . $this->appContext->getProject () . "/" . $context . "/" . $contextid;
		$querystring = "file=" . $filekey;
	
		$keyString = $this->cmDataManager->downloadGSkey ( $url, $this->appContext->getAPIKey (), $querystring );
		
		
		
		if($keyString === false){
			return false;
		}
	
		if(empty($keyString) || !(strlen($keyString) > 0)){
			$this->logHelper->log(ERROR, "downloaded key content is empty, please re-upload key and try");
			return false;
		}
	
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
        if(!is_string($keyString)){
        	$keyString=json_encode($keyString);
        }
		if($keyString === false){
			return false;
		}
		
		if(empty($keyString) || !(strlen($keyString) > 0)){
			$this->logHelper->log(ERROR, "downloaded key content is empty, please re-upload key and try");
			return false;
		}

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
			if(file_exists($file)){
				system ( "rm " . $file );
			}
		}
	}
}
?>
