<?php
require_once 'Cloud/CloudServiceProvider.php';
require_once 'JenkinsService.php';
class JenkinsServiceProvider extends CloudServiceProvider{


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


		$serverurl="jenkinsURL";
		$serverurl = $providerArray-> $accountprovider->$serverurl;


		$serv=new JenkinsService();
		$serv->setUserName($user);
		$serv->setPassword($password);
		$serv->setServiceURL($serverurl);
		return $serv;

	}

}

?>