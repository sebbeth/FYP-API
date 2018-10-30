<?php


// Include dependencies
require_once 'vendor/autoload.php';
require_once 'HelperFunctions.php';
require_once 'NormalisedSolution.php';
require_once 'Calculator.php';


$calculator = new Calculator();

echo 'WORKING...';

$calculator->consumeJob();

echo 'DONE';


 ?>
