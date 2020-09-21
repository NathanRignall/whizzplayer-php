<?php
  /*!
  * Whizz Player 0.1.0-alpha.1
  * Nathan Rignall
  * 18/09/2020
  */

  $servername = $siteconfig['server'];
  $dbname = $siteconfig['dbname'];
  $username = $siteconfig['dbuser'];
  $password = $siteconfig['dbpass'];

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check connection
  if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }
?>