<?php
/**
 *  (c) CloudMunch Inc.
 *  All Rights Reserved
 *  Un-authorized copying of this file, via any medium is strictly prohibited
 *  Proprietary and confidential
 *
 *  Rosmi Chandy rosmi@cloudmunch.com 09-Feb-2015
 */
namespace CloudMunch\Integrations;

/**
 * This helper file process the cloudproviders input to get the selected provider details.
 * @author rosmi
 *
 */
  class IntegrationHelper{
  	private $logHelper=null;
  	
  	/**
  	 * This method process plugin input to retreive the provider details.
  	 * @param  $jsonParams Input parameters to the plugin in json format.
  	 * @return array $integrationdetails Array containing credentials to connect to the provider.
  	 *         
  	 */
 	
  	
  	public function __construct($logHandler){
  	 $this->logHelper=	$logHandler;
  	}
 	function getService($jsonParams){
 		
 		$arg10 = 'cloudproviders';
		$cloudproviders = $jsonParams-> $arg10;
		$cloudproviders=json_decode($cloudproviders);
		$arg1 = 'providername';
		$provname = $jsonParams-> $arg1;
		//$this->logHelper->log(DEBUG, "Provider Name: ".$provname);
	    $provtype="providerType";
	    
	   
	    if(($provname != null) && (strlen(trim($provname))>0)){
	    $regfields=$cloudproviders->$provname;
	    
	   // $integration= file_get_contents("integration.json");
	   // $integration=json_decode($integration);
	  // $regfields= $integration->$type->registrationFields;
	   $integrationdetails=array();
	    foreach ($regfields as $key=>$value){
	    	$integrationdetails[$key]=$value;
	    	
	    }
	  return $integrationdetails;
	    }else{
	    	return null;
	    }
 		
 	}
 	
 	function getIntegration($jsonParams,$integrations){
 		$arg1 = 'providername';
 		$provname = $jsonParams-> $arg1;
 		
 	//	$this->logHelper->log(DEBUG, "Provider Name: ".$provname);
 		
 		
 		if(($provname != null) && (strlen(trim($provname))>0)){
 			//$tpe="type";
 			$conf="configuration";
 			//$type=$integrations->$provname->$tpe;
 			$regfields=$integrations->$provname->$conf;
 			$integrationdetails=array();
 			foreach ($regfields as $key=>$value){
 				$integrationdetails[$key]=$value;
 			
 			}
 			return $integrationdetails;
 		
 		}else{
 			return null;
 		}
 		
 	}
 	
 	function getIntegrationData($cloudmunchservice,$jsonParams){
 		$arg1 = 'providername';
 		$provname = $jsonParams-> $arg1;
 		$contextArray = array('integrations' => $provname);
 		$data = $cloudmunchservice->getCustomContextData($contextArray, null);
 		if ($data->configuration){
 			$regfields= $data->configuration;
 			$integrationdetails=array();
 			foreach ($regfields as $key=>$value){
 				$integrationdetails[$key]=$value;
 		
 			}
 			return $integrationdetails;
 		} else {
 			return null;
 		}
 	}
 }
?>
