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
require_once ("AppErrorLogHandler.php");

use CloudMunch\Integrations\IntegrationHelper;
use DateTime;

/**
 * Class AppAbstract
 * @package CloudMunch
 * @author Rosmi <rosmi@cloudmunch.com>
 * An abstract base class for Cloudmunch App Object, providing methods to read parameters,
 * create app context object and retreive service objects
 */
abstract class AppAbstract {
	/**
	 * AppContext 
	 */
	private $appContext = null; 
	
	/**
	 * String containing input parameters.
	 */
	private $parameterObject=null;
	
	/**
	 * Strat time of the plugin execution.
	 */
	private $stime=null;
	
	/**
	 * A boolean to indicate if it is new platform
	 */
	private $newVer=false;
	
	/**
	 * This is an abstract method to be implemented by every plugin.
	 * @param array $processparameters This array contains the two entries , appInput and integrationdetails
	 *	
	 */
    abstract function process($processparameters);
    
	 /**
	  * This method read and process the input parameters.
	  */ 
	function getInput() {
		$argArray = $_SERVER['argv'];
		echo sizeof($argArray);
		for ($i = 0; $i < sizeof($argArray); $i++) {

			switch ($argArray[$i]) {

				case "-jsoninput" :
					{
						
						$jsonParameters = $argArray[$i +1];

						continue;

					}
				case "-variables":
				{
				  $variableParams=$argArray[$i +1];
				 
				  $this->newVer=true;
				}
			
				case "-integrations":{
					  $integrations=$argArray[$i +1];
					 
					  $this->newVer=true;
				}
				
				

			}

		}
        if($this->newVer){
        	$jsonParams = json_decode($jsonParameters);
        	$varParams = json_decode($variableParams);
        	$integrations=json_decode($integrations);
        	$appContext = new AppContext();

    		$arg10 = '{master_url}';
    		$masterurl = $varParams-> $arg10;
    		$appContext->setMasterURL($masterurl);

    		
    		$appContext->setIntegrations($integrations);
    		$arg2 = '{domain}';
    		$domainName = $varParams-> $arg2;
    		$appContext->setDomainName($domainName);

    		$arg6 = '{application}';
    		$projectId = $varParams-> $arg6;
    		$appContext->setProject($projectId);

    		$arg6 = '{ci_job_name}';
    		$jobname = $varParams-> $arg6;
    		$appContext->setJob($jobname);
    		
    		$arg="{workspace}";
    		$workspace = $varParams-> $arg;
    		$appContext->setWokSpaceLocation($workspace);
    		
    		$arg="stepdetails";
    		$stepDetails = $varParams-> $arg;
    		$stepDetails=json_decode($stepDetails);
    		$appContext->setStepID($stepDetails->id);
    		$appContext->setReportsLocation($stepDetails->reports_location);
    		
    		$arg="{archive_location}";
    		$archiveloc = $varParams-> $arg;
    		$appContext->setArchiveLocation($archiveloc);
    		
    		$arg="{server}";
    		$targetServer = $varParams-> $arg;
    		$appContext->setTargetServer($targetServer);
    		
    		$arg="{run}";
    		$run = $varParams-> $arg;
    		$appContext->setRunNumber($run);
    		
    		$arg="{api_key}";
    		$apikey = $varParams-> $arg;
    		$appContext->setAPIKey($apikey);
    		
    		$this->setAppContext($appContext);
        }else{
		$jsonParams = json_decode($jsonParameters);
		foreach ($jsonParams as $key => $value) {
			if (($key !== "cloudproviders") && ($key !== "password") && ($key !== "inputparameters")) {
				loghandler(DEBUG, $key . ": " . $value);
			}
		}

		$appContext = new AppContext();

		$arg10 = 'masterurl';
		$masterurl = $jsonParams-> $arg10;
		$appContext->setMasterURL($masterurl);

		$arg10 = 'cloudproviders';
		$cloudproviders = $jsonParams-> $arg10;
		$appContext->setCloudproviders($cloudproviders);
		$arg2 = 'domain';
		$domainName = $jsonParams-> $arg2;
		$appContext->setDomainName($domainName);

		$arg6 = 'projectName';
		$projectId = $jsonParams-> $arg6;
		$appContext->setProject($projectId);

		$arg6 = 'jobname';
		$jobname = $jsonParams-> $arg6;
		$appContext->setJob($jobname);
		$this->setAppContext($appContext);
        }
		return $this->setParameterObject($jsonParams);
	}

