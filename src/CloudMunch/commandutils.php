<?php

namespace CloudMunch;

include_once ("httpclientcurlutil.php");

class commandutils{

	function & commandutils_getservercredentials($domain,$domainurl,$i_servername) {
		$default=null;

		$serverlist=&commandutils_getserverlist($domainurl,$domain);
		foreach ($serverlist as $servername => $attributes) {
			if (strcmp($servername,$i_servername) == 0 ) {
				return $attributes;
			}
		}
		return $default;
	}

	function  commandutils_getvalue($key,$array) 
	{
		$value=null;
		if (array_key_exists($key,$array) == true) {
			$value=$array[$key];
		}
		return $value;
	} 

	function display_error($reason="") {
			echo ("Unable to service request [".$reason."]\n");
			exit(-1);
	}

	function  display_jsonerror()
	{
		switch (json_last_error()) {
	        case JSON_ERROR_NONE:
	            display_error(' - No errors');
	        break;
	        case JSON_ERROR_DEPTH:
	            display_error(' - Maximum stack depth exceeded');
	        break;
	        case JSON_ERROR_STATE_MISMATCH:
	            display_error(' - Underflow or the modes mismatch');
	        break;
	        case JSON_ERROR_CTRL_CHAR:
	            display_error(' - Unexpected control character found');
	        break;
	        case JSON_ERROR_SYNTAX:
	            display_error(' - Syntax error, malformed JSON');
	        break;
	        case JSON_ERROR_UTF8:
	            display_error(' - Malformed UTF-8 characters, possibly incorrectly encoded');
	        break;
	        default:
	            display_error(' - Unknown error');
	        break;
	    }
	}


	function display_message($message="") {
			echo ("Execution Status [".$message."]\n");
			
	}

	function  & commandutils_getserverlist ($domainurl,$domain)
	{
		$client = new httpclientcurlutil();
		$httpMethod = 'GET';
	 
		$url=$domainurl.'/cbdata.php';
		$parameters = array (
		'username'=>'CI',
		'domain'=>$domain,
		'context'=> 'serverlist'
		);
	  

		$response=$client->request($url, $parameters, $httpMethod);
		echo "the url is :".$url."    :".$domain;
		$responsestring=json_encode($response);
		$response_a=json_decode($responsestring,true);
		$data=$response_a['response'];
		
		$serverdata=json_decode($data,true);

		return $serverdata;
	}

	function  & commandutils_getcpuutilizationthreshold($domainurl,$domain)
	{
		$client = new httpclientcurlutil();
		$httpMethod = 'GET';
	 
		$url=$domainurl.'/cbdata.php';
		$parameters = array(
		'username'=>'CI',
		'domain'=>$domain,
		'context'=> 'serviceactions');
	  

		$response=$client->request($url, $parameters, $httpMethod);
		
		$responsestring=json_encode($response);
		$response_a=json_decode($responsestring,true);
		$data=$response_a['response'];
		$cputhreshold=-1;	
		$servicecommands=json_decode($data,true);
		if (is_null($servicecommands) != true) {
			if (array_key_exists('cpuutilization',$servicecommands) == true) {
				$tmp_a=$servicecommands['cpuutilization'];
				if (array_key_exists('threshold',$tmp_a) == true ) {
					$setthreshold=$tmp_a['threshold'];
					if (is_numeric($setthreshold)==true) {
						$cputhreshold=$setthreshold;
					}
				} 
			}
		}

		return $cputhreshold;	

	}
	function  & commandutils_gethealthcheckcommands($domainurl,$domain)
	{
		$client = new httpclientcurlutil();
		$httpMethod = 'GET';
	 
		$url=$domainurl.'/cbdata.php';
		$parameters = array(
		'username'=>'CI',
		'domain'=>$domain,
		'context'=> 'serviceactions');
	  

		$response=$client->request($url, $parameters, $httpMethod);
		
		$responsestring=json_encode($response);
		$response_a=json_decode($responsestring,true);
		$data=$response_a['response'];
		$o_command=null;	
		$servicecommands=json_decode($data,true);
		if (is_null($servicecommands) != true) {
			if (array_key_exists('healthcheck',$servicecommands) ==true ) {
				$healthcheckcommands=$servicecommands['healthcheck'];
				if (is_null($healthcheckcommands) != true) {
					$o_command=$healthcheckcommands['command'];
				}
			}
		}

		return $o_command;
	}
	function commandutils_replaceenvvariable($value)
	{
		$o_value="";
		$rec=0;
		$len=0;
		$env=false;
		$values_a=explode("##",$value);

		$len=sizeof($values_a);
		$env_file="../__environment__.json";
		
		if (file_exists($env_file)==true) {
			$env_a=json_decode(file_get_contents($env_file),true); 
		}
		else {
			$env_a=array();
		}
		for($rec;$rec<$len;$rec++) {
			if ($env == false) {
				$env=true;
				$o_value.=$values_a[$rec];
					
			}
			else {
				$env=false;
				if (array_key_exists($values_a[$rec],$env_a) == true) {
					$o_value.=$env_a[$values_a[$rec]];
				}
				else {
					$o_value.= " ";
				}
			}
		}
		return $o_value;
	}
	function commandutils_modifyresponse($inp_array) 
	{
		$out_array=array();
		foreach ($inp_array as $name => $value) {
			if (is_string($value) == true) {
				$out_array[$name] = commandutils_replaceenvvariable($value);
			}
			else {
				$out_array[$name] = $value;
			}
		}
		return $out_array;
	}

