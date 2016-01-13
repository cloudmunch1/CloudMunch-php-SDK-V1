<?php
namespace CloudMunch;
class LogHandler{
	private $appContext=null;
	public function __construct($appContext){
		$this->appContext = $appContext;
		
	}
	function isdebugenabled(){
		return true;
	}
	
	function log($msgNo, $msg) {
		try{
			date_default_timezone_set('UTC');
			$date =date('Y-m-d H:i:s');
		} catch (Exception $se) {
		}
		$stepname=$this->appContext->getStepName();
		switch ($msgNo) {
			case DEBUG :
				if (isdebugenabled()) {
					echo "<b>DEBUG</b> [$date][$stepname] $msg\n";
				}
				break;
			case INFO :
				echo "<b>INFO</b> [$date][$stepname] $msg\n";
				break;
			case ERROR:
				echo "<b>ERROR</b> [$date] [$stepname]$msg\n";
				break;
					
					
		}
	}
	
}