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
use CloudMunch\Cloud\CloudServiceHelper;
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
	
	private $appContext = null; 
	private $parameterObject=null;
	private $stime=null;
    abstract function process($processparameters);
    
	 /**
	  * This method read and processcthe input parameters
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

			}

		}

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
		return $this->setParameterObject($jsonParams);
	}

	/**
	 * This method sets the plugin context object that contains all environment variables.
	 */
	function setAppContext($appContext) {
		$this->appContext = $appContext;
	}
	
    /**
     *This method returns the  plugin context object that contains all environment variables.
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
	 */
	function getCloudmunchServerHelper() {
		$serverhelper = new ServerHelper($this->appContext);
		return $serverhelper;
	}

	/**
	 * This method returns reference to CloudmunchService,this helper class has all the methods to get/set data to cloudmunch service.
	 */
	function getCloudmunchService() {
		$cloudmunchService = new CloudmunchService($this->appContext);
		return $cloudmunchService;
	}
	
	/**
	 * Set parameter object.
	 */
	function setParameterObject($params){
		$this->parameterObject=$params;
	}
	
	/**
	 * Get parameter object.
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
	 * @return An array containing pluginiput parameters and integration details if any.
	 */
	public function getProcessInput(){
		$cloudservice=null;
		$CloudServiceHelper =new CloudServiceHelper();
		$cloudservice= $CloudServiceHelper->getService($this->getParameterObject());
		$integrationHelper=new IntegrationHelper();
		$integrationService=$integrationHelper->getService($this->getParameterObject());
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
	 * @param variablename: Name of the variable to be output.
	 * @param variable : Value of the variable.
	 */
	public function outputPipelineVariables($variablename,$variable){
		echo "\n<{\"" . $variablename . "\":\"" . $variable . "\"}>" . PHP_EOL;
	}

	
	
}
?>
