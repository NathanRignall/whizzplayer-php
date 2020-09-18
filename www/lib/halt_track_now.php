<?php 
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */

    $sqlHaltTrack = "UPDATE Playing SET HaltTrack = 1";

    if ($conn->query($sqlHaltTrack) === TRUE) {
        $_SESSION["info-headertitle"] = "Success!";
        $_SESSION["info-bodyinfo"] = "Successfully halted music playback";
        $_SESSION["info-targeturl"] = "home";
        $_SESSION["info-iserror"] = "n";
    } else {
        $_SESSION["info-headertitle"] = "Error!";
        $_SESSION["info-bodyinfo"] = "Error... Couldn't halted playback. " . $sqlHaltTrack . "<br>" . $conn->error;
        $_SESSION["info-targeturl"] = "home";
        $_SESSION["info-iserror"] = "y";
    }
    header("Location: " . $INFOURL);
    ob_end_flush();

?>