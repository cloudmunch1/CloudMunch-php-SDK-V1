<?php
require_once "WindowsAzure/WindowsAzure.php";
use WindowsAzure \ Common \ ServicesBuilder;
use WindowsAzure \ Common \ ServiceException;
require_once 'Cloud/CloudServiceProvider.php';

/*
 * Created on 09-Mar-2015
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class AzureServiceManagementProvider extends CloudServiceProvider {

	function getCloudService($jsonParams) {

		$arg1 = 'providername';
		$accountprovider = $jsonParams-> $arg1;
		$arg1 = 'domain';
		$domain = $jsonParams-> $arg1;
		
		$arg10 = 'cloudproviders';
		$cloudproviders = $jsonParams-> $arg10;
		$providerArray = json_decode($cloudproviders);
		$subscriptionid = "subscriptionid";
		$subscrid = $providerArray-> $accountprovider-> $subscriptionid;
		loghandler(DEBUG, "Subscription id:".$subscrid);
		$certpath = "ServiceManagementAPICertificate";
		$certfpath = $providerArray-> $accountprovider-> $certpath;
		loghandler(DEBUG, "Certificate path:".$certfpath);

		//$connectionString="SubscriptionID=".$subscrid.";CertificatePath=/var/cloudbox/azurecer/mycert.pem";
		$connectionString = "SubscriptionID=" . $subscrid . ";CertificatePath=/var/cloudbox/" .$domain."/data/keys/". $certfpath;
		loghandler(DEBUG, "Connection String:".$connectionString);

		try {

			$serviceManagementRestProxy = ServicesBuilder :: getInstance()->createServiceManagementService($connectionString);

			return $serviceManagementRestProxy;
		} catch (Exception $se) {
			$message = $se->getMessage();
			trigger_error($message, E_USER_ERROR);
		}
		return null;
	}

}
?>
