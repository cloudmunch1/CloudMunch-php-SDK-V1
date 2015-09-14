<?php
/**
 *  (c) CloudMunch Inc.
 *  All Rights Reserved
 *  Un-authorized copying of this file, via any medium is strictly prohibited
 *  Proprietary and confidential
 *
 *  Rosmi Chandy rosmi@cloudmunch.com
 */
/**
 * This file handles error/debug logs
 */

const DEBUG = 'DEBUG';
const INFO = 'INFO';
$__log_level = "DEBUG";
function myErrorHandler($errno, $errstr, $errfile, $errline) {
	if (!(error_reporting() & $errno)) {
		// This error code is not included in error_reporting
		return;
	}
	date_default_timezone_set('UTC');
	$date = date(DATE_ATOM);
	switch ($errno) {
		case E_RECOVERABLE_ERROR :
		case E_COMPILE_ERROR :
		case E_CORE_ERROR :
		case E_PARSE :
		case E_ERROR :
		case E_USER_ERROR :
			echo "<b><font color=\"red\">ERROR</b> [$date] $errstr\n";
			//  echo "  Fatal error on line $errline in file $errfile";
			//  echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
			echo "\nAborting...</font><br />\n";
			exit (1);
			break;
		case E_CORE_WARNING :
		case E_WARNING :
		case E_USER_WARNING :
			if (strpos($errstr, 'ssh2_connect():') !== false) {
				$msg = "Could not connect to the server";
				echo "<b>INFO</b> [$date] $msg\n";
			} else {
				echo "<b>WARNING</b> [$date] $errstr\n";
			}
			break;
		case E_STRICT :
		case E_NOTICE :
		case E_USER_NOTICE :
			echo "<b>NOTICE</b> [$date] $errstr $errfile $errline\n";
			break;
		default :
			echo "Unknown error type: [$date] $errstr\n";
			break;
	}
	/* Don't execute PHP internal error handler */
	return true;
}
set_error_handler("myErrorHandler");

/**
 * 
 * @param string  $log_level : loglevel to set for the app 
 */

function set_log_level($log_level){
	$__log_level = $log_level;
}


/**
 * 
 * @param string  $msgNo : debug/warning/info/error
 * @param string $msg : message to be logged.
 */

function loghandler($msgNo, $msg) {
	date_default_timezone_set('UTC');
	$date = date(DATE_ATOM);
	// BITMASK support 
	if(strpos(strtolower($__log_level), strtolower($msgNo)) !== false || strtolower($__log_level) == "debug") {
		// If $__log_level is set as DEBUG - show all logs from the plugin as - DEBUG  
		if(strtolower($__log_level) == "debug") {
			$msgNo = "DEBUG";
		}
		switch (strtolower($msgNo)) {
			case "warning" :
				echo "<b>WARNING</b> [$date] $msg\n";
				break;
			case "info" :
				echo "<b>INFO</b> [$date] $msg\n";
				break; 
			case "error" : 
				echo "<b style='color:red'>ERROR</b> [$date] $msg\n";
				break;
			case "debug" : 
				echo "<b>DEBUG</b> [$date] $msg\n";
				break;	
		}
	}else{
		return false; 
	}
}
?>