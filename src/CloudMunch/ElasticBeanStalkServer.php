<?php
namespace CloudMunch;
//require_once "Server.php";
use CloudMunch\Server;
/*
 * Created on 20-Feb-2015
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class ElasticBeanStalkServer extends Server{
		
	
	
	private $bucketname ="";
	private $applicationName = "";
	private $templateName = "";
	private $envname = "";
	//private $region="";
	//private $appName="";
	//private $provider="";
	//private $dns="";
	
	//private $cmserver="";
	//private $assetname="";
	//private $description="";
	//private $assettype="";
	//private $deployTempLoc="";
	
	//private $status="";
	//private $starttime="";
	
	//private $servername="";
	
	function getServerName(){
		return $this->servername;
	}
	
	function setServerName($name){
		
		$this->servername=$name;
	}
	
	
	function getEnvironmentName(){
		return $this->envname;
	}
	
	function setEnvironmentName($name){
		
		$this->envname=$name;
	}
	
	function getBucketName(){
		return $this->bucketname;
	}
	
	function setBucketName($name){
		
		$this->bucketname=$name;
	}
	
	function getApplicationName(){
		return $this->applicationName;
	}
	
	function setApplicationName($name){
		
		$this->applicationName=$name;
	}
	
	function getTemplateName(){
		return $this->templateName;
	}
	
	function setTemplateName($name){
		
		$this->templateName=$name;
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
	
	/*function getInstanceId(){
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
	}*/
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
	/*function getBuildLocation(){
		return $this->buildLocation;
		
	}
	function setBuildLocation($bloc){
		$this->buildLocation=$bloc;
		
	}
	*/
	
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
	/*function getInstancesize(){
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
	}*/
} 
 
 
?>