	function &read_jsoninput($argArray) {
		$jsonParameters = null; 
		
		for ($i = 0; $i < sizeof($argArray); $i++) {
			
			switch ($argArray[$i]) {

				case "-jsoninput":
				{
					$jsonParameters=$argArray[$i +1];
					break;
				}
			}
		} 
		
		if (is_null($jsonParameters) == TRUE) {
				display_error("arguments with -jsoninput not passed");
		}
		$output_a=$jsonParameters;
		
		return $output_a;
	}

	function   commandutils_getnexttoken($domainurl,$domain)
	{
	        $client = new httpclientcurlutil();
	        $httpMethod = 'GET';

	        $url=$domainurl.'/cbdata.php';
	        $parameters = array (
	        'username'=>'CI',
	        'domain'=>$domain,
	        'action'=> 'gettoken'
	        );

	        $token = "";
	        $response=$client->request($url, $parameters, $httpMethod);
	        if (array_key_exists("response",$response) == true) {
	                $token=$response["response"];
	        }
	        var_dump($token);
	        return $token;
	}
	function   commandutils_gettoken($domainurl,$domain,$forcenew=false)
	{
	        global $_ci_token_;

	        if ((empty($_ci_token_) == false) && ($forcenew == false)) {
	                return $_ci_token_;
	        }
	        $_ci_token_=commandutils_getnexttoken($domainurl,$domain);
	        return $_ci_token_;

	}

/*
	if (!defined("JSON_UNESCAPED_SLASHES")) {
		define("JSON_UNESCAPED_SLASHES", 64);  // 5.4.0
		define("JSON_PRETTY_PRINT", 128);      // 5.4.0
		define("JSON_UNESCAPED_UNICODE", 256); // 5.4.0
	}
*/
	function json_array($data) {
		if (is_scalar($data)) {
			return json_decode($data,true);
		}
		else {
			if (is_object($data)) {
				return json_decode(json_encode($data,JSON_UNESCAPED_SLASHES),true);
			}
			else {
				return $data;
			}
		}
	}
	function json_string($data) {
		if (is_scalar($data)) {
			return $data;
		}
		else {
			return json_encode($data,JSON_UNESCAPED_SLASHES);
		}
	}



	function json_object($data) {
		if (is_scalar($data)) {
			return json_decode($data);
		}
		else {
			if (is_array($data)) {
				return json_decode(json_encode($data,JSON_UNESCAPED_SLASHES));
			}
			else {
				return $data;
			}
		}
	}

	function update_data_object($old, $new, $mode = "CHANGE") {
		if ($old === null) {
			return $new;
		}
		if (($new === null)||(is_scalar($new))) {
			return $new;
		}
		foreach ($new as $new_key => $new_val) {
			if (is_object($new)) {
				$sub_new = isset($new->$new_key) ? $new->$new_key : null;
			}
			elseif (is_array($new)) {
				$sub_new = isset($new[$new_key]) ? $new[$new_key] : null;
			}
			if (is_object($old)) {
				$sub_old = isset($old->$new_key) ? $old->$new_key : null;
				if ($sub_new === null) {
					if ($sub_old !== null) {
						unset($old->$new_key);
					}
				}
				else {
					$sub_result = update_data_object($sub_old,$sub_new);
					if ($sub_result === null) {
						unset($old->$new_key);
					}
					else {
						if (isset($old->$new_key)) {
							if (is_scalar($sub_result) && is_array($old->$new_key)) {
								array_push($old->$new_key,$sub_result);
							}
							else {
								$old->$new_key = $sub_result;
							}
						}
						else {
							$old->$new_key = $sub_result;
						}
					}
				}
			}
			elseif (is_array($old)) {
				$sub_old = isset($old[$new_key]) ? $old[$new_key] : null;
				if ($sub_new === null) {
					if ($sub_old !== null) {
						unset($old[$new_key]);
						$old = array_values($old);
					}
				}
				else {
					$sub_result = update_data_object($sub_old,$sub_new);
					if ($sub_result === null) {
						unset($old[$new_key]);
						$old = array_values($old);
					}
					else {
						if (is_numeric($new_key)) {
							if ($new_key < 0) {
								array_unshift($old,$sub_result);
							}
							else {
								$old[$new_key] = $sub_result;
							}
						}
						else {
							$old[$new_key] = $sub_result;
						}
						$old = array_values($old);
					}
				}
			}


		}

		return $old;
	}

	function update_data($old, $new) {
		if (empty($old)||($old === null) || ($new === null)||empty($new)) {
			return $new;
		}
		$old_type = "default";

		if (is_scalar($old)) {
			$old_type = "scalar";
		}
		elseif (is_object($old)) {
			$old_type = "object";
		}
		elseif (is_array($old)) {
			$old_type = "array";
		}
		$old_object = json_object($old);
		if ($old_object === null) {
			return $old;
		}
		$new_object = json_object($new);
		if ($new_object === null) {
			return $old;
		}
		$updated_data = update_data_object($old_object, $new_object);
		switch ($old_type) {
			case "scalar" : $result = json_string($updated_data);break;
			case "object" : $result = json_object($updated_data);break;
			case "array" : $result = json_array($updated_data);break;
			default: $result = $updated_data;
		}
		return $result;
	}
}


?>
