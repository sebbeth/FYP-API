<?php

class ComparisonInterface {

  // public $edible;
  // public $color;

  /* function __construct($edible, $color="green")
   {
       $this->edible = $edible;
       $this->color = $color;
   }*/


   /*

   */
   function createComparison($input) {
     debug($input);
     http_response_code(200);
   }

   /*

   */
   function getResult($key) {

     header('Content-Type: application/json');

     echo 'RESULT';
     http_response_code(200);
   }




 }


?>
