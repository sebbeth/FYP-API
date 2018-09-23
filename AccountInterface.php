<?php

class AccountInterface {

   function __construct()
   {
   }

   function getAccount() {
     header('Content-Type: application/json');
     // TODO input sanitation
     $user = $_SERVER['PHP_AUTH_USER'];
     $account = query("SELECT id,username FROM Accounts WHERE username='$user' LIMIT 1");

     echo json_encode($account);

     http_response_code(200);

   }

   function createAccount($input) {
     header('Content-Type: application/json');
     if (!isset($input['username']) || !isset($input['password'])) { // If no input is set, return error
       http_response_code(400);
       return;
     }
     // TODO DATA SANITATION!
     $user = $input['username'];
     $hash = password_hash($input['password'],PASSWORD_DEFAULT);


     query("INSERT INTO Accounts (username,password_hash) VALUES ('$user','$hash')");

     echo 'done';
     http_response_code(200);

   }
   /*


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
   }*/

 }


?>
