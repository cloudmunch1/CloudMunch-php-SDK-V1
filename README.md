# CloudMunch-php-SDK-V2
CloudMunch SDK for PHP provides helper classes for CloudMunch plugin development.

###Download SDK
We recommend using [Composer](https://getcomposer.org/ "Composer") as package manager. All you need to install the sdk is to have the following entry in your composer.json file.

```json
{
    "require": {
        "cloudmunch/php-sdk-v1":"dev-master"
    }
}
```

###Usage
Here is a sample plugin code

```php
<?php
require __DIR__ . '/vendor/autoload.php';
use CloudMunch\AppAbstract;
class SampleApp extends AppAbstract{
	
	public function process($processparameters) {
	    //To read the input to the plugin
		$inputparameters = $processparameters['appInput'];
		$inputvalue = $inputparameters-> input1;
		//To get the credentials to any integration read the integration specific details
		//from the array $integrationdetails
		$integrationdetails = $processparameters['integrationdetails'];
		$username=$integrationdetails[username];
		$password=$integrationdetails[password];
		}
	}
		
		//LifeCycle methods of a plugin
	$sampleapp = new SampleApp();
    $sampleapp->initialize();
    $processInput=$sampleapp->getProcessInput();
    $sampleapp->process($processInput);
    $sampleapp->performAppcompletion();

```

To write a cloudmunch plugin for Amazon Web Service,all you need to do is create a project and composer.json should have the following,

```json
{
    "require": {
        "cloudmunch/php-sdk-v1":"dev-master",
        "aws/aws-sdk-php": "2.*"
    }
}
```

The plugin can get the Amazon Web Service credentials as follows

```
	public function process($processparameters) {
	    //To read the input to the plugin
		$inputparameters = $processparameters['appInput'];
		$region = $inputparameters-> region;
		//Reading the credentials from SDK
		
		$integrationdetails = $processparameters['integrationdetails'];
		$accessKey=$integrationdetails[accessKey];
		$secretKey=$integrationdetails[secretKey];
		}
	}
```
