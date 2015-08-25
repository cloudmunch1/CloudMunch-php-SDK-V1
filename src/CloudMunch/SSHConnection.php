<?php
namespace CloudMunch;

use Exception;
/*
 * Created on 22-Mar-2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class SSHConnection{
 	private $connection=null;
 	private $userConnection=null;
 function getRootConnection($dnsName,$connectionTimeOut,$serverName,$sshport = 22){
 	    $currentTime=time();
		$connectionTimeout=$currentTime+($connectionTimeOut*60);
		$privateKey='/var/cloudbox/data/rootKeyLoc/privateKey';
		$publicKey='/var/cloudbox/data/rootKeyLoc/publicKey';
		do{
			if(($dnsName == null) ||($dnsName == '')){
			throw new Exception("Invalid dns".$dnsName);	
			}
		//writeStatusLog($serverName,"connecting to ".$dnsName);	
		$this->connection = ssh2_connect($dnsName, $sshport);
		if (!$this->connection) {
			sleep(10);
		}
		
		}while((!$this->connection) && (time()<$connectionTimeout));
		
		if(!$this->connection){
			throw new Exception("Failed to connect to ".$dnsName);
		}
		
			if (ssh2_auth_pubkey_file($this->connection,'root', $publicKey,$privateKey)) {
				//writeStatusLog($serverName,"Public Key Authentication Successful");
		//	echo "Public Key Authentication Successful\n";
			
		} else {
		if (ssh2_auth_pubkey_file($this->connection, 'root', $publicKey,$privateKey)) {
			//writeStatusLog($serverName,"Public Key Authentication Successful");
				
			} else {
				
				throw new Exception('Public Key Authentication Failed');
			}
			
		}
		return $this->connection;
 	
 }
 
 function getUserConnection($dnsName,$connectionTimeOut,$serverName,$requestid,$sshport = 22){
  $currentTime=time();
		$connectionTimeout=$currentTime+($connectionTimeOut*60);
		$privateKey='/var/cloudbox/data/rootKeyLoc/privateKey';
		$publicKey='/var/cloudbox/data/rootKeyLoc/publicKey';
		//echo 'getting connection for:'.$dnsName;
		
		//echo 'getting connection for:'.$dnsName;
		error_log("****************connecting to ".$dnsName);
		do{
			if(($dnsName == null) ||($dnsName == '')){
			throw new Exception("Invalid dns".$dnsName);	
			}
		
		$this->userConnection = ssh2_connect($dnsName, $sshport);
		if (!$this->userConnection) {
			sleep(10);
		}
		
		}while((!$this->userConnection) && (time()<$connectionTimeout));
		
		if(!$this->userConnection){
			echo "Failed to connect to ".$dnsName;
			throw new Exception("Failed to connect to ".$dnsName);
		}
		
		//echo '****************authenticating to '.$dnsName;
		error_log("****************authenticating to ".$dnsName);
			if (ssh2_auth_pubkey_file($this->userConnection,'ec2-user', $publicKey,$privateKey)) {
				//	writeStatusLog($serverName, date("Y-m-d-H:i:s"). ':INFO:****************authenticated to '.$dnsName, $requestid);
				echo '****************authenticated to '.$dnsName;
				error_log("****************authent-icated to ".$dnsName);
				//writeStatusLog($serverName,"Public Key Authentication Successful");
		//	echo "Public Key Authentication Successful\n";
			
		} else {
		if (ssh2_auth_pubkey_file($this->userConnection, 'ec2-user', $publicKey,$privateKey)) {
			//writeStatusLog($serverName,"Public Key Authentication Successful");
				echo "****************Public Key Authentication Successful";
			} else {
			//	writeStatusLog($serverName, date("Y-m-d-H:i:s"). ':INFO:authentication to '.$dnsName.' failed', $requestid);
				echo 'authentication to '.$dnsName.' failed';
				//die('Public Key Authentication Failed');
				throw new Exception('Public Key Authentication Failed');
			}
			
		}
		return $this->userConnection;
 }


 
 function getUserConnectionwithKey($dnsName,$connectionTimeOut,$serverName,$privateKey,$publicKey,$password,$userName,$sshport = 22){
 	 $currentTime=time();
		$connectionTimeout=$currentTime+($connectionTimeOut*60);
		
		//echo "\ngetting connection for:".$dnsName;
	//	echo "\nkeys:".$privateKey;
		
	//	echo "\nloginuser:".$userName;
		
	//	echo "\ngetting connection for:".$dnsName;
		
		do{
			if(($dnsName == null) ||($dnsName == '')){
			throw new Exception("/nInvalid DNS".$dnsName);	
			}
		
		$this->userConnection = ssh2_connect($dnsName, $sshport);
		if (!$this->userConnection) {
			sleep(10);
		}
		
		}while((!$this->userConnection) && (time()<$connectionTimeout));
		
		if(!$this->userConnection){
			//echo "\nFailed to connect to ".$dnsName;
			throw new Exception("Failed to connect to ".$dnsName);
		}
		
	//	echo "\nauthenticating to server  ".$dnsName;
	
		
		if((strlen($privateKey)>0) ){
		$publicKey=$privateKey.".pub";
	//	echo "generating publickey".$privateKey.".pub";
		system("ssh-keygen -y -f ".$privateKey." >".$privateKey.".pub");
			if (ssh2_auth_pubkey_file($this->userConnection,$userName, $publicKey,$privateKey)) 	{
				
			//	echo "\nauthenticated to ".$dnsName;
				
			
		} else {
		if (ssh2_auth_pubkey_file($this->userConnection, $userName, $publicKey,$privateKey)) {
			
	//			echo "\nPublic Key Authentication Successful";
			} else {
			
	//			echo "\nauthentication to ".$dnsName.' failed';
				
				throw new Exception('Public Key Authentication Failed');
			}
			
		}
		
		}
		else if(strlen($password)>0){
			

			if(ssh2_auth_password($this->userConnection, $userName, $password)){
	//			echo "\nauthenticated to ".$dnsName;
			}else{
	//			echo "\npassword authentication failed";
				throw new Exception('Invalid Password');
			}
		}else{
			throw new Exception("\n no valid credentials");
		}
		
		return $this->userConnection;
 }

}
?>