	/**
	 * This method sets the plugin context object that contains all environment variables.
	 * @param AppContext appContext
	 * 
	 */
	function setAppContext($appContext) {
		$this->appContext = $appContext;
	}
	
    /**
     * This method returns the  plugin context object that contains all environment variables.
     * @return AppContext appContext
     * List of environment variables that can be retreived be retreived from App context are,
     * 1.MasterURL: This is the cloudmunch service URL.
     * 2.cloudproviders: This is a json object that has reference to the integrations.
     * 3.domain: Domain to which your project belongs to.
     * 4.ProjectName: Name of the current project.
     * 5.JobName: Name of the current job. 
     */
	function getAppContext() {
		return $this->appContext;
	}
	
	/**
	 * This method gives reference to ServerHelper,this helper class has all the methods to get/set data on 
	 * servers registered with cloudmunch.
	 * @return ServerHelper serverhelper
	 */
	function getCloudmunchServerHelper() {
		$serverhelper = new ServerHelper($this->appContext);
		return $serverhelper;
	}
	
	/**
	 * This method gives reference to AssetHelper,this helper class has all the methods to get/set data on
	 * assets registered with cloudmunch.
	 * @return AssetHelper assethelper
	 */
	function getCloudmunchAssetHelper() {
		$assethelper = new AssetHelper($this->appContext);
		return $assethelper;
	}

	/**
	 * This method returns reference to CloudmunchService,this helper class has all the methods to get/set data to cloudmunch service.
	 * @return CloudmunchService
	 */
	function getCloudmunchService() {
		$cloudmunchService = new CloudmunchService($this->appContext);
		return $cloudmunchService;
	}
	
	/**
	 * Set parameter object.
	 * @param string params : String in json format ,containing plugin input.
	 */
	function setParameterObject($params){
		$this->parameterObject=$params;
	}
	
	/**
	 * Get parameter object.
	 * @return string parameterObject : String in json format ,containing plugin input.
	 */
	function getParameterObject(){
		return $this->parameterObject;
	}
	
	/**
	 * This is a lifecycle method that is invoked on the plugin to initialize itself with the incoming
	 * data.
	 */
	public  function initialize(){
		loghandler(INFO, "App execution started"); 
		$date_a = new DateTime();
		$this->stime=$date_a;
		$this->getInput();
	}
	
	/**
	 * This is a lifecycle method to process input.
	 * @return array processparameters : Array containing pluginiput parameters and integration details if any.
	 */
	public function getProcessInput(){
		$cloudservice=null;
		
		$integrationHelper=new IntegrationHelper();
		if($this->newVer){
			$integrationService=$integrationHelper->getIntegration($this->getParameterObject(),$this->appContext->getIntegrations());
		}else{
		$integrationService=$integrationHelper->getService($this->getParameterObject());
		}
		$processparameters=array("appInput"=>$this->getParameterObject(), "cloudservice"=>$cloudservice,"integrationdetails"=>$integrationService);
		return $processparameters;
	}
	
	/**
	 * This is a lifecycle method invoked at the completion of the plugin to capture some data.
	 */
	public function performAppcompletion(){
	loghandler(INFO, "App completed successfully");
		$date_b=new DateTime();
		$interval = date_diff($this->stime,$date_b);
		loghandler(INFO, "Total time taken: ".$interval->format('%h:%i:%s'));	
	}
	
	/**
	 * This method outputs variables from the plugin
	 * @param string variablename : Name of the variable to be output.
	 * @param string variable : Value of the variable.
	 */
	public function outputPipelineVariables($variablename,$variable){
		if($this->newVer){
			$fileloc=$this->appContext->getReportsLocation()."/".$this->appContext->getStepID().".out";
			$varlist=file_get_contents($fileloc);
			if(($varlist ==null) ||(strlen($varlist)==0)){
				$varlist=array($variablename => $variable);	
				$varlist=json_encode($varlist);
				file_put_contents($fileloc, $varlist);
			}else{
				$varlist=json_decode($varlist);
				$varlist->$variablename=$variable;
				$varlist=json_encode($varlist);
				file_put_contents($fileloc, $varlist);
				
			}
		}else{
		echo "\n<{\"" . $variablename . "\":\"" . $variable . "\"}>" . PHP_EOL;
		}
		
	}

	
	
}
?>
