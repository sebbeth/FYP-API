<?php


/**

API

**/

require_once 'vendor/autoload.php';
require_once 'ComparisonInterface.php';

// get the HTTP method, path and body of the request
$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$input = json_decode(file_get_contents('php://input'),true);

// connect to the mysql database
//$link = mysqli_connect('localhost', 'root', '', 'FYP');
//mysqli_set_charset($link,'utf8');

// retrieve the table and key from the path
$resource = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));
$key = array_shift($request)+0;
/*
// escape the columns and values from the input object
$columns = preg_replace('/[^a-z0-9_]+/i','',array_keys($input));
$values = array_map(function ($value) use ($link) {
  if ($value===null) return null;
  return mysqli_real_escape_string($link,(string)$value);
},array_values($input));
*/
// build the SET part of the SQL command
/*
$set = '';
for ($i=0;$i<count($columns);$i++) {
  $set.=($i>0?',':'').'`'.$columns[$i].'`=';
  $set.=($values[$i]===null?'NULL':'"'.$values[$i].'"');
}
*/


$database = new mysqli("localhost", "root", "","FYP");



if (isset($method)) {

switch ($resource) {

  case ('comparison'):

  $ComparisonInterface = new ComparisonInterface();


  if ( ($method == 'POST') && (isset($input)) ) {
    $ComparisonInterface->createComparison($input);
    return;
  } else {
    http_response_code(400);
  }

  if ( ($method == 'GET') && (isset($key)) ) {
    $ComparisonInterface->getResult($key);
    var_dump(mysqli_fetch_assoc($database->query("SELECT * FROM Results WHERE 1")));
    return;
  } else {
    http_response_code(400);
  }


  break;

  case ('account'):
  break;

  default:
    http_response_code(400);
  break;


}
}

?>
