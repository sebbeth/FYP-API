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
     echo "{'id':'$id',
            'status':'queued'}";
     http_response_code(200);
   }

   /*

   */
   function getResult($key) {
     header('Content-Type: application/json');
      $results = query("SELECT * FROM Results WHERE input_id='$key'");
     if (sizeof($results) == 0) {
       echo "{'status':'not found'}";
     } else {
       echo "{'status':{$results['status']},
          'data':{$results['data']}}";
     }
     http_response_code(200);
   }







 }


?>
