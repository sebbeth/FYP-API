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




 }


?>
