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

    $results = [];



    // Get each solution being examined
    foreach (explode(",",$comparison['solutions']) as $solutionIndex => $solutionId ) {



      $solution = query("SELECT * FROM Solutions WHERE id='$solutionId' LIMIT 1;");

      if ( sizeof($solution) > 0 ) {


        // Do some normalisation here
        debug('solution');
        debug($solution);
        $solution_data = json_decode($solution['data'],true);

        $segments = [];

        //Now get each of the input sets

        foreach (explode(",",$comparison['inputs']) as $inputIndex => $inputId) {

          $inputSet = query("SELECT * FROM InputData WHERE id='$inputId' LIMIT 1;");
          if ( sizeof($inputSet) > 0 ) {

            $logs = json_decode($inputSet['data'],true);

            $segmentsRemaining = count($logs);
            for ($i=0; $i < $segmentsRemaining; $i++) {
              $segment = 0;
              if (sizeof($solution_data['setup_costs']) > 0) {// Add setup costs to the 0th segment
                if ($i == 0) {
                  foreach ($solution_data['setup_costs'] as $setup_cost) { // Add each setup cost to the segment
                    $segment = $segment + doubleval($setup_cost['cost']);
                  }
                }
              }

              // Go through the usage costs adding them to the segment

              if (sizeof($solution_data['usage_costs']) > 0) {

                foreach ($solution_data['usage_costs'] as $cost) {

                  if ((isset($cost['type'])) && (isset($cost['value'])) ) {

                    switch ($cost['type']) {
                      case 'C':
                      $segment = $segment + doubleval($cost['value']);
                      break;
                      case 'M':
                      $segment = $segment + doubleval($cost['value']);
                      break;
                      case 'D':
                      $segment = $segment + doubleval($cost['value']);
                      break;
                      case 'S':
                      $segment = $segment + doubleval($cost['value']);
                      break;
                      case 'any':
                      $segment = $segment + doubleval($cost['value']);
                      break;
                      default:
                      break;
                    }
                  }
                }
              }


              // Add the previous segment to this one so that we get a total
              if ($i != 0) {
                $segment = $segment + $segments[$i-1];
              }
              // Round to remove floating point errors
              $segment = round($segment,2);

              array_push($segments,$segment);
            }
          }
        }




        array_push($results,[
          "total_cost" => end($segments),
          "title" => $solution_data['title'],
          "solution" => intval($solutionId),
          "segments" => $segments
        ]);
        debug(end($segments));

      }
    }

    return $dbConnection->real_escape_string(json_encode($results));
  }


}

?>
