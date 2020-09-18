<?php 
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */

    //Get SQL data for playing track
    $sqlNowPlaying = "SELECT Playing.TrackID, Playing.HaltTrack, Tracks.TrackDisplayName, Tracks.SongFile FROM Playing,Tracks WHERE Playing.TrackID = Tracks.TrackID";
    $resultNowPlaying = $conn->query($sqlNowPlaying);

    if ($resultNowPlaying->num_rows > 0) {
        while($rowNowPlaying = $resultNowPlaying->fetch_assoc()) {
            echo "data:" . $rowNowPlaying["TrackDisplayName"] . "\n\n";
        }
    } else {
        echo "data: No Track Playing\n\n";
    }
?>
