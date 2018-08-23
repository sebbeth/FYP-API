<?php


// Include dependencies
require_once 'vendor/autoload.php';
require_once 'HelperFunctions.php';


function consumeJob() {
  global $dbConnection;

  $job = query("SELECT * FROM JobQueue LIMIT 1");
  if (!isset($job)) {
    echo 'Nothing to complete';
    return;
  }

  echo debug($job);
  // Start of computation
  query("INSERT INTO Results (id,account_id,input_id,status,data) VALUES ('{$job['id']}','{$job['account_id']}','{$job['input_id']}','PROCESSING','');");


  $resultData = $dbConnection->real_escape_string("{'result':'data'}");

  // Upon completion
  query("UPDATE Results SET status='DONE', data='$resultData' WHERE id='{$job['id']}';");

  // Drop the Job from the job queue.
  query("DELETE FROM JobQueue WHERE id='{$job['id']}'");
}


echo 'WORKING...';

consumeJob();

echo 'DONE';














 ?>
