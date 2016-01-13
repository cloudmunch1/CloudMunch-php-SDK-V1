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
function isdebugenabled() {
	return $debugenabled = true;
}
function myErrorHandler($errno, $errstr, $errfile, $errline) {
	if (!(error_reporting() & $errno)) {
		// This error code is not included in error_reporting
		return;
	}

	date_default_timezone_set('UTC');
	$date =date('Y-m-d H:i:s');
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
			//echo "<b>NOTICE</b> [$date] $errstr $errfile $errline\n";
			break;

		default :
			echo "Unknown error type: [$date] $errstr\n";
			break;
	}

	/* Don't execute PHP internal error handler */
	return true;
}

set_error_handler("myErrorHandler");
date_default_timezone_set('UTC');

/**
 * 
 * @param string  $msgNo : DEBUG or INFO.
 * @param string $msg : message to be logged.
 */
/* function loghandler($msgNo, $msg) {

	date_default_timezone_set('UTC');
	$date = date(DATE_ATOM);
	switch ($msgNo) {
		case DEBUG :
			if (isdebugenabled()) {
				echo "<b>DEBUG</b> [$date] $msg\n";
			}
			break;
		case INFO :
			echo "<b>INFO</b> [$date] $msg\n";
			break;
		case ERROR:
			echo "<b>ERROR</b> [$date] $msg\n";
			break;
			
			
	}
} */
?>
