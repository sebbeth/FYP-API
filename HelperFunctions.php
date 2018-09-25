<?php



$dbConnection = new mysqli("127.0.0.1", "root", "","FYP");

function query($query) {
  global $dbConnection;
  return mysqli_fetch_assoc($dbConnection->query($query));
}


function queryAll( $query ) {
  global $dbConnection;

    $output = array();
    $result = $dbConnection->query($query);
    for ($i = 0; $i < $result->num_rows; $i++ ) { // What! A for loop in PHP?! ;)
      array_push($output,$result->fetch_assoc());
    }

    return $output;
}

function getLatestInsert() {
  global $dbConnection;
  return mysqli_insert_id($dbConnection);
}

/*



@return int - The ID of the account or null if inauthorized
*/
function authenticate() {
// TODO SAnitise input

list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':' , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));

// TODO input sanitation
if ( isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) ) {

  $account = query("SELECT * FROM Accounts WHERE username='{$_SERVER['PHP_AUTH_USER']}' LIMIT 1");

  if (isset($account) && password_verify($_SERVER['PHP_AUTH_PW'],$account['password_hash']) ) {
    return $account['id'];
  }
}
//
header('HTTP/1.0 401 Unauthorized');
return null;

}


 ?>
