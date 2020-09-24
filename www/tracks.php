<?php
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 24/09/2020
    */

    $pageurl = htmlspecialchars($_SERVER['PHP_SELF']);
    $urlparts = Explode('/', $pageurl);

    //Include Important
    if (file_exists("config/config.php")) {
        require_once "config/config.php";
    }

    //Initialize the session
    session_start();
    session_write_close();

    //Check if user is already logged in. If so send to index.php  
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        $SongFile = $urlparts[3];
        $filename = $siteconfig['uploadtrack'] . $SongFile;

        if(file_exists($filename)) {
            set_time_limit(0);
            header("Content-Type: audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3"); 
            header("Content-disposition: inline; filename=$SongFile");
            header('Content-length: ' . filesize($filename));
            header('Cache-Control: no-cache');
            header("Content-Transfer-Encoding: binary"); 
            readfile($filename);
            exit(0);
        } else {
            header("HTTP/1.0 404 Not Found");
        }
    } else {
        header("HTTP/1.0 404 Not Found");
    }

?>