<?php

class ComparisonInterface {

   private $jobQueue;

   function __construct()
   {
       $this->jobQueue = new JobQueue();
   }


   /*
    input data schema:

    {
    ""
    }
   */


   function createComparison($input) {
     $id = $this->jobQueue->addToQueue($input['input_id'],$input['parameters']);
     header('Content-Type: application/json');
     $output = [
       'id' => $id,
       'status' => 'queued'
     ];
     echo json_encode($output);

     http_response_code(200);
   }

   /*

   */
   function getResult($key) {
     header('Content-Type: application/json');
      $results = query("SELECT * FROM Results WHERE id='$key'");

     if (sizeof($results) == 0) {
       echo '{"status":"not found"}';
     } else {
       // Make an array with the data to be sent, then convert it to JSON.
       $output = [
         'timestamp' => $results['timestamp'],
         'status' => $results['status'],
          'data' => $results['data']];
          echo json_encode($output);
     }
     http_response_code(200);
   }

   function getAllResults($accountId) {
     if (!isset($accountId)) { // If no key is set, return error
       http_response_code(400);
       return;
     }
     $query = queryAll("SELECT * FROM Results WHERE account_id='$accountId' LIMIT 10"); // Get every input set for account
     header('Content-Type: application/json');
     echo '[';// Open the brackets
     $count = count($query);
     foreach ($query as $key => $value) {
       echo  '{ "id":"' . $value['id'] . '",' .
          '"timestamp":"' . $value['timestamp'] .
           '", "data":' . $value['data'] . '}'; // Print the data, we expect it to be valid JSON.
       if (--$count > 0) {
         echo ','; // For every row except the last, add a comma between rows.
       }
    }
    echo ']'; // Close the brackets
     http_response_code(200);
   }







 }


?>
