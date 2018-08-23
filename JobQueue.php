<?php


class JobQueue {

public function addToQueue($inputId,$parameters,$accountId = '') {

    $parameters = json_encode($parameters);
    query("INSERT INTO JobQueue ( account_id, input_id, parameters) VALUES ('$accountId', '$inputId','$parameters' ) ");

    // Now, start an instance of the worker so that the job gets processed on it's own thread.
    shell_exec('php Worker.php');
    
    return getLatestInsert();
}





}



 ?>
