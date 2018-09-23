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

function authenticate() {
// TODO SAnitise input
  $user = $_SERVER['PHP_AUTH_USER'];
  $account = query("SELECT * FROM Accounts WHERE username='$user' LIMIT 1"); // Get the account from debug
  
  if (isset($_SERVER['PHP_AUTH_USER'])  &&  isset($account) && password_verify($_SERVER['PHP_AUTH_PW'],$account['password_hash'])) {
    return true;

  } else {
      header('WWW-Authenticate: Basic realm="SBROWN"');
      header('HTTP/1.0 401 Unauthorized');
      return false;

  }
}


 ?>
