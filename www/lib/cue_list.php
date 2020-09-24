<?php
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */

    //Get SQL data for all cues
    $sqlClueList = "SELECT Cues.CueID, Cues.CueDisplayName, Cues.PlayTime, Cues.PlayDate, Cues.Repeats, Cues.RepeatMon, 
    Cues.RepeatTue, Cues.RepeatWed, Cues.RepeatThu, Cues.RepeatFri, Cues.RepeatSat, Cues.RepeatSun, Cues.Enabled, 
    Tracks.TrackID, Tracks.TrackDisplayName, Tracks.SongFile FROM Cues, Tracks WHERE Cues.TrackID = Tracks.TrackID";
    $resultClueList = $conn->query($sqlClueList);
?>

<?php
    // Simple functions for page load
    function isenableddayscol($data) {
        if ($data == 1) {
            return "list-group-item-primary";
        } else {
            return "list-group-item-secondary";
        }
    }
    // Checks input for sql injection attacks
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        $data = str_replace( "'","",$data );
        return $data;
    }

    //Server side for deleteing cues
    //If the delete cue button is pressed
    if(isset($_POST['deletecue'])){
        if(!empty($_POST['deletecuearry'])) {
            foreach($_POST['deletecuearry'] as $value){
                $sqlDeleteCue = "DELETE FROM Cues WHERE CueID=$value";
                if ($conn->query($sqlDeleteCue) === TRUE) {
                    $_SESSION["info-headertitle"] = "Success!";
                    $_SESSION["info-bodyinfo"] = "Cue deleted successfully";
                    $_SESSION["info-targeturl"] = "cues";
                    $_SESSION["info-iserror"] = "n";
                } else {
                    $_SESSION["info-headertitle"] = "Error!";
                    $_SESSION["info-bodyinfo"] = "Error... Could not delete cue. Internal Error. : " . $conn->error;
                    $_SESSION["info-targeturl"] = "cues";
                    $_SESSION["info-iserror"] = "y";
                }
            }
            header("Location: " . $INFOURL);
            ob_end_flush();
        }
    }

    //Server side for creating a new cue
    // IF the create cue button is pressed
    if(isset($_POST['createcue'])){
        // Set vars equal to post data from form with injection checking
        $CueDisplayNameSet = test_input($_POST['cuedisplayname']);
        $PlayTimeSet = test_input($_POST['playtime']);
        $PlayDateSet = test_input($_POST["playdate"]);
        // Set boolean vars to 0 to remove errors
        $RepeatsSet = 0;
        $RepeatMonSet = 0;
        $RepeatTueSet = 0;
        $RepeatWedSet = 0;
        $RepeatThuSet = 0;
        $RepeatFriSet = 0;
        $RepeatSatSet = 0;
        $RepeatSunSet = 0;
        $EnabledSet = 0;

        // Set selected track var
        foreach ($_POST['trackselect'] as $selectedTrack) {
            $TrackIDSet = $selectedTrack;
        }

        // Set vars to 1 if checkboxes are checked and more if repeats is enabled
        if (isset($_POST['enabled'])) { $EnabledSet = 1; }
        if (isset($_POST['repeats'])) { $RepeatsSet = 1; }
        // Set vars to 1 for repeat days, uses array to store form data
        foreach ($_POST['repeatdays'] as $selectedRepeatDays) {
            if ($selectedRepeatDays == 1) {
                $RepeatMonSet = 1;
            } elseif ($selectedRepeatDays == 2) {
                $RepeatTueSet = 1;
            } elseif ($selectedRepeatDays == 3) {
                $RepeatWedSet = 1;
            } elseif ($selectedRepeatDays == 4) {
                $RepeatThuSet = 1;
            } elseif ($selectedRepeatDays == 5) {
                $RepeatFriSet = 1;
            } elseif ($selectedRepeatDays == 6) {
                $RepeatSatSet = 1;
            } elseif ($selectedRepeatDays == 7) {
                $RepeatSunSet = 1;
            }
        }

        // SQL Create Cue Command - Used create new cue
        $sqlCreateCue = "INSERT INTO Cues (CueDisplayName,TrackID,PlayTime,PlayDate,Repeats,RepeatMon,RepeatTue,RepeatWed,RepeatThu,RepeatFri,RepeatSat,RepeatSun,Enabled)
                        VALUES ('" . $CueDisplayNameSet . "', " . $TrackIDSet  . ", '" . $PlayTimeSet  . "', '" . $PlayDateSet  . "', " . $RepeatsSet  . ", 
                        " . $RepeatMonSet  . ", " . $RepeatTueSet  . ", " . $RepeatWedSet  . ", " . $RepeatThuSet  . ", " . $RepeatFriSet  . ", " . $RepeatSatSet  . ", " . $RepeatSunSet . ", " . $EnabledSet . ")";
        if ($conn->query($sqlCreateCue) === TRUE) {
            $_SESSION["info-headertitle"] = "Success!";
            $_SESSION["info-bodyinfo"] = "Created cue successfully";
            $_SESSION["info-targeturl"] = "cues";
            $_SESSION["info-iserror"] = "n";
        } else {
            $_SESSION["info-headertitle"] = "Error!";
            $_SESSION["info-bodyinfo"] = "Error... Couldn't create cue. " . $sqlCreateCue . "<br>" . $conn->error;
            $_SESSION["info-targeturl"] = "cues";
            $_SESSION["info-iserror"] = "y";
        }
        header("Location: " . $INFOURL);
        ob_end_flush();
    }

    //Upload modal pre expand if get url is correct
    $uploadinstant = $_GET["createinstant"];
    if ($uploadinstant == "Y") {
        echo "<script type='text/javascript'>$(document).ready(function(){ $('#cueCreate').modal('show'); });</script>"; 
    }
