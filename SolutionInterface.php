<?php

class SolutionInterface {

   function __construct()
   {
   }


   /*
    input data schema:

    {
    ""
    }
   */


   function createSolution($input) {

  //   echo json_encode($output);

     http_response_code(200);
   }

   /*

   */
   function getCustomSolution($key) {
     header('Content-Type: application/json');
    //  $results = query("SELECT * FROM Results WHERE id='$key'");
     http_response_code(200);
   }

   function getAllCustomSolutions($accountId) {
     if (!isset($accountId)) { // If no key is set, return error
       http_response_code(400);
       return;
     }
    // $query = queryAll("SELECT * FROM Results WHERE account_id='$accountId' LIMIT 10"); // Get every input set for account
     header('Content-Type: application/json');

     http_response_code(200);
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
