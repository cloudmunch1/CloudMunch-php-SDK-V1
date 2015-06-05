<?php
namespace CloudMunch\Cloud;
/*
 * Created on 09-Feb-2015
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
  class CloudServiceHelper{
 	
 	function getService($jsonParams){
 		
 		$arg10 = 'cloudproviders';
		$cloudproviders = $jsonParams-> $arg10;
		$cloudproviders=json_decode($cloudproviders);
		$arg1 = 'providername';
		$provname = $jsonParams-> $arg1;
		loghandler(DEBUG, "Provider Name: ".$provname);
	    $provtype="providerType";
	    if(($provname != null) && (strlen(trim($provname))>0)){
	    $type=$cloudproviders->$provname->$provtype;
	   // $type="amazon";
	    loghandler(DEBUG, "ProviderType: ".$type);
	    switch($type){
	    	case "amazon":
	    	require_once 'AWS/AWSServiceProvider.php';
	    	 $awsService=new AWSServiceProvider();
	    	 $cloudService=$awsService->getCloudService($jsonParams);
	    	 return $cloudService;
	    	 case "azure":
	    	require_once 'Azure/AzureServiceManagementProvider.php';
	    	 $azureService=new AzureServiceManagementProvider();
	    	 $cloudService=$azureService->getCloudService($jsonParams);
	    	 return $cloudService;
	    	 case "vchs":
	    	 require_once 'VMWare/VMWareServiceProvider.php';
	    	 $vmwareService=new VMWareServiceProvider();
	    	 $cloudService=$vmwareService->getCloudService($jsonParams);
	    	 return $cloudService;
	    	 case "VSO":
		    	 require_once 'VSO/VSOServiceProvider.php';
		    	 $vsoService=new VSOServiceProvider();
		    	 $cloudService=$vsoService->getCloudService($jsonParams);
		    	 return $cloudService;
	    }
	    }else{
	    	return null;
	    }
 		
 	}
 }
?>
