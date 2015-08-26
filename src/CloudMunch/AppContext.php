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
  * @package CloudMunch
  * @author Rosmi
  * This class is the Applictaion Context object that has all the environment variables needed 
  * for plugin runtime.
  */
 class AppContext{
 	private $masterurl = "";
	private $cloudproviders="";
	private $domainName="";
	private $project="";
	private $job="";
	
	/**
	 * 
	 * @return string masterurl: Cloudmunch service URL.
	 */
	function getMasterURL(){
		return $this->masterurl;
	}
	
	/**
	 * 
	 * @param string  mURL : Cloudmunch service URL.
	 */
	function setMasterURL($mURL){
		$this->masterurl=$mURL;
	}
	
	/**
	 * 
	 * @return array  cloudproviders
	 */
	function getCloudproviders(){
		return $this->cloudproviders;
		
	}
	
	/**
	 * 
	 * @param array  cps provider details.
	 */
	function setCloudproviders($cps){
		$this->cloudproviders=$cps;
	}
	
	/**
	 * 
	 * @return string domainName
	 */
	function getDomainName(){
		return $this->domainName;
	}
	
	/**
	 * 
	 * @param string dname : domain name
	 */
	function setDomainName($dname){
		$this->domainName=$dname;
	}
	
	/**
	 * 
	 * @return string project
	 */
	function getProject(){
		return $this->project;
	}
	
	/**
	 * 
	 * @param string $proj 
	 */
	function setProject($proj){
		$this->project=$proj;
	}
	
	/**
	 * 
	 * @return string $job
	 */
	function getJob(){
		return $this->job;
	}
	
	
	/**
	 * 
	 * @param string  $job 
	 */
	function setJob($job){
		$this->job=$job;
		
	}
 }
?>
