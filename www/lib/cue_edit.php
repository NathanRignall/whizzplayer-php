<?php 
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */

    //Get CueID to edit
    $CueID = $urlparts[4];

    //Get current SQL data for correct CueID
    $sqlCueEdit = "SELECT * FROM Cues WHERE CueID = $CueID";
    $resultCueEdit  = $conn->query($sqlCueEdit);

    //Save SQL data to correct vars
    if ($resultCueEdit->num_rows > 0) {
        while($rowCueEdit = $resultCueEdit->fetch_assoc()) {
            $CueDisplayName = $rowCueEdit["CueDisplayName"];
            $TrackID = $rowCueEdit["TrackID"];
            $PlayTime = $rowCueEdit["PlayTime"];
            $PlayDate = $rowCueEdit["PlayDate"];
            $Repeats = $rowCueEdit["Repeats"];
            $RepeatMon = $rowCueEdit["RepeatMon"];
            $RepeatTue = $rowCueEdit["RepeatTue"];
            $RepeatWed = $rowCueEdit["RepeatWed"];
            $RepeatThu = $rowCueEdit["RepeatThu"];
            $RepeatFri = $rowCueEdit["RepeatFri"];
            $RepeatSat = $rowCueEdit["RepeatSat"];
            $RepeatSun = $rowCueEdit["RepeatSun"];
            $Enabled = $rowCueEdit["Enabled"];
        }
    } else {
        $_SESSION["info-headertitle"] = "Error!";
        $_SESSION["info-bodyinfo"] = "Error... the cue you are trying to edit doesn't exist!";
        $_SESSION["info-targeturl"] = "cues";
        $_SESSION["info-iserror"] = "y";
        header("Location: " . $INFOURL);
        ob_end_flush();
    }

    // Simple functions for page load
    function isenabled($data) {
        if ($data == 1) {
            echo "checked";
        }
    }
    function isselected($data) {
        if ($data == 1) {
            echo "selected";
        }
    }
    function isdisabled($data) {
        if ($data == 0) {
            echo "disabled";
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

    //Server side for editing cues
    // IF the apply button is pressed
    if(isset($_POST['apply'])){
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
        if (isset($_POST['repeats'])) { 
            $RepeatsSet = 1; 
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
            // SQL Update Command 2 - Used to update repeat days data
            $sqlUpdateDetails2 = "UPDATE Cues SET RepeatMon = '" . $RepeatMonSet . "', RepeatTue = '" . $RepeatTueSet . "', RepeatWed = '" . $RepeatWedSet . "', RepeatThu = '" . $RepeatThuSet . "', RepeatFri = '" . $RepeatFriSet . "', RepeatSat = '" . $RepeatSatSet . "', RepeatSun = '" . $RepeatSunSet . "' WHERE CueID = $CueID";
            if ($conn->query($sqlUpdateDetails2) === TRUE) {
                echo "<br> Update2 saved successfully (Repeat info)";
            } else {
                echo "<br> Error... Couldn't update info. " . $sqlUpdateDetails2 . "<br>" . $conn->error;
            }
        }

        // SQL Update Command 1 - Used to update nessiary data
        $sqlUpdateDetails1 = "UPDATE Cues SET CueDisplayName = '" . $CueDisplayNameSet . "', TrackID = " . $TrackIDSet . ", PlayTime = '" . $PlayTimeSet . "', PlayDate = '" . $PlayDateSet . "', Repeats = " . $RepeatsSet . ", Enabled = " . $EnabledSet . " WHERE CueID = $CueID";
        if ($conn->query($sqlUpdateDetails1) === TRUE) {
            $_SESSION["info-headertitle"] = "Success!";
            $_SESSION["info-bodyinfo"] = "Cue updated successfully";
            $_SESSION["info-targeturl"] = "cues";
            $_SESSION["info-iserror"] = "n";
        } else {
            $_SESSION["info-headertitle"] = "Error!";
            $_SESSION["info-bodyinfo"] = "Error... Could not update cue. Internal SQL Error. : " . $conn->error;
            $_SESSION["info-targeturl"] = "cues";
            $_SESSION["info-iserror"] = "y";
        }
        header("Location: " . $INFOURL);
        ob_end_flush();
    }

    //Server side for deleteing cues
    // SQL Delete cue Command - Used to delete cue
    if(isset($_POST['delete'])){
        $sqlDelCue = "DELETE FROM Cues WHERE CueID=$CueID";
        if ($conn->query($sqlDelCue) === TRUE) {
            $_SESSION["info-headertitle"] = "Success!";
            $_SESSION["info-bodyinfo"] = "Cue deleted successfully";
            $_SESSION["info-targeturl"] = "cues";
            $_SESSION["info-iserror"] = "n";
        } else {
            $_SESSION["info-headertitle"] = "Error!";
            $_SESSION["info-bodyinfo"] = "Error... Could not delete cue. Internal SQL Error. : " . $conn->error;
            $_SESSION["info-targeturl"] = "cues";
            $_SESSION["info-iserror"] = "y";
        }
        header("Location: " . $INFOURL);
        ob_end_flush();
    }
?>

<!-- Cue edit Card (All code to edit selected cue) -->
<div class="card">
    <!-- Cue Edit Card Header -->
    <div class="card-header bg-primary text-white">
        <h4 class="card-title">Edit Cue: <?php echo $CueDisplayName; ?></h4>
    </div>
    <!-- Cue Edit Card Form -->
    <form method="post" id="editcueform" action="" class="needs-validation" novalidate>
        <!-- Cue Edit Card Body -->
        <div class="card-body">
            <!-- Cue Edit Card Display Name -->
            <div class="form-group">
                <label for="cuedisplayname">Cue Display Name:</label>
                <input type="text" class="form-control" id="cuedisplayname" placeholder="Cue Display Name" value="<?php echo $CueDisplayName; ?>" name="cuedisplayname" required>
                <div class="valid-feedback">Valid display name.</div>
                <div class="invalid-feedback">Please fill out this field.</div>
            </div>
            <!-- Cue Edit Card Track Select -->
            <div class="form-group">
                <label for="trackselect">Song:</label>
                <select name="trackselect[]" class="form-control" id="trackselect" required>
                    <?php 
                        $sqlTrackInfo = "SELECT * FROM Tracks";
                        $resultTrackInfo = $conn->query($sqlTrackInfo);
                        if ($resultTrackInfo->num_rows > 0) {
                            while($rowTrackInfo = $resultTrackInfo->fetch_assoc()) {
                                if ($rowTrackInfo["TrackID"] == $TrackID){
                                    echo "<option value='" . $rowTrackInfo["TrackID"] . "'selected>" . $rowTrackInfo["TrackDisplayName"] . "</option>";
                                } else {
                                    echo "<option value='" . $rowTrackInfo["TrackID"] . "'>" . $rowTrackInfo["TrackDisplayName"] . "</option>";
                                }
                            }
                        } else {
                            echo "Error... No Tracks";
                        }
                    ?>
                    <div class="valid-feedback">Valid Song.</div>
                    <div class="invalid-feedback">Please fill out this field.</div>
                </select>
                <a href="tracks?uploadinstant=Y" class="btn btn-info mt-1" role="button">Upload Track</a>
            </div>
            <!-- Cue Create Modal Time Date -->
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="playtime">Cue Play Time:</label>
                        <input type="time" class="form-control" id="playtime" name="playtime" value="<?php echo $PlayTime; ?>" required>
                        <div class="valid-feedback">Valid Time.</div>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="playdate">Cue Start Date:</label>
                        <input type="date" class="form-control" id="playdate" name="playdate" value="<?php echo $PlayDate; ?>" required>
                        <div class="valid-feedback">Valid Date.</div>
                        <div class="invalid-feedback">Please fill out this field.</div>
                    </div>
                </div>
            </div>
            <!-- Cue Edit Card Repeat Settings -->
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="repeatCheck" name="repeats" <?php isenabled($Repeats); ?>>
                <label class="custom-control-label" for="repeatCheck">Repeats Enabled</label>
            </div>
            <div class="form-group">
                <select name="repeatdays[]" id="repeatOptions" class="custom-select" multiple <?php isdisabled($Repeats); ?> required>
                    <option value=1 <?php isselected($RepeatMon); ?>>Repeat Monday</option>
                    <option value=2 <?php isselected($RepeatTue); ?>>Repeat Tuesday</option>
                    <option value=3 <?php isselected($RepeatWed); ?>>Repeat Wednesday</option>
                    <option value=4 <?php isselected($RepeatThu); ?>>Repeat Thursday</option>
                    <option value=5 <?php isselected($RepeatFri); ?>>Repeat Friday</option>
                    <option value=6 <?php isselected($RepeatSat); ?>>Repeat Saturday</option>
                    <option value=7 <?php isselected($RepeatSun); ?>>Repeat Sunday</option>
                </select>
            </div>
            <!-- Cue Edit Card Enabled -->
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="enabled" name="enabled" <?php isenabled($Enabled); ?>>
                <label class="custom-control-label" for="enabled">Cue Enabled</label>
            </div>
            <br/>
        </div>
        <!-- Cue Edit Card Footer-->
        <div class="modal-footer">
            <input type="submit" class="btn btn-success" value="Apply Changes" name="apply">
            <form method="post">
                <input type="submit" class="btn btn-danger" value="Delete Cue" name="delete">
            </form>
            <a href="<?php echo $siteconfig['baseurl'];?>index.php/cues" class="btn btn-secondary">Return</a>
        </div>
    </form>
</div>

<script type="text/javascript">
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

