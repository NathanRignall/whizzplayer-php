<?php 
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */

    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');

    //Include Important
    $pageurl = htmlspecialchars($_SERVER['PHP_SELF']);
    $urlparts = Explode('/', $pageurl);

    require_once "config/config.php";
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
    } elseif ($pageuri == "backend") {
        $execResult = shell_exec("pgrep -f '/usr/bin/python3 /var/www/whizzplayer/local/main.py'");
        $execResult = trim(preg_replace('/\s+/', ' ', $execResult));
        $execParts = Explode(' ', $execResult);
        if (count($execParts) == 3) {
            echo "data: Backend Process Running\n\n";
        } else {
            echo "data: Backend Not Running\n\n";
        }
    }

    flush();
?>