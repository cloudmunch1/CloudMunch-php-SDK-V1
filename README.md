# CloudMunch-php-SDK-V2
CloudMunch SDK for PHP provides helper classes for CloudMunch plugin development.

###Download SDK
We recommend using [Composer](https://getcomposer.org/ "Composer") as package manager. All you need to install the sdk is to have the following entry in your composer.json file.

```json
"require": 
  {
    "cloudmunch/php-sdk-v2":"dev-master"
  }

```

##Cloudmunch SDK Details
###AppAbstract class
This is a base abstract class that should be extended by any plugin. 
This class, provides methods to read parameters, create plugin context object and retrieve service objects.
This class has the following lifecycle methods that the plugin need to invoke,
initialize()
This method handles the input parameters and make this available for plugin.

getProcessInput()
This method returns the list of inputs to the plugin.
process()
This is an abstract method to be implemented by every plugin.

performAppcompletion()
This method handles the completion of a plugin execution.
 
 
Here is  the list of helper methods that can be used by plugin,
getAppContext()
This method returns AppContext object for this runtime.

getCloudmunchServerHelper()
This method returns the Cloudmunch Server Helper class to manage servers registered with cloudmunch.
getCloudmunchAssetHelper()
This method returns Asset helper to manage assets registered with cloudmunch.
getCloudmunchService()
This method returns Cloudmunch Service helper needed to invoke any services in cloudmunch.
outputPipelineVariables()
Plugin uses this method to output variables to pipeline.
 
AppContext class
Plugins can get the context or environment information from this class.
Here is the list of methods available,
getWorkSpaceLocation()
This methods returns the absolute path to the workspace of the job.

getArchiveLocation()
This method returns the absolute path to the archive location.
getTargetServer()
This method returns the target server id on which the step is getting executed.
getProject()
This method returns the project name in which the plugin is being executed.
getJob()
This method returns the job name in which the plugin is being executed.
getRunNumber()
This method returns the current build number.
 
##Helper classes
###CloudmunchService
This helper class provides method to retreive/update data from cloudmunch.
Below is the list of methods that can be used.
a) getCloudmunchData($context,$contextid,$filterdata)
b)updateCloudmunchData($context,$contextid,$data)
c) addCloudmunchData($context,$data)
e)deleteCloudmunchData($context,$contextid)
f)downloadKeys($filekey, $context, $contextid)
 
###AssetHelper
This is a helper class to manage assets in cloudmunch.

###EnvironmentHelper
This is a helper class to manage environments in cloudmunch.
 
###RoleHelper
This is a helper class to manage roles in cloudmunch.
 



##Sample Plugin     
 
Let us look at a sample plugin that prints "Hello "+ "string passed at runtime" on execution.
Plugin name : Hello
Input: helloname: This need to be printed out with Hello.
 
Step1: Create a folder "Hello"
Step2: Create a file plugin.json with the following contents,

```
{
  "id": "Hello",
  "name": "Hello,
  "author": "rosmi@cloudmunch.com",
  "version": "1",
  "status": "enabled",
  "tags": ["git","checkout"],
  "inputs": {
    "helloname": {
      "type": "text",
        "mandatory": "yes",
      "display": "yes", 
      "label": "Name",
      "hint": "Give the name to be printed"    
    }
  },
  "outputs": {
    "param1": {
      "name": "",
      "format": ""
    }
  },
  "execute": {
    "main": "Hello/src/HelloDisplay.php",
    "language": "PHP",
    "options": "-debug"
  },
  "documentation": {
    "description": "Prints Hello"
  },
  "_created_by": "rosmi@cloudmunch.com",
  "_create_time": "2015-10-01 06:23:25.0474",
  "_updated_by": "rosmi@cloudmunch.com",
  "_update_time": ""
}
 ```
 
Step 3:
Create composer.json to download Cloudmunch PHP SDK 
```
 {
"require": 
   {
    "cloudmunch/php-sdk-v2":"dev-master"
   }
}
``` 

Step 4:
Create a folder 'src'.
Create a file , HelloDisplay.php.
This file will have all the life cycle methods to control the plugin execution.
```
<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../src/HelloDisplay.class.php';
 
$hellodisplay= new HelloDisplay();
$hellodisplay->initialize();
$processInput = $hellodisplay->getProcessInput();
$hellodisplay->process($processInput);
 
?>
```

Step 5:
Create a file HelloDisplay.class.php

```
<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'../vendor/autoload.php';
use CloudMunch \ AppAbstract;
class HelloDisplay extends AppAbstract
{
  
    public function process($processparameters)
    {
        $inputparameters    = $processparameters['appInput'];
        $name       = $inputparameters->helloname;
       echo "HELLO  ".$name;
    }
}
?>
```

Step 6:
Create file install.sh, this will have scripts to install dependencies for your plugin.
```
#!/bin/bash
BASEDIR=$(dirname "$0")
echo "Script location: ${BASEDIR}"
cd "${BASEDIR}"
#Install composer
if hash composer 2>/dev/null; then
echo "Composer is available"
else
echo "Installing composer ..."
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
fi
composer install
 ```



##Using Integrations

Cloudmunch has an integration framework using which plugins can interact with any external service providers.
For example if the plugin need to interact with Amazon web services, the account can be registered in cloudmunch integrations page.
This integration will now be available for the plugin at runtime. The details can be retrieved in the plugin with the following code sample,

 ```php
public function process($processparameters)
    {
        $inputparameters    = $processparameters['appInput'];
        $integrationdetails = $processparameters['integrationdetails'];
 
        $awsacesskey = $integrationdetails['accessKey'];
 
        $secretkey   = $integrationdetails['secretKey'];
    }
```

##Logging framework

Any event in the plugin has to be logged so that the plugin report will give enough information to the end user. There is a logging framework in the SDK and this has to be used.
Following types of messages are supported,
DEBUG, INFO and ERROR.
Here is the sample code to output log messages,
```
$this->getLogHandler()->log(INFO, “Info message”);
$this->getLogHandler()->log(DEBUG, “Debug message”);
```

##Handling failure scenarios
 The plugin should exit with error on any failure scenarios.To enable this SDK provides a method to exit with error. The format to invoke the event is as below,
 ```
    $message = “Error message”;
    trigger_error($message, E_USER_ERROR);
    ```
 
