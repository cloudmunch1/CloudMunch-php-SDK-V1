<?php
namespace CloudMunch\Integrations;
/*
 * Created on 09-Feb-2015
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
  class IntegrationHelper{
 	
 	function getService($jsonParams){
 		
 		$arg10 = 'cloudproviders';
		$cloudproviders = $jsonParams-> $arg10;
		$cloudproviders=json_decode($cloudproviders);
		$arg1 = 'providername';
		$provname = $jsonParams-> $arg1;
		loghandler(DEBUG, "Provider Name: ".$provname);
	    $provtype="providerType";
	    
	   
	    if(($provname != null) && (strlen(trim($provname))>0)){
	    $regfields=$cloudproviders->$provname;
	    
	   // $integration= file_get_contents("integration.json");
	   // $integration=json_decode($integration);
	  // $regfields= $integration->$type->registrationFields;
	   $integrationdetails=array();
	    foreach ($regfields as $key=>$value){
	    	$integrationdetails[$key]=$cloudproviders->$provname->$key;
	    	
	    }
	  return $integrationdetails;
	    }else{
	    	return null;
	    }
 		
 	}
 }
?>
