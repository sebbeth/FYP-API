<?php

class ProviderInterface {

   function __construct()
   {
   }

   /*

   */
   function getProvider($key) { // Get a provider along with an array of it's solutions
     header('Content-Type: application/json');
     $output = '';
     $provider = queryAll("SELECT * FROM Providers WHERE id=$key LIMIT 1");
     $provider['solutions'] = $this->getSolutions($key); // Add each solution to the provider array
     $output .=  json_encode($provider);
     echo $output;
     http_response_code(200);
   }

   function getAllProviders() { // Get every provider along with an array of all their solutions
     header('Content-Type: application/json');
     $providers = queryAll("SELECT * FROM Providers"); // Get all providers
     $output = '[';
     $count = count($providers);
     foreach ($providers as $provider) {
       $provider['solutions'] = $this->getSolutions($provider['id']); // Add each solution to the provider array
       $output .=  json_encode($provider);
       if (--$count > 0) {
          $output .= ','; // For every row except the last, add a comma between rows.
       }
     }
     echo $output.= ']';
     http_response_code(200);
   }


   private function getSolutions($key) {
     $solutions = queryAll("SELECT * FROM Solutions WHERE provider=$key");
     foreach ($solutions as $key => $value) {
       $solutions[$key]['data'] = json_decode($value['data'],true);
     }
     return $solutions;
   }

 }


?>
