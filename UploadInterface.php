<?php

class UploadInterface {

  // public $edible;
  // public $color;

  /* function __construct($edible, $color="green")
   {
       $this->edible = $edible;
       $this->color = $color;
   }*/


   /*
    Input schema:
    {
	   "account":" ACCOUNT ID ",
	    "data":{ PAYLOAD }
     }





   */
   function addInputData($input) {

     if (!isset($input['data'])) { // If no input is set, return error
       http_response_code(400);
       return;
     }
     $data = json_encode($input['data']);

     if (isset($input['account'])) {
       query("INSERT INTO InputData ( account_id, data) VALUES ('{$input['account']}', '$data');");
     } else {
       query("INSERT INTO InputData (data) VALUES ('$data');");
     }
     header('Content-Type: application/json');
     $output = [
       'id' => getLatestInsert()
     ];
     echo json_encode($output);

     http_response_code(200);
   }

   /*

   */
   function getInputData($key) {

     if (!isset($key)) { // If no key is set, return error
       http_response_code(400);
       return;
     }

     $query = query("SELECT * FROM InputData WHERE id='$key' LIMIT 1");
     header('Content-Type: application/json');
     echo $query['data'];
     http_response_code(200);
   }


   function getAllInputData($accountId) {
     if (!isset($accountId)) { // If no key is set, return error
       http_response_code(400);
       return;
     }
     $query = queryAll("SELECT * FROM InputData WHERE account_id='$accountId' LIMIT 10"); // Get every input set for account
     header('Content-Type: application/json');
     echo '{';// Open the brackets
     $count = count($query);
     foreach ($query as $key => $value) {
       echo '"' . $value['id'] . '":' . $value['data']; // Print the data, we expect it to be valid JSON.
       if (--$count > 0) {
         echo ','; // For every row except the last, add a comma between rows.
       }
    }
    echo '}'; // Close the brackets
     http_response_code(200);
   }




 }


?>
