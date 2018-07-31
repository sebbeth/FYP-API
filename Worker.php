<?php


// Include dependencies
require_once 'vendor/autoload.php';
require_once 'HelperFunctions.php';


function consumeJob() {

  $job = query("SELECT * FROM JobQueue LIMIT 1");
  if (!isset($job)) {
    echo 'Nothing to complete';
    return;
  }

  echo 'doing ' .debug($job);


  //query("DELETE FROM JobQueue WHERE id='{$job['id']}'");
}


echo 'WORKING...';

consumeJob();

echo 'DONE';














 ?>
