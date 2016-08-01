<?php

/**
 *  (c) CloudMunch Inc.
 *  All Rights Reserved
 *  Un-authorized copying of this file, via any medium is strictly prohibited
 *  Proprietary and confidential
 *
 *  Amith Kumar amith@cloudmunch.com
 */
namespace CloudMunch;

use CloudMunch\cmDataManager;
require_once ("AppErrorLogHandler.php");

/**
 * Class CloudmunchService
 * 
 * @package CloudMunch
 * @author Rosmi
 *         This class provides the service methods for the apps to invoke action on cloudmunch
 */
class NotificationHandler {
	private $appContext = null;
	private $cmDataManager;
	private $logHelper=null;
	public function __construct($logHandler, $appContext, $cmDataManager = null) {
		$this->appContext = $appContext;
		$this->logHelper  = $logHandler;
		if (is_null($cmDataManager)) {
			$cmDataManager = new cmDataManager ($this->logHelper, $this->appContext, $this);
		}
		$this->cmDataManager = $cmDataManager;
	}

	/**
	 * Send notification to a selected channel on slack
	 * 
	 * @param string $message
	 *        	: Notification message.
 	 * @param string $status
	 *        	: Status level of message : ERROR/WARNING/INFO
 	 * @param string $channel
	 *        	: Channel to send notification
	 * @param string $to
	 *        	: To address to be notified
	 * @param string $from
	 *        	: From user
	 */
	public function sendSlackNotification($message, $status = "ERROR", $channel = "support", $from = null, $to = null) {
		if(is_null($message) || empty($message)){
			$this->logHelper->log ( ERROR, "Message is mandatory to send a notification!" );
			return false;
		}
		$dataArray = array (
			"message" => $message,
			"channel" => $channel,
			"status" => $status 
		);

		if (!is_null($from)){
			$dataArray["from"] = $from;
		}

		if (!is_null($to)) {
			$dataArray["to"] = $to;
		}

		return $this->cmDataManager->sendNotification ( $this->appContext->getMasterURL (), $this->appContext->getAPIKey(), $dataArray );
	}
}
?>
