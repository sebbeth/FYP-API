<?php
/**



*/

class Calculator {


  /*

  */
  function consumeJob() {
    global $dbConnection;

    $job = query("SELECT * FROM JobQueue LIMIT 1");
    if (!isset($job)) {
      echo 'Nothing to complete';
      return;
    }

    echo debug($job);

    $parameters = json_decode($job['parameters'],true);
    $save = true;
    if (isset($parameters['no-save'])) {
      $save = false;
    }

    // Create a results row.
    if ($save) {
      query("INSERT INTO Results (id,account_id,input_id,status,data) VALUES ('{$job['id']}','{$job['account_id']}','{$job['input_id']}','PROCESSING','');");
    }

    // Do the computation
    $resultData = $this->compute($job);
    debug($resultData);

    // Upon completion
    if ($save) {
      query("UPDATE Results SET status='DONE', data='$resultData' WHERE id='{$job['id']}';");
    }
    // Drop the Job from the job queue.
    if ($save) {
      query("DELETE FROM JobQueue WHERE id='{$job['id']}'");
    }
  }

  /*

  */
  function compute($job) {
    global $dbConnection;


    $totalCost = 0;

    $inputDataSet = queryAll("SELECT * FROM InputData WHERE id='{$job['input_id']}'"); // TODO, extend this to many input sets


    // TODO put this in a foreach
    $solution = query("SELECT * FROM Solutions WHERE id='{$job['solutions']}' LIMIT 1");


    // TODO put this in a foreach
    $inputData = json_decode($inputDataSet[0]['data'],true);

    debug($inputData);

    // Iterate through every hour calculating the total cost of that segment
    $segmentsRemaining = $inputData['run_for_hours'];

    // Every hour, add the cost of running the workload
    for ($i=0; $i < $segmentsRemaining; $i++) {

      if ($i == 0) { // Add the initial costs
        $totalCost = $solution['initial_cost'];
      }

      

    }



    return $dbConnection->real_escape_string("{'total_cost':'$totalCost'}");
  }


}

?>
