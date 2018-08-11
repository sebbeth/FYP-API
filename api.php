<?php


/**

API

**/


// Include dependencies
require_once 'vendor/autoload.php';
require_once 'HelperFunctions.php';
require_once 'ComparisonInterface.php';
require_once 'UploadInterface.php';
require_once 'JobQueue.php';

header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Headers');

// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

$resource = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
$key = array_shift($request)+0;


/*

Endpoints:

api.php/comparison/{id} - GET

Returns the results for a comparison with given id

api.php/comparison/ - POST

Schedules an analysis on input data already stored in the database.

JSON input used to control parameters of analysis and indentifies which input dataset to use.

api.php/upload/ - GET

Returns every input data set for the user

api.php/upload/{id} - GET

Returns an existing input dataset from database with given id

api.php/upload/ - POST

Stores an input dataset in database.

*/


try {
if (isset($method)) {

switch ($resource) {

  case ('comparison'):

  $ComparisonInterface = new ComparisonInterface();


  if ( ($method == 'POST') && (isset($input)) ) {
    $ComparisonInterface->createComparison($input); // Initiate a comparison
    return;
  } else {
    http_response_code(400);
  }

  if ($method == 'GET') {

    if ($key == 0) {
      $ComparisonInterface->getAllResults(1); // Return every result for the account
    } else {
      $ComparisonInterface->getResult($key); // Return the results of a comparison
    }
  } else {
    http_response_code(400);
  }

  break;

  case ('upload'):

  $UploadInterface = new UploadInterface();

  if ( ($method == 'POST') && (isset($input)) ) {
    $UploadInterface->addInputData($input); // Initiate a comparison
    return;
  } else {
    http_response_code(400);
  }

  if ($method == 'GET') {
    if ($key == 0) {
      $UploadInterface->getAllInputData(1); // Return every input set for account
    } else {
      $UploadInterface->getInputData($key); // Return a single input set
    }
  } else {
    http_response_code(400);
  }


  break;

  case ('account'):
  //TODO
  break;

  default:
    http_response_code(400);
  break;


}
}

} catch (Exception $e) {
  echo $e;
}

?>
