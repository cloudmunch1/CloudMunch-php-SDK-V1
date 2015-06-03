<?php
/*
 * Created on 22-Oct-2014
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 require_once '/var/cloudbox/awsdk2/aws.phar';
 use Aws \ Common \ Enum \ Region;

use Aws \ Common \ Aws;
use Aws \ Ec2 \ Enum \ InstanceType;
require_once ("/var/cloudbox/CBApp/CMUtil/AppErrorLogHandler.php");

 function getec2client($awsacesskey, $secretkey, $region) {
//	$region = Region :: US_EAST_1;
	try {
		loghandler(DEBUG,"Creating service factory");
		//echo "\ncreating service factory ....................:";
				$aws = Aws :: factory(array (
			'key' => $awsacesskey,
			'secret' => $secretkey,
			'region' => $region
		));
		loghandler(DEBUG,"Got service factory");
		//echo "\ngot service factory ....................:";
	} catch (Exception $se) {
		$message = $se->getMessage();
		trigger_error ( $message, E_USER_ERROR );
	}
	try {
		$ec2client = $aws->get('ec2');
	} catch (Exception $se) {
		$message = $se->getMessage();
		trigger_error ( $message, E_USER_ERROR );
	}
	//echo "return service factory ....................:";
	return $ec2client;
}
 
?>
