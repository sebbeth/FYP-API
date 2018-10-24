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


   function createSolution($input,$accountId) {
     header('Content-Type: application/json');

    // Do some validation
    if (!isset($input['type'])) {
      http_response_code(40);
      return;
    }

    http_response_code(200);
    if (isset($input['id'])) { // If this is set, we are updating a row not inserting a new one
      $id = $input['id'];
      unset($input['id']); // Pop the ID from the input as we don't want to save this in the JSON.

      $input = json_encode($input);
      query("UPDATE Solutions SET data='$input'  WHERE id='$id';");

      $output = [
        'id' => intval($id)
      ];
    } else {
      $input = json_encode($input);
      query("INSERT INTO Solutions ( provider, account_id, data ) VALUES ('0', '$accountId', '$input');");

      $output = [
        'id' => getLatestInsert()
      ];
    }

     echo json_encode($output);
     return;
   }


   function deleteSolution($key,$accountId) {
     query("DELETE FROM Solutions WHERE id='$key' AND account_id='$accountId';");
     header('Content-Type: application/json');
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
    $data['id'] = intval($query['id']);
    echo json_encode($data);
    return;
   }

   function getAllCustomSolutions($accountId) {
     if (!isset($accountId)) { // If no key is set, return error
       http_response_code(400);
       return;
     }
     $query = queryAll("SELECT * FROM Solutions WHERE account_id='$accountId' AND provider='0';"); // Get every input set for account
     $count = count($query);
     header('Content-Type: application/json');
     http_response_code(200);
     echo '[';
     foreach ($query as $solution) {
       // return the data column with the ID column added to it
       $data = json_decode($solution['data'],true);
       $data['id'] = intval($solution['id']);
       echo json_encode($data);
       if (--$count > 0) {
         echo ','; // For every row except the last, add a comma between rows.
       }
     }
     echo ']';
     return;

   }

   /*
   Returns a list of valid CPU identifiers
   */
   function getCPUOptions() {
     header('Content-Type: application/json');
     http_response_code(200);

     $cpu_options = [
       "Intel Xeon CPU E5-2670 @ 2.60GHz",
       "AMD Ryzen Threadripper 1950X",
       "Intel Core i5-4288U @ 2.60GHz"
     ];

     echo json_encode($cpu_options);

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