?>

<h1>Cue List</h1>

<!-- Cue list form (All code to show all cues) -->
<form method="post" action="">
    <!-- Cue List Create Delete Buttons -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#cueCreate">Create A Cue</button>
    <input type="submit" value="Delete Selected Cues" class="btn btn-danger" name="deletecue">
    <br><br>
    <?php
    //Cue list main php loop
    if ($resultClueList->num_rows > 0) {
        while($rowClueList = $resultClueList->fetch_assoc()) { ?>
            <!-- Cue Item Card -->
            <div class="card">
                <!-- Cue Item Card Header-->
                <div class="card-header text-white <?php echo ($rowClueList["Enabled"]==0) ? "bg-secondary" :"bg-success"; ?>">
                    <h4 class="card-title"><?php echo $rowClueList["CueDisplayName"]; ?></h4>
                    <span class="badge badge-light"><?php echo ($rowClueList["Enabled"]==0) ? "Disabled" :"Enabled"; ?></span>
                </div>
                <!-- Cue Item Card Body -->
                <div class="card-body">
                    <!-- Cue Item Card Deck 1 -->
                    <div class="card-deck">
                        <!-- Cue Item Audio Track -->
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <p class="card-text">
                                    <b>Audio Track</b>
                                    <br>
                                    <?php echo $rowClueList["TrackDisplayName"]; ?>
                                </p>
                            </div>
                        </div>
                        <!-- Cue Item Play Time -->
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <p class="card-text">
                                    <b>Time</b>
                                    <br>
                                    <?php echo $rowClueList["PlayTime"]; ?>
                                </p>
                            </div>
                        </div>
                        <!-- Cue Item Start Date -->
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <p class="card-text">
                                    <b><?php echo ($rowClueList["Repeats"]==0) ? "Play Date" :"Start Date"; ?></b>
                                    <br>
                                    <?php echo $rowClueList["PlayDate"]; ?>
                                </p>
                            </div>
                        </div>
                        <!-- Cue Item Repeats -->
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <p class="card-text">
                                    <b>Repeats</b>
                                    <br>
                                    <?php echo ($rowClueList["Repeats"]==0) ? "Disabled" :"Enabled"; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Cue Item List Group If Repeats enabled -->
                    <?php if ($rowClueList["Repeats"] == 1) {
                        echo "<br><ul class='list-group list-group-horizontal'>";
                        echo "<li class='list-group-item " . isenableddayscol($rowClueList["RepeatMon"]) . "'>Monday</li>";
                        echo "<li class='list-group-item " . isenableddayscol($rowClueList["RepeatTue"]) . "'>Tuesday</li>";
                        echo "<li class='list-group-item " . isenableddayscol($rowClueList["RepeatWed"]) . "'>Wednesday</li>";
                        echo "<li class='list-group-item " . isenableddayscol($rowClueList["RepeatThu"]) . "'>Thursday</li>";
                        echo "<li class='list-group-item " . isenableddayscol($rowClueList["RepeatFri"]) . "'>Friday</li>";
                        echo "<li class='list-group-item " . isenableddayscol($rowClueList["RepeatSat"]) . "'>Saturday</li>";
                        echo "<li class='list-group-item " . isenableddayscol($rowClueList["RepeatSun"]) . "'>Sunday</li>";
                        echo "</ul>";
                    } ?>
                    <br>
                    <!-- Cue Item Audio Playback -->
                    <div class="text-center">
                        <audio controls>
                            <source src="<?php echo $siteconfig['baseurl'] . "tracks.php/" . $rowClueList["SongFile"]; ?>" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </div>
                    <!-- Cue Item Flex box items -->
                    <div class="d-flex justify-content-between">
                        <!-- Cue Item Delete Cue Checkbox -->
                        <div class="p-2">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input deletecueTest" name='deletecuearry[]' value="<?php echo $rowClueList["CueID"]; ?>">Delete Cue
                                </label>
                            </div>
                        </div>
                        <!-- Cue Item Edit Button -->
                        <div class="p-2">
                            <a href="cue-edit/<?php echo $rowClueList["CueID"]; ?>" class="btn btn-primary">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
            </br>
        <?php }
    } else {
        //Get SQL data for all tracks
        $sqlTrackList = "SELECT Tracks.TrackID FROM Tracks";
        $resultTrackList = $conn->query($sqlTrackList);
        if ($resultTrackList->num_rows == 0) {
            echo "<div class='alert alert-danger'><strong>Error!</strong> You cannot create a cue without uploading a track first!</div>";
            echo '<a href="tracks?uploadinstant=Y" class="btn btn-warning btn-lg mt-1" role="button">Upload Track</a><br><br>';
        } else {
            echo "<div class='alert alert-warning'><strong>Warning!</strong> There are currently 0 Cues, you need to create a cue!</div>";   
        }
    }
    ?>
    <!-- Cue List Create Delete Buttons -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#cueCreate">Create A Cue</button>
    <input type="submit" value="Delete Selected Cues" class="btn btn-danger" name="deletecue">
