<?php
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */

    //Include Important
    require_once 'config/config.php';

    //Initialize the session
    session_start();
    
    //Unset all of the session variables
    $_SESSION = array();
    
    //Destroy the session.
    session_destroy();
    
    //Redirect to login page
    header("location: " . $siteconfig['baseurl'] . "login.php");
    exit;
?>