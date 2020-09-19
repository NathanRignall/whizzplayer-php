<?php 
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */

    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');

    require_once 'lib/vars.php';
    require_once 'lib/sql_connect.php';

    $pageuri = basename(($_SERVER['REQUEST_URI']));
    $pageuri = strtok($pageuri, '?');

    //page vars for include
    if ($pageuri == "playing") {
        //Get SQL data for playing track
        $sqlNowPlaying = "SELECT Playing.TrackID, Playing.HaltTrack, Tracks.TrackDisplayName, Tracks.SongFile FROM Playing,Tracks WHERE Playing.TrackID = Tracks.TrackID";
        $resultNowPlaying = $conn->query($sqlNowPlaying);

        if ($resultNowPlaying->num_rows == 1) {
            while($rowNowPlaying = $resultNowPlaying->fetch_assoc()) {
                echo "data: " . $rowNowPlaying["TrackDisplayName"] . "\n\n";
            }
        } else {
            echo "data: No Track Playing\n\n";
        }
    } elseif ($pageuri == "time") {
        //Get time data
        $time = date('Y/m/d H:i:s');
        echo "data: {$time}\n\n";
    }

    flush();
?>