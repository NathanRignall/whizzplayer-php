<?php
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */

    //Load correct page
    $SetPage = $urlparts[5];

    $UserSites = array(""=>"users/list.php", 
                    "new"=>"users/new.php",
                    "reset"=>"users/reset.php");

    //If no site is found in index user is sent to home page
    if (empty($UserSites[$SetPage])) {
        $UserPage = "users/list.php";
        $SetPage = "main";
    } else {
        $UserPage = $UserSites[$SetPage];
    }
?>

<?php include_once $UserPage ?>