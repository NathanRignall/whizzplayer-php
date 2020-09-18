<?php
  /*!
  * Whizz Player v0.1.0
  * Nathan Rignall
  * 18/09/2020
  */

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }
?>