<?php
require_once '/var/cloudbox/awsdk2/aws.phar';
require_once 'AppAbstract.php';
use Aws \ Common \ Enum \ Region;

use Aws \ Common \ Aws;
use Aws \ Ec2 \ Enum \ InstanceType;

/*
 * Created on 04-Feb-2015
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
/**
 * Abstract base class for AWS cloudmunch apps,providing methods to connect to AWS account and 
 * other basic AWS service methods.
 */
abstract class AWSAppAbstract extends AppAbstract {

	function getAWSService($region,$accprov) {
		$providerArray = json_decode($this->getAppContext()->getCloudproviders());
		
		$awsacesskey = $providerArray-> $accprov->accessKey;

		$secretkey = $providerArray-> $accprov->secretKey;
		try {
			loghandler(DEBUG, "Creating service factory");
			//echo "\ncreating service factory ....................:";
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
