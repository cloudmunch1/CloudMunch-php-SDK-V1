
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
