<?php
/**
 *  (c) CloudMunch Inc.
 *  All Rights Reserved
 *  Un-authorized copying of this file, via any medium is strictly prohibited
 *  Proprietary and confidential
 */

namespace CloudMunch;

/** 
 *  Display message based on log level set in app context
 *		LogLevel             Message Types displayed
 *		INFO                 All - except for DEBUG
 *		DEBUG                All
 *		WARNING              WARNING, ERROR and AUDIT
 *      ERROR                ERROR and AUDIT
 *
 *  @package CloudMunch
 *  @author Rosmi <rosmi@cloudmunch.com>
 *  @author Amith <amith@cloudmunch.com>
 */

class LogHandler{
	private $appContext = null;
	private $logLevel   = null;

	public function __construct($appContext){
		$this->appContext = $appContext;
		$this->logLevel   = $this->appContext->getLogLevel();
	}

	public function isDebugEnabled(){
		if($this->logLevel && (strtolower($this->logLevel) === "debug")) {
			return true;
		} else {
			return false;
		}
	}

	public function isInfoEnabled(){
		if($this->logLevel && (strtolower($this->logLevel) === "error") || (strtolower($this->logLevel) === "warning")) {
			return false;
		} else {
			return true;
		}
	}
	
	public function isWarningEnabled(){
		if($this->logLevel && (strtolower($this->logLevel) === "info") || (strtolower($this->logLevel) === "warning") || (strtolower($this->logLevel) === "debug")) {
			return true;
		} else {
			return false;
		}		
	}

	function log($msgNo, $msg) {
		try{
			date_default_timezone_set('UTC');
			$date = date('Y-m-d H:i:s');
		} catch (Exception $se) {
		}
		$stepname = $this->appContext->getStepName();
		switch ($msgNo) {
			case DEBUG :
				if ($this->isDebugEnabled()) {
					echo "<b>DEBUG</b> [$date][$stepname] $msg\n";
				}
				break;
			case INFO :
				if ($this->isInfoEnabled()) {
					echo "<b>INFO</b> [$date][$stepname] $msg\n";
				}
				break;
			case ERROR:
				echo "<b>ERROR</b> [$date] [$stepname]$msg\n";
				break;
			case WARNING:
				if ($this->isWarningEnabled()) {
					echo "<b>WARNING</b> [$date] [$stepname]$msg\n";
				}
				break;
			case AUDIT:
				echo "<b>AUDIT</b> [$date] [$stepname]$msg\n";
				break;
					
			default :		
		}
	}
	
}