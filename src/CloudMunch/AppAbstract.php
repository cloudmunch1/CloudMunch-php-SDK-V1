<?php


require_once ("cmDataManager.php");
require_once ("AppErrorLogHandler.php");
require_once "Server.php";
require_once "ServerHelper.php";
require_once "AppContext.php";
require_once 'Cloud/CloudServiceHelper.php';
require_once "CloudmunchService.php";

/*
 * Created on 04-Feb-2015
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
/**
 * An abstract base class for Cloudmunch App Object, providing methods to read parameters,
 * create app context object and retreive service objects
 */
abstract class AppAbstract {
	
	private $appContext = null; 
	private $parameterObject=null;
	private $stime=null;
	 abstract function process($processparameters);
	function getInput() {
		$argArray = $_SERVER['argv'];
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
			if (($key !== "cloudproviders") && ($key !== "password")) {
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

	function setAppContext($appContext) {

		$this->appContext = $appContext;

	}

	function getAppContext() {

		return $this->appContext;
	}
	function getCloudmunchServerHelper() {
		$serverhelper = new ServerHelper($this->appContext);
		return $serverhelper;

	}

	function getCloudmunchService() {

		$cloudmunchService = new CloudmunchService($this->appContext);

		return $cloudmunchService;
	}
	function setParameterObject($params){
		$this->parameterObject=$params;
	}
	function getParameterObject(){
		return $this->parameterObject;
	}
	
	public  function initialize(){
		loghandler(INFO, "App execution started"); 
		$date_a = new DateTime();
		$this->stime=$date_a;
		$this->getInput();
	}
	

	
	public function getProcessInput(){
		$cloudservice=null;
		$CloudServiceHelper =new CloudServiceHelper();
		$cloudservice= $CloudServiceHelper->getService($this->getParameterObject());
		
		$processparameters=array("appInput"=>$this->getParameterObject(), "cloudservice"=>$cloudservice);
		return $processparameters;
		
		
	}
	
	public function performAppcompletion(){
	loghandler(INFO, "App completed successfully");
		$date_b=new DateTime();
		$interval = date_diff($this->stime,$date_b);
		loghandler(INFO, "Total time taken: ".$interval->format('%h:%i:%s'));	
	}
	
	public function outputPipelineVariables($variablename,$variable){
		
		echo "\n<{\"" . $variablename . "\":\"" . $variable . "\"}>" . PHP_EOL;
		
	}
	
}
?>
