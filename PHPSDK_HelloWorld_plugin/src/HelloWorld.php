<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '../src/HelloDisplay.class.php';

$hellodisplay= new HelloDisplay();
$hellodisplay->initialize();
$processInput = $hellodisplay->getProcessInput();
$hellodisplay->process($processInput);
$hellodisplay->performAppcompletion();
?>