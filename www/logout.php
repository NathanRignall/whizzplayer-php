<?php
    /*!
    * Whizz Player v0.1.0
    * Nathan Rignall
    * 18/09/2020
    */

    // Initialize the session
    session_start();
    
    // Unset all of the session variables
    $_SESSION = array();
    
    // Destroy the session.
    session_destroy();
    
    // Redirect to login page
    header("location: " . $BASEURL . "login.php");
    exit;
?>