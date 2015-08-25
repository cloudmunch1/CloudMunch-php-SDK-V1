<?php
/**
 *  (c) CloudMunch Inc.
 *  All Rights Reserved
 *  Un-authorized copying of this file, via any medium is strictly prohibited
 *  Proprietary and confidential
 *
 *  Rosmi Chandy rosmi@cloudmunch.com
 */
namespace CloudMunch;

/**
 * This class is to create server object ,that holds the data about a server.
 */
class Server{
		
	private $description="";
	private $dns="";
	private $domainName="";
	private $emailID="";
	private $CI="";
	private $deploymentStatus="";
	private $instanceId="";
	private $imageID="";
	private $launcheduser="";
	private $build="";
	private $appName="";
	private $deployTempLoc="";
	private $buildLocation="";
	private $privateKeyLoc="";
	private $publicKeyLoc="";
	private $loginUser="";
	private $serverType="";
	private $assettype="";
	private $status="";
	private $starttime="";
	private $provider="";
	private $region="";
	private $cmserver="";
	private $assetname="";
	private $instancesize="";
	private $servername="";
	private $password="";
	private $sshport=22;
	private $tier="";
	
	function getTier(){
		return $this->tier;
	}
	
	function setTier($tier){
		$this->tier=$tier;
	}
	
	function getSSHPort(){
		return $this->sshport;
	}

	function setSSHPort($port){
		$this->sshport=$port;
	}

	function getServerName(){
		return $this->servername;
	}
	
	function setServerName($name){
		
		$this->servername=$name;
	}
	
	/**
	 * Get description of server
	 */
	function getDescription(){
	return	$this->description;
	}
	/**
	 * Set description of the server
	 * @param string $desc server descritption
	 */
	function setDescription($desc){
		$this->description=$desc;
	}
	
	/**
	 * Get public DNS of the server
	 */
	function getDNS(){
		return $this->dns;
	}
	/**
	 * @param string public DNS of server
	 */
	function setDNS($dns){
		$this->dns=$dns;
		
	}
	function getDomainName(){
		return $this->domainName;
	}
	function setDomainName($dname){
		$this->domainName=$dname;
		
	}
	function getCI(){
		return $this->CI;
	}
	function setCI($ci){
		$this->CI=$ci;
	}
	function getDeploymentStatus(){
		return $this->deploymentStatus;
	}
	function setDeploymentStatus($ds){
		$this->deploymentStatus=$ds;
	}
	function getInstanceId(){
		return $this->instanceId;
	}
	function setInstanceId($instid){
		$this->instanceId=$instid;
	}
	function getImageID(){
	return $this->imageID;	
	}
	function setImageID($imageid){
		$this->imageID=$imageid;
	}
	function getLauncheduser(){
		return $this->launcheduser;
	}
	function setLauncheduser($luser){
		$this->launcheduser=$luser;
	}
	function getBuild(){
		return $this->build;
	}
	function setBuild($bld){
		$this->build=$bld;
	}
	function getAppName(){
		return $this->appName;
	}
	function setAppName($appn){
		$this->appName=$appn;
	}
	function getDeployTempLoc(){
		return $this->deployTempLoc;
	}
	function setDeployTempLoc($deptemp){
		$this->deployTempLoc=$deptemp;
	}
	function getBuildLocation(){
		return $this->buildLocation;
		
	}
	function setBuildLocation($bloc){
		$this->buildLocation=$bloc;
		
	}
	function getPrivateKeyLoc(){
		return $this->privateKeyLoc;
	}
	function setPrivateKeyLoc($pkey){
		$this->privateKeyLoc=$pkey;
	}
	function getPublicKeyLoc(){
		return $this->publicKeyLoc;
	}
	function setPublicKeyLoc($ploc){
		$this->publicKeyLoc=$ploc;
		
	}
	function getLoginUser(){
		return $this->loginUser;
	}
	function setLoginUser($luser){
		$this->loginUser=$luser;
	}
	function getServerType(){
		return $this->serverType;
	}
	function setServerType($stype){
		 $this->serverType=$stype;
		
	}
	function getAssettype(){
		return $this->assettype;
	}
	function setAssettype($atype){
		$this->assettype=$atype;
	}
	function getStatus(){
		return $this->status;
	}
	function setStatus($status){
		 $this->status=$status;
	}
	function getStarttime(){
		return $this->starttime;
	}
	function setStarttime($stime){
		$this->starttime=$stime;
	}
	function getProvider(){
		return $this->provider;
	}
	function setProvider($provider){
		$this->provider=$provider;
	}
	function getRegion(){
		return $this->region;
	}
	function setRegion($region){
		$this->region=$region;
	}
	function getCmserver(){
		return $this->cmserver;
	}
	function setCmserver($cmserver){
		$this->cmserver=$cmserver;
	}
	function getAssetname(){
		return $this->assetname;
	}
	function setAssetname($aname){
		$this->assetname=$aname;
	}
	function getInstancesize(){
		return $this->instancesize;
	}
	function setInstancesize($isize){
		$this->instancesize=$isize;
	}
	function getEmailID(){
		return $this->emailID;
	}
	function setEmailID($eid){
		$this->emailID=$eid;
	}
	function getPassword(){
		return $this->password;
	}
	function setPassword($eid){
		$this->password=$eid;
	}
	
}
	
?>
