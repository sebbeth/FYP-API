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

     if (!isset($input['input'])) { // If no input is set, return error
       http_response_code(400);
       return;
     }
     $inputData = $input['input'];

     // Now break input data appart and store it


    $description = '';
    $start_date = '';
    $spec = '';
    $data = '';

    if (array_key_exists('description',$input['input'])) {
        $description = $input['input']['description'];
      }
    if (array_key_exists('start_date',$input['input'])) {
        $start_date = $input['input']['start_date'];
      }
      if (array_key_exists('data',$input['input'])) {
        $data = json_encode($input['input']['data']);
      }
      if (array_key_exists('spec',$input['input'])) {
        $spec = json_encode($input['input']['spec']);
      }

     if (isset($input['account'])) {
       query("INSERT INTO InputData ( account_id, description, start_date, spec, data) VALUES ('{$input['account']}', '$description', '$start_date', '$spec', '$data');");
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
       echo '{ "id":"' . $value['id'] . '","data":' . $value['data'] . '}'; // Print the data, we expect it to be valid JSON.
       if (--$count > 0) {
         echo ','; // For every row except the last, add a comma between rows.
       }
    }
    echo ']'; // Close the brackets
   }




 }


?>
