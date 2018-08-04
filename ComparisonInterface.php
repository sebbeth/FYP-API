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
         'status' => $results['status'],
          'data' => $results['data']];
          echo json_encode($output);
     }
     http_response_code(200);
   }







 }


?>
