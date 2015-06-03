<?php


require_once 'Cloud/CloudServiceProvider.php';
require_once 'VSOService.php';
/*
 * Created on 24-Apr-2015
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
  class VSOServiceProvider extends CloudServiceProvider{
 	
 	
 	function getCloudService($jsonParams){
 		
 		$arg1 = 'providername';
		$accountprovider = $jsonParams-> $arg1;
		loghandler(INFO, "Connecting to provider:" . $accountprovider);
 		
 		$arg10 = 'cloudproviders';
		$cloudproviders = $jsonParams-> $arg10;
 		$providerArray = json_decode($cloudproviders);
 		
 		
 		
 		$password = $providerArray-> $accountprovider->password;


$username="username";
$user = $providerArray-> $accountprovider->$username;


$serverurl="serverURL";
$serverurl = $providerArray-> $accountprovider->$serverurl;


$serv=new VSOService();
$serv->setUserName($user);
$serv->setPassword($password);
$serv->setServiceURL($serverurl);
return $serv;
	
 	}
 
 	}
 
?>
