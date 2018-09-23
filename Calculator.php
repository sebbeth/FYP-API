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
    if ( isset($parameters['no-save']) ) {
      $save = false;
    }

    // Create a results row.
    if ($save) {
      query("UPDATE Results SET status='PROCESSING' WHERE id='{$job['id']}';");
    }

    // Do the computation
    $resultData = $this->compute($job);


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

//    debug($job);
    $totalCost = 0;
    // Put the job parameters in an associative array.
    $parameters = json_decode($job['parameters'],true);
    debug('Parameters:');
    debug($parameters);

    $inputDataSet = queryAll("SELECT * FROM InputData WHERE id='{$job['input_id']}'"); // TODO, extend this to many input sets


    // TODO put this in a foreach
    $solution = query("SELECT * FROM Solutions WHERE id='{$job['solutions']}' LIMIT 1");


    // TODO put this in a foreach
    $inputData = json_decode($inputDataSet[0]['data'],true);

  //  debug($inputData);

    $totalCostForSegment = [];

    // Iterate through every hour calculating the total cost of that segment
    $segmentsRemaining = $parameters['run_time_hours'];

    // Every hour, add the cost of running the workload
    for ($i=0; $i < $segmentsRemaining; $i++) {

      if ($i == 0) { // Add the initial costs
        $totalCost = $solution['initial_cost'];
      }

      $totalCostForSegment[$i] = $totalCost + ($i * 50);


    }


    // Now let's colate the output

    $results = [];
    $results['0']['total_cost'] = $totalCost;
    $results['0']['segments'] = $totalCostForSegment;


    debug(json_encode($results));
    return $dbConnection->real_escape_string(json_encode($results));
  }


}

?>
