<?php 
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */

    //Get SQL data for all tracks
    $sqlTrackList = "SELECT Tracks.TrackID, Tracks.TrackDisplayName, Tracks.SongFile FROM Tracks";
    $resultTrackList = $conn->query($sqlTrackList);

    //Server side for deleteing tracks
    //If the delete cue button is pressed
    if(isset($_POST['submit'])){
        if(!empty($_POST['delete'])) {
            foreach($_POST['delete'] as $value){
                $sqlCheckCues = "SELECT Cues.TrackID FROM Cues WHERE TrackID=$value";
                $resultCheckCues = $conn->query($sqlCheckCues);
                if ($resultCheckCues->num_rows > 0) {
                    $_SESSION["info-headertitle"] = "Error!";
                    $_SESSION["info-bodyinfo"] = "Error... Could not delete track. Track is used in exisitng Cue, you need to delete the Cue first!";
                    $_SESSION["info-targeturl"] = "cues";
                    $_SESSION["info-iserror"] = "y";
                } else {
                    $sqlDelFile = "SELECT Tracks.SongFile FROM Tracks WHERE TrackID=$value";
                    $resultDelFile = $conn->query($sqlDelFile);
                    while($rowDelFile = $resultDelFile->fetch_assoc()) {
                        $delfile =  $siteconfig['uploadtrack'] . $rowDelFile["SongFile"];
                    }
                    unlink($delfile);
                    $sql = "DELETE FROM Tracks WHERE TrackID=$value";
                    if ($conn->query($sql) === TRUE) {
                        $_SESSION["info-headertitle"] = "Success!";
                        $_SESSION["info-bodyinfo"] = "Track deleted successfully";
                        $_SESSION["info-targeturl"] = "tracks";
                        $_SESSION["info-iserror"] = "n";
                    } else {
                        $_SESSION["info-headertitle"] = "Error!";
                        $_SESSION["info-bodyinfo"] = "Error... Could not delete track. Internal Error. : " . $conn->error;
                        $_SESSION["info-targeturl"] = "tracks";
                        $_SESSION["info-iserror"] = "y";
                    }
                }
            }
            header("Location: " . $INFOURL);
            ob_end_flush();
        }
    }

    //Upload modal pre expand if get url is correct
    $uploadinstant = $_GET["uploadinstant"];
    if ($uploadinstant == "Y") {
        echo "<script type='text/javascript'>$(document).ready(function(){ $('#uploadTrack').modal('show'); });</script>"; 
    }
?>

<h1>Instant Play
<span class="badge badge-warning">Beta</span>
</h1>

<br>

<?php
//Track list main php loop
if ($resultTrackList->num_rows > 0) {
    echo '<div class="row row-cols-1 row-cols-md-2">';
    while($rowTrackList = $resultTrackList->fetch_assoc()) { ?> 
        <div class="col mb-4">
            <div class="card">
                <!-- Track Item Card Header-->
                <div class="card-header bg-secondary text-white">
                    <h4 class="card-title"><?php echo $rowTrackList["TrackDisplayName"]; ?> <a class="btn btn-primary" href="">Play</a></h4>
                </div>
            </div>
        </div>
    <?php }
    echo "</div>";
} else {
    echo "<div class='alert alert-warning'><strong>Warning!</strong> There are currently 0 Tracks uploaded, upload a track first!</div>";
}
?>