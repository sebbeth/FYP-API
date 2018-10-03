<?php

class AccountInterface {

   function __construct()
   {
   }

   function getAccount() {
     header('Content-Type: application/json');
     http_response_code(200);
     // TODO input sanitation
     $user = $_SERVER['PHP_AUTH_USER'];
     $account = query("SELECT id,username FROM Accounts WHERE username='$user' LIMIT 1");

     echo json_encode($account);


   }

   function createAccount($input) {
     header('Content-Type: application/json');
     http_response_code(200);
     if (!isset($input['username']) || !isset($input['password'])) { // If no input is set, return error
       http_response_code(400);
       return;
     }
     // TODO DATA SANITATION!
     $user = $input['username'];
     $hash = password_hash($input['password'],PASSWORD_DEFAULT);


     query("INSERT INTO Accounts (username,password_hash) VALUES ('$user','$hash')");

   }

 }


?>
