<?php
/*
 * Created on 26-Apr-2015
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 class VMWareService{
 	private $orgName=null;
 	private $vdc=null;
 	private $service=null;
 	private $provider=null;
 	function setService($sv){
 		 $this->service=$sv;
 	}
 	
 	function setOrganisation($org){
 		 $this->orgName=$org;
 	}
 	function setDataCenter($dc){
 		 $this->vdc=$dc;
 	}
 	function getService(){
 		return $this->service;
 	}
 	
 	function getOrganisation(){
 		return $this->orgName;
 	}
 	function getDataCenter(){
 		return $this->vdc;
 	}
 	
 	function getProviderName(){
 		return $this->provider;
 	}
 	
 	function setProviderName($provider){
 		$this->provider=$provider;
 	}
 }
?>
