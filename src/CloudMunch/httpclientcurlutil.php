<?php


//TODO-VIVEK: remove if checking in this file
require_once ("/var/cloudbox/CBApp/CMUtil/httpclientutil.php");


/**
 * Performs requests on GitHub API. API documentation should be self-explanatory.
 *
 * @author    vivek (copied from Thibault Duplessis <thibault.duplessis at gmail dot com>)
 */
class httpclientcurlutil  extends httpclientutil
{
    /**
     * Send a request to the server, receive a response
     *
     * @param  string   $path          Request url
     * @param  array    $parameters    Parameters
     * @param  string   $httpMethod    HTTP method to use
     * @param  array    $options       Request options
     *
     * @return string   HTTP response
     */

   

    public function doRequest($url, array $parameters = array(), $httpMethod = 'GET', array $options = array())
    {
        

        $curlOptions = array();

        if (!empty($parameters)) {
            $queryString = trim(utf8_encode(http_build_query($parameters, '', '&')));

            if ('GET' === $httpMethod) {
                $url .= '?'.$queryString;
            } else {
               
                $curlOptions += array(
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $queryString
                );
            }
        }
        

        $curlOptions += array(
            CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_VERBOSE=> false
        );
	if (array_key_exists("timeout",$options) == true) {
		$curlOptions[CURLOPT_TIMEOUT]=$options["timeout"];
	}
       
       
       
        $response = $this->doCurlCall($curlOptions);
       

        
        if (!in_array($response['headers']['http_code'], array(0, 200, 201))) {
      	   var_dump($response); 
            throw new Exception(null, (int) $response['headers']['http_code']);
        }

        if ($response['errorNumber'] != '') {
      	   var_dump($response); 
       
            throw new Exception('error '.$response['errorNumber']);
        }

        return $response;
    }

    protected function doCurlCall(array $curlOptions)
    {
        $curl = curl_init();
       // $file = fopen('curlLogs.log', "a+");

        curl_setopt_array($curl, $curlOptions);
        //curl_setopt($curl , CURLOPT_COOKIEJAR, "cj.txt");
       // curl_setopt($curl , CURLOPT_COOKIEFILE, "cj.txt");
        //curl_setopt($curl, CURLOPT_STDERR,$file);


        $response = curl_exec($curl);
       // fclose($file);
        


        $headers = curl_getinfo($curl);
        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        curl_close($curl);

        return compact('response', 'headers', 'errorNumber', 'errorMessage');
    }
}
