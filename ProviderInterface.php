<?php

class ProviderInterface {

   function __construct()
   {
   }

   /*

   */
   function getProvider($key) {
     header('Content-Type: application/json');

     $query = queryAll("SELECT * FROM Providers WHERE id=$key LIMIT 1"); // Get every input set for account
     echo json_encode($query);
     http_response_code(200);
   }

   function getAllProviders() {

     $query = queryAll("SELECT * FROM Providers"); // Get every input set for account
     header('Content-Type: application/json');
     echo json_encode($query);
     http_response_code(200);
   }

 }


?>
