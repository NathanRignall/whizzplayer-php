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

<h1>Track List</h1>

<!-- Track list form (All code to show all tracks) -->
<form method="post" action="">
    <!-- Track List Upload Delete Buttons -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadTrack">Upload a track</button>
    <input type="submit" value="Delete Selected Tracks" class="btn btn-danger" name="submit">
    <br><br>
    <?php
    //Track list main php loop
    if ($resultTrackList->num_rows > 0) {
        echo '<div class="row row-cols-1 row-cols-md-2">';
        while($rowTrackList = $resultTrackList->fetch_assoc()) { ?> 
            <div class="col mb-4">
                <div class="card">
                    <!-- Track Item Card Header-->
                    <div class="card-header bg-secondary text-white">
                        <h4 class="card-title"><?php echo $rowTrackList["TrackDisplayName"]; ?></h4>
                    </div>
                    <!-- Track Item Card Body -->
                    <div class="card-body">
                        <!-- Track Item Audio Playback -->
                        <div class="text-center">
                            <audio controls>
                                <source src="<?php echo $siteconfig['trackurl'] . $rowTrackList["SongFile"]; ?>" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                        <!-- Track Item Flex box items -->
                        <div class="d-flex justify-content-between pt-2">
                            <!-- Track Item Delete Track Checkbox -->
                            <div class="p-2">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" name='delete[]' value="<?php echo $rowTrackList["TrackID"]; ?>">Delete Track
                                    </label>
                                </div>
                            </div>
                            <!-- Track Item Edit Button -->
                            <div class="p-2">
                                <a class="btn btn-primary" href="track-edit/<?php echo $rowTrackList["TrackID"]; ?>">Edit</a>
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        <?php }
        echo "</div>";
    } else {
        echo "<div class='alert alert-warning'><strong>Warning!</strong> There are currently 0 Tracks uploaded, upload a track first!</div>";
    }
    ?>
    
    <!-- Track List Upload Delete Buttons -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#uploadTrack">Upload a track</button>
    <input type="submit" value="Delete Selected Tracks" class="btn btn-danger" name="submit">
</form>

<!-- Upload Track Modal -->
<div class="modal fade" id="uploadTrack">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <!-- Upload Track Modal Header -->
        <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Upload a Track</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <!-- Upload Track Modal Form -->
        <form action="<?php echo $siteconfig['baseurl'];?>index.php/upload" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            <!-- Upload Track Modal Body -->
            <div class="modal-body">
                <!-- Upload Track Modal Display Name -->
                <div class="form-group">
                    <label for="trackdisplayname">Track Display Name:</label>
                    <input type="text" class="form-control" id="trackdisplayname" placeholder="Cue Display Name" name="trackdisplayname" required>
                    <div class="valid-feedback">Valid display name.</div>
                    <div class="invalid-feedback">Please fill out this field.</div>
                </div>
                <div class="form-group">
                    <!-- <label for="fileToUpload">Upload Song File</label> -->
                    <input type="file" id="fileToUpload" name="fileToUpload" required>
                    <div class="valid-feedback">Valid Upload.</div>
                    <div class="invalid-feedback">Please upload a song.</div>
                </div>
                <br/>
            </div>
            <!-- Upload Track Modal Footer-->
            <div class="modal-footer">
                <input type="submit" class="btn btn-success" value="Upload Track" name="uploadtrack">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </form>
        </div>
    </div>
</div>

<script>
    // Disable form submissions if there are invalid fields
    (function() {
    'use strict';
    window.addEventListener('load', function() {
        // Get the forms we want to add validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
        });
    }, false);
    })();
</script>