<?php 
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */
    
    //Get TrackID to edit
    $TrackID = $urlparts[4];

    //Get current SQL data for correct TrackID
    $sqlTrackEdit = "SELECT * FROM Tracks WHERE TrackID = $TrackID";
    $resultTrackEdit = $conn->query($sqlTrackEdit);

    //Save SQL data to correct vars
    if ($resultTrackEdit->num_rows > 0) {
        while($rowTrackEdit = $resultTrackEdit->fetch_assoc()) {
            $TrackDisplayName = $rowTrackEdit["TrackDisplayName"];
            $SongFileName = $rowTrackEdit["SongFile"];
        }
    } else {
        $_SESSION["info-headertitle"] = "Error!";
        $_SESSION["info-bodyinfo"] = "Error... the track you are trying to edit doesn't exist!";
        $_SESSION["info-targeturl"] = "tracks";
        $_SESSION["info-iserror"] = "y";
        header("Location: " . $INFOURL);
        ob_end_flush();
    }

    // Checks input for sql injection attacks
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $data = str_replace( "'","",$data );
        return $data;
    }

    //Server side for editing track
    //If the apply button is pressed
    if(isset($_POST['apply'])){
        $TrackDisplayNameSet = test_input($_POST['trackdisplayname']);
        $sql = "UPDATE Tracks SET TrackDisplayName = '" . $TrackDisplayNameSet ."' WHERE TrackID = $TrackID";
        if ($conn->query($sql) === TRUE) {
            $_SESSION["info-headertitle"] = "Success!";
            $_SESSION["info-bodyinfo"] = "Track edited successfully";
            $_SESSION["info-targeturl"] = "tracks";
            $_SESSION["info-iserror"] = "n";
        } else {
            $_SESSION["info-headertitle"] = "Error!";
            $_SESSION["info-bodyinfo"] = "Error... Could not edit track. Internal SQL Error. : " . $conn->error;
            $_SESSION["info-targeturl"] = "tracks";
            $_SESSION["info-iserror"] = "y";
        }
        header("Location: " . $INFOURL);
        ob_end_flush();
    }

    //Server side for deleteing tracks
    //If the delete cue button is pressed
    if(isset($_POST['delete'])){
        $sqlCheckCues = "SELECT Cues.TrackID FROM Cues WHERE TrackID=$TrackID";
        $resultCheckCues = $conn->query($sqlCheckCues);
        if ($resultCheckCues->num_rows > 0) {
            $_SESSION["info-headertitle"] = "Error!";
            $_SESSION["info-bodyinfo"] = "Error... Could not delete track. Track is used in exisitng Cue(s), you need to delete the Cue(s) first!";
            $_SESSION["info-targeturl"] = "cues";
            $_SESSION["info-iserror"] = "y";
        } else {
            $sqlDelFile = "SELECT Tracks.SongFile FROM Tracks WHERE TrackID=$TrackID";
            $resultDelFile = $conn->query($sqlDelFile);
            while($rowDelFile = $resultDelFile->fetch_assoc()) {
                $delfile =  $siteconfig['uploadtrack'] . $rowDelFile["SongFile"];
            }
            unlink($delfile);
            $sql = "DELETE FROM Tracks WHERE TrackID=$TrackID";
            if ($conn->query($sql) === TRUE) {
                $_SESSION["info-headertitle"] = "Success!";
                $_SESSION["info-bodyinfo"] = "Track deleted successfully";
                $_SESSION["info-targeturl"] = "tracks";
                $_SESSION["info-iserror"] = "n";
            } else {
                $_SESSION["info-headertitle"] = "Error!";
                $_SESSION["info-bodyinfo"] = "Error... Could not delete track. Internal SQL Error. : " . $conn->error;
                $_SESSION["info-targeturl"] = "tracks";
                $_SESSION["info-iserror"] = "y";
            }
        }
        header("Location: " . $INFOURL);
        ob_end_flush();
    }
?>

<div class="card">
    <!-- Edit Track Card Header -->
    <div class="card-header bg-primary text-white">
        <h4 class="card-title">Edit Track: <?php echo $TrackDisplayName; ?></h4>
    </div>
    <!-- Edit Track Form -->
    <form action="" method="post" class="needs-validation" novalidate>
        <!-- Edit Track Card Body -->
        <div class="modal-body">
            <!-- Edit Track Display Name -->
            <div class="form-group">
                <label for="trackdisplayname">Track Display Name:</label>
                <input type="text" class="form-control" id="trackdisplayname" value="<?php echo $TrackDisplayName; ?>" name="trackdisplayname" required>
                <div class="valid-feedback">Valid display name.</div>
                <div class="invalid-feedback">Please fill out this field.</div>
            </div>
            <!-- Track Item Audio Playback -->
            <div class="text-center">
                <audio controls>
                    <source src="<?php echo $siteconfig['baseurl'] . "tracks.php/" . $SongFileName ?>" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </div>
            <br/>
        </div>
        <!-- Edit Track Card Footer-->
        <div class="modal-footer">
            <input type="submit" class="btn btn-success" value="Apply Changes" name="apply">
            <form method="post">
                <input type="submit" class="btn btn-danger" value="Delete Track" name="delete">
            </form>
            <a href="<?php echo $siteconfig['baseurl'];?>index.php/tracks" class="btn btn-secondary">Return</a>
        </div>
    </form>
</div>