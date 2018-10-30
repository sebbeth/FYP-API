<?php


/**

API

**/


// Include dependencies
require_once 'vendor/autoload.php';
require_once 'HelperFunctions.php';
require_once 'ComparisonInterface.php';
require_once 'UploadInterface.php';
require_once 'SolutionInterface.php';
require_once 'ProviderInterface.php';
require_once 'AccountInterface.php';
require_once 'NormalisedSolution.php';


header("Access-Control-Allow-Origin: $allowedOrigin");
header('Access-Control-Allow-Credentials: true');
header('Content-Type: application/json');
header('Cache-Control: no-cache');


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


    if ($method == 'OPTIONS') {

      // Tell the Client this preflight holds good for only 20 days
      if($_SERVER['HTTP_ORIGIN'] == $allowedOrigin) {
        header("Access-Control-Allow-Origin: $allowedOrigin");
        header('Access-Control-Allow-Methods: GET, DELETE, POST, OPTIONS');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Max-Age: 1728000');
        header("Content-Length: 0");
      } else {
        header("HTTP/1.1 403 Access Forbidden");
        header("Content-Type: text/plain");
        echo "You cannot repeat this request";
      }

      return;
    }

    switch ($resource) {

      case ('comparison'):
      $accountId = authenticate();
      if ($accountId != null) { // Auth Required
        $ComparisonInterface = new ComparisonInterface();
        if ( ($method == 'POST') && (isset($input)) ) {
          $ComparisonInterface->createComparison($input,$fullHostname); // Initiate a comparison
          return;
        } else {
          http_response_code(400);
        }

        if ($method == 'GET') {

          if ($key == 0) {
            $ComparisonInterface->getAllResults($accountId); // Return every result for the account
            return;
          } else {
            $ComparisonInterface->getResult($key); // Return the results of a comparison
            return;
          }

        } else {
          http_response_code(400);
        }
      }
      break;

      case ('upload'):
      $accountId = authenticate();
      if ($accountId != null) { // Auth Required
        $UploadInterface = new UploadInterface();

        if ( ($method == 'POST') && (isset($input)) ) {
          $UploadInterface->addInputData($input,$accountId); // Initiate a comparison
          return;
        } else {
          http_response_code(400);
        }

        if ($method == 'DELETE') {
          if ($key == 0) {
            http_response_code(400);
          } else {
            $UploadInterface->deleteInputData($key);
            return;
          }

        }

        if ($method == 'GET') {
          if ($key == 0) {
            $UploadInterface->getAllInputData($accountId); // Return every input set for account
          } else {
            $UploadInterface->getInputData($key); // Return a single input set
          }
        } else {
          http_response_code(400);
        }
      }


      break;

      case ('solution'):
      $accountId = authenticate();
      if ($accountId != null) { // Auth Required
        $solutionInterface = new SolutionInterface();

        if ( ($method == 'POST') && (isset($input)) ) {
          $solutionInterface->createSolution($input,$accountId);
          return;
        } else {
          http_response_code(400);
        }

        if ( ($method == 'DELETE') && ($key != 0) ) {
          $solutionInterface->deleteSolution($key,$accountId);
          return;
        } else {
          http_response_code(400);
        }

        if ($method == 'GET') {
          if ($key == 0) {
               $solutionInterface->getAllCustomSolutions($accountId); // Return every input set for account
          } else {
              $solutionInterface->getCustomSolution($key,$accountId); // Return a single input set
          }
        } else {
          http_response_code(400);
        }
      }

      break;

      case ('cpu'):
      $accountId = authenticate();
      if ($accountId != null) { // Auth Required
        $solutionInterface = new SolutionInterface();

        if ($method == 'GET') {
          $solutionInterface->getCPUOptions();
        } else {
          http_response_code(400);
        }
      }
      break;

      case ('provider'):
      $accountId = authenticate();
      if ($accountId != null) { // Auth Required
        $providerInterface = new ProviderInterface();

        if ($method == 'GET') {
          if ($key == 0) {
            $providerInterface->getAllProviders(); // Return every provider
          } else {
            $providerInterface->getProvider($key); // Return a single provider set
          }
        } else {
          http_response_code(400);
        }
      }
      break;

      case ('account'):
      $accountInterface = new AccountInterface();
      if ($method == 'GET') {
        $accountId = authenticate();
        if ($accountId != null) { // Auth Required
          $accountInterface->getAccount();
          return;
        }
      }
      if ( ($method == 'POST') && (isset($input)) ) {
        $accountInterface->createAccount($input);
        return;
      } else {
        http_response_code(400);
      }


      default:
      http_response_code(404);
      break;
    }
  }

} catch (Exception $e) {
  echo $e;
}

?>
