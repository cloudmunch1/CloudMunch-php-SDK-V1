<?php
namespace CloudMunch\Cloud\AWS;
//require_once '/var/cloudbox/awsdk2/aws.phar';
//require_once 'AppAbstract.php';
use Aws \ Common \ Enum \ Region;

use Aws \ Common \ Aws;
use Aws \ Ec2 \ Enum \ InstanceType;

use CloudMunch\Cloud\CloudServiceProvider;


/*
 * Created on 09-Feb-2015
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 
 class AWSserviceProvider extends CloudServiceProvider{
 	
 	function getCloudService($jsonParams){
 		
 		$arg1 = 'providername';
		$accountprovider = $jsonParams-> $arg1;
 		$arg10 = 'region';
		$region = $jsonParams-> $arg10;
 		$arg10 = 'cloudproviders';
		$cloudproviders = $jsonParams-> $arg10;
 		$providerArray = json_decode($cloudproviders);
		
		$awsacesskey = $providerArray-> $accountprovider->accessKey;

		$secretkey = $providerArray-> $accountprovider->secretKey;
		try {
			loghandler(DEBUG, "Creating service factory");
			
			$aws = Aws :: factory(array (
				'key' => $awsacesskey,
				'secret' => $secretkey,
				'region' => $region
			));
			loghandler(DEBUG, "Got service factory");
			return $aws;
		} catch (Exception $se) {
			$message = $se->getMessage();
			trigger_error($message, E_USER_ERROR);
		}
		return null;
 		
 		
 	}
 	
 }
?>
