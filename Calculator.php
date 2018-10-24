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
    $comparison = query("SELECT * FROM Results WHERE id='{$job['id']}' LIMIT 1");
    // Do the computation
    $resultData = $this->compute($job,$comparison);
    // Upon completion
    if ($save) {
      query("UPDATE Results SET status='DONE', data='$resultData' WHERE id='{$job['id']}';");
    }
    // Drop the Job from the job queue.
    if ($save) {
    //    query("DELETE FROM JobQueue WHERE id='{$job['id']}'");
    }

    // Now check if there are any other tasks to complete
    $jobs = queryAll("SELECT id FROM JobQueue");
    if (sizeof($jobs) != 0) { // If there are, consume them too.
    //  $this->consumeJob();
    }
  }

  /*
    compute

    This is where the cost analysis is performed
  */
  function compute($job,$comparison) {
    global $dbConnection;
    //    debug($job);
    $totalCost = 0;
    // Put the job parameters in an associative array.
    $parameters = json_decode($job['parameters'],true);
    debug('Parameters:');
    //  debug($parameters);

    $inputDataSets = [];
    foreach (explode(",",$comparison['inputs']) as $inputId ) { // Construct an array of all the input sets being used for this comparison
      $set = query("SELECT * FROM InputData WHERE id='$inputId' LIMIT 1");
      if ($set != null) { $inputDataSets[] = $set; }
    }
    debug("--Input--");
    //debug($inputDataSets);

    $results = [];
    debug("--Solutions--");

    // Iterate through each solution, calculating pricing information for each.
    foreach (explode(",",$comparison['solutions']) as $solutionIndex => $solutionId ) {
      $solutionId = intval($solutionId);
      $solution = query("SELECT * FROM Solutions WHERE id='$solutionId' LIMIT 1");
      if (!isset($solutionId)) {
        break;
      }
      debug($solution);

      // TODO put this in a foreach
      debug("--test--");
      $inputData = json_decode($inputDataSets[0]['data'],true);

      $totalCostForSegment = [];
      // Iterate through every hour calculating the total cost of that segment
      $segmentsRemaining = count($inputData);//$parameters['run_time_hours'];
      debug($segmentsRemaining);
      // Every hour, add the cost of running the workload
      for ($i=0; $i < $segmentsRemaining; $i++) {
        $initialCost = 0;
        if ($i == 0) { $initialCost = 5.0; } // Add the initial costs
        $previousTotalCost = 0;
        if ($i > 0) {$previousTotalCost = $totalCostArray[$i-1]; }

        // Tally up all the costs for this comparison
        $totalCostArray[$i] = $initialCost + $previousTotalCost + doubleval(0.01);
      }
      // Now let's colate the output
      $results[$solutionIndex]['total_cost'] = end($totalCostArray);
      $results[$solutionIndex]['solution'] = $solutionId;
      $results[$solutionIndex]['segments'] = $totalCostArray;
    }

    debug(json_encode($results));
    return $dbConnection->real_escape_string(json_encode($results));
  }


}

?>
