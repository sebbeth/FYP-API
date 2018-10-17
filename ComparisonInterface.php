<?php

class ComparisonInterface {

  function __construct()
  {
  }


  /*
  input data schema:

  {
  ""
}
*/


function createComparison($input) {

  // First parse inputs and solutions into comma delineated strings
  $inputs = '';
  if (isset($input['inputs'])) {
    $inputs = implode (",", $input['inputs']);
  }
  $solutions = '';
  if (isset($input['solutions'])) {
    $solutions = implode (",", $input['solutions']);
  }

  $parameters = json_encode($input['parameters']);



  // Store a result object to hold the output
  query("INSERT INTO Results ( account_id, inputs, solutions, status) VALUES ('{$input['account']}', '$inputs','$solutions','PENDING' ) ");
  $taskId = getLatestInsert();
  // Schedule the comparison to be calculated
  query("INSERT INTO JobQueue (id, parameters) VALUES ('$taskId', '$parameters' ) ");

  // Now, start an instance of the worker so that the job gets processed on it's own thread.
  $handle = curl_init();
  $url = $fullHostname . "worker.php/";
  // Set the url
  curl_setopt($handle, CURLOPT_URL, $url);
  // Set the result output to be a string.
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
  curl_exec($handle);
  curl_close($handle);


  header('Content-Type: application/json');
  $output = [
    'id' => $taskId,
    'status' => 'queued'
  ];
  echo json_encode($output);
  http_response_code(200);
}

/*

*/
function getResult($key) {
  header('Content-Type: application/json');
  http_response_code(200);
  $results = query("SELECT * FROM Results WHERE id='$key'");

  if (sizeof($results) == 0) {
    echo '{"status":"not found"}';
  } else {

    // Parse the arrays that are being stored as strings
    $solutions = array_map('intval', explode(",", $results['solutions']));
    $inputs = array_map('intval', explode(",", $results['inputs']));
    $providers =  $this->getProvidersFromSolutions($solutions);

    // Data is stored as a string, parse it into an object
    $data = json_decode($results['data'],true);
    foreach ($data as $key => $value) {
      $data[$key]['title'] = $this->getTitleOfSolution($data[$key]['solution']); // Set the title for the solution
      $data[$key]['provider'] = $this->getProvidersFromSolutions([$data[$key]['solution']])[0]; // Get the provider for the solution
    }

    // Make an array with the data to be sent, then convert it to JSON.
    $output = [
      'timestamp' => $results['timestamp'],
      'inputs' => $inputs,
      'solutions' => $solutions,
      'providers' => $providers,
      'status' => $results['status'],
      'data' => $data];
      echo json_encode($output);
    }
  }

  /*

  */
  function getAllResults($accountId) {
    if (!isset($accountId)) { // If no key is set, return error
      http_response_code(400);
      return;
    }
    http_response_code(200);
    $query = queryAll("SELECT * FROM Results WHERE account_id='$accountId' ORDER BY timestamp DESC"); // Get every input set for account
    header('Content-Type: application/json');
    echo '[';// Open the brackets
    $count = count($query);
    foreach ($query as $key => $value) {

      $solutions = '[' . $value['solutions'] . ']';
      $inputs = '[' . $value['inputs'] . ']';
      $providers = '[' . implode(",", $this->getProvidersFromSolutions(array_map('intval', explode(",", $value['solutions'])))) . ']';

      // Data is stored as a string, parse it into an array so that it can be maniplutated
      $data = json_decode($value['data'],true);
      foreach ($data as $data_key => $data_value) {
        $data[$key]['title'] = $this->getTitleOfSolution($data[$data_key]['solution']); // Set the title for the solution
        $data[$key]['provider'] = $this->getProvidersFromSolutions([$data[$data_key]['solution']])[0]; // Get the provider for the solution
      }

      //   $data = json_decode($value['data'],true);   // Data is stored as a string, parse it into a JSON array of objects

      echo  '{ "id":"' . $value['id'] . '",' .
        '"inputs":' . $inputs . ',' .
        '"solutions":' . $solutions . ',' .
        '"providers":' . $providers . ',' .
        '"timestamp":"' . $value['timestamp'] .
        '", "data":' . json_encode($data) . '}'; // Now turn $data back into a JSON string
        if (--$count > 0) {
          echo ','; // For every row except the last, add a comma between rows.
        }
      }
      echo ']'; // Close the brackets

    }



    // Helper functions

    /*
    getProvidersFromSolutions
    @param array - an array of solution ids stored as associative array

    @return array - ids of each provider used stored as associative array
    */
    private function getProvidersFromSolutions($solutions) {
      $providers = [];
      foreach ($solutions as $solutionId) {
        $providers[] = intval(query("SELECT Providers.id FROM Providers INNER JOIN Solutions ON Solutions.provider = Providers.id WHERE Solutions.id = '$solutionId' LIMIT 1")['id']);
      }
      return $providers;
    }

    private function getTitleOfSolution($solutionId) {
      $result = query("SELECT title FROM Solutions WHERE id='$solutionId' LIMIT 1");
      if (isset($result)) {
        return $result['title'];
      }
      return null;
    }


  }


  ?>
