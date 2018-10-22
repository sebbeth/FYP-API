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

     header('Content-Type: application/json');

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
      // If input does not contain spec or data return an error
      if ( ($input['spec'] == '') || ($input['data'] == '') ) {
        http_response_code(400);
        return;
      }

     if (isset($accountId)) {
       query("INSERT INTO InputData ( account_id, description, start_date, spec, data) VALUES ('$accountId', '$description', '$start_date', '$spec', '$data');");
     } else {
       query("INSERT INTO InputData (data) VALUES ('$data');");
     }

     http_response_code(200);

     $output = [
       'id' => getLatestInsert()
     ];
     echo json_encode($output);

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

     if (sizeof($query) == 0) {
       http_response_code(400);
       return;
     }

     try {
       http_response_code(200);
       echo '{ "id":"' . $query['id'] . '",' .
         '"description": "' . $query['description'] . '",' .
         '"start_date": "' . $query['start_date'] . '",' .
         '"spec":' . $query['spec'] . ',' .
         '"data":' . $query['data'] . '}';
     } catch (Exception $e) {
       http_response_code(500);
     }

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

       $spec = $value['spec'];
       if ($spec == '') {
         $spec = '""';
       }
       $data = $value['data'];
       if ($data == '') {
         $data = '""';
       }
       echo '{ "id":"' . $value['id'] . '",' .
         '"description": "' . $value['description'] . '",' .
         '"start_date": "' . $value['start_date'] . '",' .
         '"spec":' . $spec . ',' .
         '"data":' . $data . '}'; // Print the data, we expect it to be valid JSON.
       if (--$count > 0) {
         echo ','; // For every row except the last, add a comma between rows.
       }
    }
    echo ']'; // Close the brackets
   }




 }


?>
