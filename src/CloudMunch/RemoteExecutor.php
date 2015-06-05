<?php
namespace CloudMunch;
class RemoteExecutor {

	private $connection;
	private $commands;
	private $output;
	private $timeout;
	public function setConnection($Connection) {
		$this->connection=$Connection;
	}	
	public function setCommands($commands) {
		$this->commands=$commands;
	}	
	public function setOutput($output) {
		$this->output=$output;
	}	
	public function setTimeOut($timeout) {
		$this->timeout=$timeout;
	}	
	private function validate() {
		/*Check mandatoriness */
		$mandatoryfields=array();
		if (empty($this->connection) == true) {
			$len=sizeof($mandatoryfields);	
			$mandatoryfields[$len]="connection";		
		}
		if (empty($this->commands) == true) {
			$len=sizeof($mandatoryfields);	
			$mandatoryfields[$len]="commands";		
		}
		if (empty($this->timeout) == true) {
			$this->timeout=3;
		}
		if (empty($mandatoryfields) == false) {
			$addcomma=false;
			$dynamicdata="";
			foreach ( $mandatoryfields as $index => $value) {
				if ($addcomma == true) {
					$dynamicdata.=",".$value;
					
				}
				else {
					$dynamicdata.=$value;
					$addcomma=true;
				}
			}	
			throw new Exception("Mandatory Data [".$dynamicdata."] not available".PHP_EOL);
		}
	}

	private function executeCommand($command) {
			echo "\nrunning command.....".$command.PHP_EOL;
		$stream = ssh2_exec($this->connection, $command);
		echo "\ntimeout is.....".$this->timeout.PHP_EOL;
		$connectionTimeout = time();
		$connectionTimeout = $connectionTimeout + ((int)($this->timeout) * 60);
		stream_set_blocking($stream,false);
		$error_stream = ""; 
		$result=0;
		$finalresult=1;
		$errMessage="";	
		sleep(30);
		
		$err_stream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
		while (((time() < $connectionTimeout))) {
			sleep(1);
			if ($err =fgets($err_stream)) {
				//flush();
				echo $err;
				$errMessage.=$err;	
				$err="";
				$connectionTimeout = time();
				$connectionTimeout = $connectionTimeout + ((int)($this->timeout) * 60);
			}
			if ($cmd =fgets($stream)) {
				//flush();
				echo $cmd.PHP_EOL;	
				if (strstr($cmd,"EXECCOMPLETED=") == true ) {
					if ((strstr($cmd,"EXECCOMPLETED=0") == false) && (strstr($cmd,"EXECCOMPLETED=%ERRORLEVEL%") == false)) {
						$result=1;
					}	
					if (strstr($cmd,"EXECCOMPLETED=0")== true) {
						$finalresult=0;
					}
					break;
				}
				$connectionTimeout = time();
				$connectionTimeout = $connectionTimeout + ((int)($this->timeout) * 60);
			}
			$connectionTimeout = time();
			$connectionTimeout = $connectionTimeout + ((int)($this->timeout) * 60);
					//else {
		//		break;
		//	}
	
		}
		if ((empty($errMessage) == false) && ($finalresult != 0 )) {
			echo "\nUnable to  execute".$errMessage.PHP_EOL;
			$result=1;
		} 
    		fclose( $err_stream );
    		fclose( $stream );
		return $result;
	}
	
	private function process() {
		$finalresult;
		/* For each command run and timeout*/
		if (is_array($this->commands) == false) {
			$finalresult=$this->executeCommand($this->commands);
		}
		else {
			foreach($this->commands as $index => $value) {
				$finalresult=$this->executeCommand($value);
				if ($finalresult !=0) {
					break;
				}
			}
		}
		return $finalresult;
	}
	public function run() {
		echo "\nvalidating.....".PHP_EOL;
		$this->validate();
		echo "\nvalidated.....".PHP_EOL;
		echo "\nprocessing.....".PHP_EOL;
		$result=$this->process();
		return $result;
		
	}
}
?>
