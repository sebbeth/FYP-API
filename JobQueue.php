<?php


class JobQueue {

public function addToQueue($inputId,$parameters,$accountId = '') {

    $parameters = json_encode($parameters);
    query("INSERT INTO Results ( account_id, input_id, status) VALUES ('$accountId', '$inputId','PENDING' ) ");
    $taskId = getLatestInsert();
    query("INSERT INTO JobQueue (id, account_id, input_id, parameters) VALUES ('$taskId', '$accountId', '$inputId','$parameters' ) ");


    // Now, start an instance of the worker so that the job gets processed on it's own thread.
    shell_exec('php Worker.php');

    return $taskId;
}





}



 ?>
