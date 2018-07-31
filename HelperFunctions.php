<?php



$dbConnection = new mysqli("127.0.0.1", "root", "","FYP");

function query($query) {
  global $dbConnection;
  return mysqli_fetch_assoc($dbConnection->query($query));
}

function getLatestInsert($query) {
  global $dbConnection;
  return mysqli_insert_id($dbConnection);
}


 ?>