</form>

<!-- Cue Create Modal -->
<div class="modal fade" id="cueCreate">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <!-- Cue Create Modal Header -->
        <div class="modal-header bg-primary text-white">
            <h4 class="modal-title">Create a Cue</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
            <!-- Cue Create Modal Form -->
        <form method="post" action="" class="needs-validation" novalidate>
            <!-- Cue Create Modal Body -->
            <div class="modal-body">
                <!-- Cue Create Modal Display Name -->
                <div class="form-group">
                    <label for="cuedisplayname">Cue Display Name:</label>
                    <input type="text" class="form-control" id="cuedisplayname" placeholder="Cue Display Name" name="cuedisplayname" required>
                    <div class="valid-feedback">Valid display name.</div>
                    <div class="invalid-feedback">Please fill out this field.</div>
                </div>
                <!-- Cue Create Modal Track Select -->
                <div class="form-group">
                    <label for="trackselect">Song:</label>
                    <select name="trackselect[]" class="form-control" id="trackselect" required>
                        <?php 
                            $sqlTrackInfo = "SELECT Tracks.TrackID, Tracks.TrackDisplayName  FROM Tracks";
                            $resultTrackInfo = $conn->query($sqlTrackInfo);
                            if ($resultTrackInfo->num_rows > 0) {
                                while($rowTrackInfo = $resultTrackInfo->fetch_assoc()) {
                                    echo "<option value='" . $rowTrackInfo["TrackID"] . "'>" . $rowTrackInfo["TrackDisplayName"] . "</option>";
                                }
                            } else {
                                echo "Error... No Tracks";
                            }
                        ?>
                    </select>
                    <div class="valid-feedback">Valid Track.</div>
                    <div class="invalid-feedback">Please fill out this field.</div>
                    <a href="tracks?uploadinstant=Y" class="btn btn-info mt-1" role="button">Upload Track</a>
                </div>
                <!-- Cue Create Modal Time Date -->
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="playtime">Cue Play Time:</label>
                            <input type="time" class="form-control" id="playtime" name="playtime" required>
                            <div class="valid-feedback">Valid Time.</div>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="playdate">Cue Start Date:</label>
                            <input type="date" class="form-control" id="playdate" name="playdate" required>
                            <div class="valid-feedback">Valid Date.</div>
                            <div class="invalid-feedback">Please fill out this field.</div>
                        </div>
                    </div>
                </div>
                <!-- Cue Create Modal Repeat Settings -->
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="repeatCheck" name="repeats">
                    <label class="custom-control-label" for="repeatCheck">Repeats Enabled</label>
                </div>
                <div class="form-group">
                    <select name="repeatdays[]" id="repeatOptions" class="custom-select" multiple disabled required>
                        <option value=1>RepeatMon</option>
                        <option value=2>RepeatTue</option>
                        <option value=3>RepeatWed</option>
                        <option value=4>RepeatThu</option>
                        <option value=5>RepeatFri</option>
                        <option value=6>RepeatSat</option>
                        <option value=7>RepeatSun</option>
                    </select>
                </div>
                <!-- Cue Create Modal Enabled -->
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="enabled" name="enabled" checked>
                    <label class="custom-control-label" for="enabled">Cue Enabled</label>
                </div>
                <br/>
            </div>
            <!-- Cue Create Modal Footer-->
            <div class="modal-footer">
                <input type="submit" class="btn btn-success" value="Create" name="createcue">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </form>
        </div>
    </div>
</div>

<script>
    // Event listener for repeat checkbox 
    const checkRepeat = document.getElementById('repeatCheck')
    checkRepeat.addEventListener('change', (event) => {
    if (event.target.checked) {
        document.getElementById("repeatOptions").disabled = false;
    } else {
        document.getElementById("repeatOptions").disabled = true;
    }
    })
</script>

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