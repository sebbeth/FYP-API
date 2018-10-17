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
   function addInputData($input,$accountId) {

     if (!isset($input)) { // If no input is set, return error
       http_response_code(400);
       return;
     }

     // Now break input data appart and store it


    $description = '';
    $start_date = '';
    $spec = '';
    $data = '';

    if (array_key_exists('description',$input)) {
        $description = $input['description'];
      }
    if (array_key_exists('start_date',$input)) {
        $start_date = $input['start_date'];
      }
      if (array_key_exists('data',$input)) {
        $data = json_encode($input['data']);
      }
      if (array_key_exists('spec',$input)) {
        $spec = json_encode($input['spec']);
      }

     if (isset($accountId)) {
       query("INSERT INTO InputData ( account_id, description, start_date, spec, data) VALUES ('$accountId', '$description', '$start_date', '$spec', '$data');");
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


   function deleteInputData($key) {
     if (!isset($key)) { // If no key is set, return error
       http_response_code(400);
       return;
     }

     query("DELETE FROM InputData WHERE id='$key'");
     header('Content-Type: application/json');
     http_response_code(200);

   }

   /*

   */
   function getInputData($key) {


     $query = query("SELECT * FROM InputData WHERE id='$key' LIMIT 1");
     header('Content-Type: application/json');
     http_response_code(200);
     echo $query['data'];
   }


   function getAllInputData($accountId) {
     if (!isset($accountId)) { // If no key is set, return error
       http_response_code(400);
       return;
     }
     $query = queryAll("SELECT * FROM InputData WHERE account_id='$accountId' ORDER BY id DESC"); // Get every input set for account
     header('Content-Type: application/json');
     http_response_code(200);
     echo '[';// Open the brackets
     $count = count($query);
     foreach ($query as $key => $value) {
       echo '{ "id":"' . $value['id'] . '",' .
         '"description": "' . $value['description'] . '",' .
         '"start_date": "' . $value['start_date'] . '",' .
         '"spec":' . $value['spec'] . ',' .
         '"data":' . $value['data'] . '}'; // Print the data, we expect it to be valid JSON.
       if (--$count > 0) {
         echo ','; // For every row except the last, add a comma between rows.
       }
    }
    echo ']'; // Close the brackets
   }




 }


?>
