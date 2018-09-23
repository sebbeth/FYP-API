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
     $solutions = queryAll("SELECT * FROM Solutions WHERE provider=$key");
     $provider['solutions'] = $solutions; // Add each solution to the provider array
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
       $solutions = queryAll("SELECT * FROM Solutions WHERE provider='{$provider['id']}'");
       $provider['solutions'] = $solutions; // Add each solution to the provider array
       $output .=  json_encode($provider);
       if (--$count > 0) {
          $output .= ','; // For every row except the last, add a comma between rows.
       }
     }
     echo $output.= ']';
     http_response_code(200);
   }

 }


?>
