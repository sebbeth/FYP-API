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
     query("DELETE FROM JobQueue WHERE id='{$job['id']}'");
    }

    // Now check if there are any other tasks to complete
    $jobs = queryAll("SELECT id FROM JobQueue");
    if (sizeof($jobs) != 0) { // If there are, consume them too.
        $this->consumeJob();
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
        $normalisedSolution = new NormalisedSolution($solution);
        debug($normalisedSolution->getTitle());
        debug($normalisedSolution->getSpec());



        $segments = [];
        $wastedResources = 0;
        $underperformance_flags = [];

        //Now get each of the input sets
        $start_date = '';

        foreach (explode(",",$comparison['inputs']) as $inputIndex => $inputId) {

          $segmentForThisInputSet = [];

          $inputSet = query("SELECT * FROM InputData WHERE id='$inputId' LIMIT 1;");
          // Get the start date for this set
          $start_date = $inputSet['start_date'];
          if ( sizeof($inputSet) > 0 ) {

            $logs = json_decode($inputSet['data'],true);

            $segmentsRemaining = count($logs);
            for ($i=0; $i < $segmentsRemaining; $i++) {
              $segment = 0;
              if ($normalisedSolution->hasSetupCosts()) {// Add setup costs to the 0th segment
                if ($i == 0) {
                  foreach ($normalisedSolution->getSetupCosts() as $setup_cost) { // Add each setup cost to the segment
                    $segment = $segment + doubleval($setup_cost['cost']);
                  }
                }
              }

              $cpu_utilisation = 0;
              $mem_utilisation = 0;
              $disk_utilisation = 0;
              $storage_utilisation = 0;


              // Go through the usage costs adding them to the segment
                foreach ($logs[$i] as $usageType => $usageValue) {
                      switch ($usageType) {
                      case 'C':
                      // TODO

                      if (!isset($normalisedSolution->getSpec()['C'])) {
                        $cpu_utilisation = 0;
                        $underperformance_flags['C'] = true;
                        break;
                      }
                      $cpu_capacity = 100.0;
                      $cpu_utilisation = doubleval($usageValue) / $cpu_capacity;
                      if (doubleval($usageValue) > $cpu_capacity) {  // If we are overusing the resource
                        $underperformance_flags['C'] = true;
                        $segment = $segment + $cpu_capacity; // add cost of 100% utilisation
                        break;
                      }
                      $segment = $segment + doubleval($normalisedSolution->getUsageCosts()['C']) * $cpu_utilisation;
                      $wastedResources = $wastedResources + doubleval($normalisedSolution->getUsageCosts()['C']) * (1 - $cpu_utilisation);



                      break;
                      case 'M':
                      if ($normalisedSolution->getSpec()['M'] == 0) {
                        $mem_utilisation = 0;
                        $underperformance_flags['M'] = true;
                        break;
                      }
                      $mem_utilisation = doubleval($usageValue) / $normalisedSolution->getSpec()['M'];
                      if (doubleval($usageValue) > $normalisedSolution->getSpec()['M']) {  // If we are overusing the resource
                        $underperformance_flags['M'] = true;
                        $segment = $segment + doubleval($normalisedSolution->getUsageCosts()['M']); // add cost of 100% utilisation
                        break;
                      }
                      $segment = $segment + doubleval($normalisedSolution->getUsageCosts()['M']) * $mem_utilisation;
                      $wastedResources = $wastedResources + doubleval($normalisedSolution->getUsageCosts()['M']) * (1 - $mem_utilisation);

                      break;
                      case 'D':
                      if ($normalisedSolution->getSpec()['D'] == 0) {
                        $disk_utilisation = 0;
                        $underperformance_flags['D'] = true;
                        break;
                      }
                      $disk_utilisation = doubleval($usageValue) / $normalisedSolution->getSpec()['D'];
                      if (doubleval($usageValue) > $normalisedSolution->getSpec()['D']) {  // If we are overusing the resource
                        $underperformance_flags['D'] = true;
                        $segment = $segment + doubleval($normalisedSolution->getUsageCosts()['D']); // add cost of 100% utilisation
                        break;
                      }
                      $segment = $segment + doubleval($normalisedSolution->getUsageCosts()['D']) * $disk_utilisation;
                      $wastedResources = $wastedResources + doubleval($normalisedSolution->getUsageCosts()['D']) * (1 - $disk_utilisation);

                      break;
                      case 'S':
                        if ($normalisedSolution->getSpec()['S'] == 0) {
                          $storage_utilisation = 0;
                          $underperformance_flags['S'] = true;
                          break;
                        }
                        $storage_utilisation = doubleval($usageValue) / $normalisedSolution->getSpec()['S'];
                        if (doubleval($usageValue) > $normalisedSolution->getSpec()['S']) {  // If we are overusing the resource
                          $underperformance_flags['S'] = true;
                          $segment = $segment + doubleval($normalisedSolution->getUsageCosts()['S']); // add cost of 100% utilisation
                          break;
                        }
                        $segment = $segment + doubleval($normalisedSolution->getUsageCosts()['S']) * $storage_utilisation;
                        $wastedResources = $wastedResources + doubleval($normalisedSolution->getUsageCosts()['S']) * (1 - $storage_utilisation);


                      break;
                      case 'any':
                      break;
                      default:
                        // Add the default cost
                        $segment = $segment + doubleval($normalisedSolution->getUsageCosts()['any']);
                      break;
                    }
                }
                // store the utilisation for later analysis
                $normalisedSolution->pushUtilisation($cpu_utilisation,$mem_utilisation,$disk_utilisation,$storage_utilisation);



              // Add the previous segment to this one so that we get a total
              if ($i != 0) {
                $segment = $segment + $segmentForThisInputSet[$i-1];
              }

              array_push($segmentForThisInputSet,$segment);
            }
          }
          // Now concatonate each $segmentForThisInputSet into $segments
          foreach ($segmentForThisInputSet as $key => $value) {
            if ($key > sizeof($segments)) { // We've overshot the end of the array, so start pushing to it`
              array_push($segments,$value);
            } else {
              $segments[$key] = $segments[$key] + $value;  // Add this element to the exiting elements in $segments
            }
          }
        }


        // Round to remove floating point errors

        foreach ($segments as $key => $value) {
          $segments[$key] = round($value,2);
        }

        debug($normalisedSolution->getUtilisation());
        $averageUtilistaion = $normalisedSolution->getAverageUtilisation();

        $toStore = [
          "total_cost" => end($segments),
          "start_date" => $start_date,
          "title" => $normalisedSolution->getTitle(),
          "solution" => $normalisedSolution->getID(),
          "utilisation_cpu" => $averageUtilistaion['C'],
          "utilisation_memory" => $averageUtilistaion['M'],
          "utilisation_io" => $averageUtilistaion['D'],
          "utilisation_storage" => $averageUtilistaion['S'],
          "wasteCost" => $wastedResources,
          "segments" => $segments
        ];

        if (!empty($underperformance_flags)) {
          $toStore['underperforming'] = 'true';
        }

        array_push($results,$toStore);
        debug(end($segments));

      }
    }

    return $dbConnection->real_escape_string(json_encode($results));
  }


}

?>
