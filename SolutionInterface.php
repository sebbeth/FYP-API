<?php

class SolutionInterface {

   function __construct()
   {
   }


   /*


   Hourly price:


   How much of stuff do we have
    CPU identifier
    Memory capacity
    Storage capacity
    I/O capacity

   Costs of each resource at 100% capacity (hourly)
    CPU cost
    Memory cost
    Storage cost
    I/O cost


   Machine costs - things we need to pay for regardless of utlisiation (hourly)
    Electical cost
    labour cost
    //network cost


    Setup machine costs - things we need to pay for either once or periodically
      Purchase price





   */


   function createSolution($input) {

  //   echo json_encode($output);

     http_response_code(200);
   }

   /*

   */
   function getCustomSolution($key,$accountId) {
     if (!isset($accountId)) { // If no key is set, return error
       http_response_code(400);
       return;
     }
     header('Content-Type: application/json');
     $query = query("SELECT * FROM Solutions WHERE id='$key' AND account_id='$accountId' AND provider='0' LIMIT 1;"); // Get every input set for account

     if (sizeof($query) == 0) {
       http_response_code(400);
       return;
     }
     header('Content-Type: application/json');
     http_response_code(200);
       // return the data column with the ID column added to it
    $data = json_decode($query['data'],true);
    $data['id'] = $query['id'];
    echo json_encode($data);
    return;
   }

   function getAllCustomSolutions($accountId) {
     if (!isset($accountId)) { // If no key is set, return error
       http_response_code(400);
       return;
     }
     $query = queryAll("SELECT * FROM Solutions WHERE account_id='$accountId' AND provider='0';"); // Get every input set for account
     header('Content-Type: application/json');
     http_response_code(200);
     echo '[';
     foreach ($query as $solution) {
       // return the data column with the ID column added to it
       $data = json_decode($solution['data'],true);
       $data['id'] = $solution['id'];
       echo json_encode($data);

     }
     echo ']';
     return;

   }

   function getSolutionsForProvider($providerId) {
     header('Content-Type: application/json');
     http_response_code(200);

   }


   function getPublicSolution($key) {
     header('Content-Type: application/json');
     http_response_code(200);
   }

 }


?>
