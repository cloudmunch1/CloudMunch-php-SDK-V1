<?php
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
	
	
	function getMasterURL(){
		return $this->masterurl;
	}
	
	function setMasterURL($mURL){
		$this->masterurl=$mURL;
	}
	
	function getCloudproviders(){
		return $this->cloudproviders;
		
	}
	
	function setCloudproviders($cps){
		$this->cloudproviders=$cps;
	}
	
	function getDomainName(){
		return $this->domainName;
	}
	
	function setDomainName($dname){
		$this->domainName=$dname;
	}
	
	function getProject(){
		return $this->project;
	}
	
	function setProject($proj){
		$this->project=$proj;
	}
	
	function getJob(){
		return $this->job;
	}
	
	function setJob($job){
		$this->job=$job;
		
	}
 }
?>
