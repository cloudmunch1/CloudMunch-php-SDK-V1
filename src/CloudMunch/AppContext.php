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
 * Class AppContext
 * 
 * @package CloudMunch
 * @author Rosmi
 *         This class is the Applictaion Context object that has all the environment variables needed
 *         for plugin runtime.
 */
class AppContext {
	private $masterurl = "";
	private $cloudproviders = "";
	private $domainName = "";
	private $project = "";
	private $job = "";
	private $workspaceLocation = "";
	private $archiveLocation = "";
	private $stepid = "";
	private $targetServer="";
	private $integrations="";
	
	/**
	 * 
	 * @return Workspace location of the job
	 */
	function getWorkSpaceLocation() {
		
		return $this->workspaceLocation;
	}
	
	/**
	 * 
	 * @param set  Workspace loctaion
	 */
	function setWokSpaceLocation($workspaceloc) {
		$this->workspaceLocation=$workspaceloc;
	}
	
	/**
	 * 
	 * @return Archive Location if build run is selected
	 */
	function getArchiveLocation() {
		return $this->archiveLocation;
	}
	
	/**
	 * 
	 * @param Set archive location
	 */
	function setArchiveLocation($archiveLoc) {
		$this->archiveLocation=$archiveLoc;
	}
	
	/**
	 * 
	 * @return ID of current step
	 */
	function getStepID() {
		return $this->stepid;
	}
	
	/**
	 * 
	 * @param Set step ID
	 */
	function setStepID($stepid) {
		$this->stepid=$stepid;
	}
	 /**
	  * 
	  * @param set Target Server
	  */
	function setTargetServer($targetServer){
		$this->targetServer=$targetServer;
		
	}
	
	/**
	 * 
	 * @return Get target server
	 */
	function getTargetServer(){
		return $this->targetServer;
	
	}
	/**
	 *
	 * @return string masterurl: Cloudmunch service URL.
	 */
	function getMasterURL() {
		return $this->masterurl;
	}
	
	/**
	 * 
	 * @param Set integration details
	 */
	function setIntegrations($ints){
		$this->integrations=$ints;
	}
	
	
    function getIntegrations(){
		return $this->integrations;
	}
	
	/**
	 *
	 * @param
	 *        	string mURL : Cloudmunch service URL.
	 */
	function setMasterURL($mURL) {
		$this->masterurl = $mURL;
	}
	
	/**
	 *
	 * @return array cloudproviders
	 */
	function getCloudproviders() {
		return $this->cloudproviders;
	}
	
	/**
	 *
	 * @param
	 *        	array cps provider details.
	 */
	function setCloudproviders($cps) {
		$this->cloudproviders = $cps;
	}
	
	/**
	 *
	 * @return string domainName
	 */
	function getDomainName() {
		return $this->domainName;
	}
	
	/**
	 *
	 * @param
	 *        	string dname : domain name
	 */
	function setDomainName($dname) {
		$this->domainName = $dname;
	}
	
	/**
	 *
	 * @return string project
	 */
	function getProject() {
		return $this->project;
	}
	
	/**
	 *
	 * @param string $proj        	
	 */
	function setProject($proj) {
		$this->project = $proj;
	}
	
	/**
	 *
	 * @return string $job
	 */
	function getJob() {
		return $this->job;
	}
	
	/**
	 *
	 * @param string $job        	
	 */
	function setJob($job) {
		$this->job = $job;
	}
}
?>
