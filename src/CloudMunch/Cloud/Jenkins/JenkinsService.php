<?php
class JenkinsService{
	private $username=null;
	private $password=null;
	private $serviceurl=null;
	private $projectname=null;


	function setUserName($sv){
		$this->username=$sv;
	}

	function setPassword($pass){
		$this->password=$pass;
	}
	function getUserName(){
		return $this->username;
	}

	function getPassword(){
		return $this->password;
	}

	function getServiceURL(){
		return $this->serviceurl;
	}

	function setServiceURL($url){
		$this->serviceurl=$url;
	}

	function getProviderName(){
		return $this->provider;
	}

	function setProviderName($provider){
		$this->provider=$provider;
	}
}
?>
