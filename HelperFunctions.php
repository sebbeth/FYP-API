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



 ?>
