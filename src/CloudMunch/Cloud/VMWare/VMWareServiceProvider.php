<?php

//set_include_path(".:/usr/share/pear");
set_include_path(implode(PATH_SEPARATOR, array (
	'.',
	'/var/cloudbox/vmwaresdk/library',
	get_include_path(),
	
)));
require_once '/var/cloudbox/vmwaresdk/library/VMware/VCloud/Helper.php';
require_once 'Cloud/CloudServiceProvider.php';
require_once 'VMWareService.php';
/*
 * Created on 24-Apr-2015
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
  class VMWareServiceProvider extends CloudServiceProvider{
 	private $orgName=null;
 	private $vdc=null;
 	
 	function getCloudService($jsonParams){
 		
 		$arg1 = 'providername';
		$accountprovider = $jsonParams-> $arg1;
		loghandler(INFO, "Connecting to provider:" . $accountprovider);
 		
 		$arg10 = 'cloudproviders';
		$cloudproviders = $jsonParams-> $arg10;
 		$providerArray = json_decode($cloudproviders);
 		
 		
 		
 		$password = $providerArray-> $accountprovider->password;

$serverurl="server name";
$server = $providerArray-> $accountprovider->$serverurl;

$username="user name";
$user = $providerArray-> $accountprovider->$username;
$this->orgName = $providerArray-> $accountprovider->Organisation;
$datacenter="Data center";
$this->vdc = $providerArray-> $accountprovider->$datacenter;

// Initialize parameters
$httpConfig = array (
	'ssl_verify_peer' => false,
	'ssl_verify_host' => false
);
$sdkversion = "5.5";
$auth = array (
	'username' => $user,
	'password' => $password
);
// Create a service object";
$service = VMware_VCloud_SDK_Service :: getService();

try{

// Login to the service portal, parameters are set from command line
$service->login($server, $auth, $httpConfig, $sdkversion);
$serv=new VMWareService();
$serv->setService($service);
$serv->setOrganisation($this->orgName);
$serv->setDataCenter($this->vdc);
$serv->setProviderName($accountprovider);
return $serv;
	} catch (Exception $se) {
			$message = $se->getMessage();
			trigger_error($message, E_USER_ERROR);
		}
		return null;

 	}
 
 	}
 
?>
