<?php


class JobQueue {

public function addToQueue($inputId,$parameters,$accountId = '') {

    $parameters = json_encode($parameters);
    query("INSERT INTO JobQueue ( account_id, input_id, parameters) VALUES ('$accountId', '$inputId','$parameters' ) ");
    return getLatestInsert();
}





}



 ?>
