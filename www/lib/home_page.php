<?php 
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */

    //Server side for instant playback
    //If the play button is pressed
    if(isset($_POST['play'])){
        // Set selected track var
        foreach ($_POST['trackselect'] as $selectedTrack) {
            $TrackIDInstant = $selectedTrack;
        }
        $sqlInstantPlay = "UPDATE InstantPlay SET TrackID = " . $TrackIDInstant . ", Played = 0";
        if ($conn->query($sqlInstantPlay) === TRUE) {
            $_SESSION["info-headertitle"] = "Success!";
            $_SESSION["info-bodyinfo"] = "Track activated successfully";
            $_SESSION["info-targeturl"] = "home";
            $_SESSION["info-iserror"] = "n";
        } else {
            $_SESSION["info-headertitle"] = "Error!";
            $_SESSION["info-bodyinfo"] = "Error... Could not activate track. Internal SQL Error. : " . $conn->error;
            $_SESSION["info-targeturl"] = "home";
            $_SESSION["info-iserror"] = "y";
        }
        header("Location: " . $INFOURL);
        ob_end_flush();
    }
?>

<!-- Homepage Top Header -->
<div class="jumbotron">
    <h1><?php echo $siteconfig['systemname'];?></h1>
    <span class="badge badge-primary mb-1"><?php echo $siteconfig['version']; ?></span>
    <p><?php echo $siteconfig['systeminfo'];?></p>
    <a href="<?php echo $siteconfig['baseurl'];?>index.php/cues?createinstant=Y" class="btn btn-dark btn-lg mt-1" role="button">Create Cue</a>
    <a href="<?php echo $siteconfig['baseurl'];?>index.php/tracks?uploadinstant=Y" class="btn btn-secondary btn-lg mt-1" role="button">Upload Track</a>
    <a href="<?php echo $siteconfig['baseurl'];?>index.php/halt-track" class="btn btn-danger btn-lg mt-1" role="button">HALT TRACK</a>
</div>

<!-- Main card grid system -->
<div class="row row-cols-1 row-cols-md-2">
    <!-- Now Playing Item+Card -->
    <div class="col mb-4">
        <div class="card">
            <div id="nowplayingdiv" class="card-header bg-secondary text-white">
                <h2>Now Playing</h2>
            </div>
            <div class="card-body text-center">
                <h3 id="nowplaying">No Track Playing</h3>
            </div>
        </div>
    </div>
    <!-- System Time Item+Card -->
    <div class="col mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2>System Time</h2>
            </div>
            <div class="card-body text-center">
                <h3 id="systemtime">No Track Playing</h3>
            </div>
        </div>
    </div>
    <!-- Instant Play Item+Card -->
    <div class="col mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h2>Instant Play</h2>
            </div>
            <div class="card-body">
                <form class="form-inline text-center" method="post" action="">
                    <!-- Cue Edit Card Track Select -->
                    <select name="trackselect[]" class="form-control mr-sm-2" required>
                        <?php 
                            $sqlTrackInfo = "SELECT * FROM Tracks";
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
                    <input type="submit" class="btn btn-primary " value="Play Song Now" name="play">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    //SSE Event for Now playing feild
    if(typeof(EventSource) !== "undefined") {
        var source1 = new EventSource("<?php echo $siteconfig['baseurl'];?>stats.php/playing");
        source1.onmessage = function(event) {
            document.getElementById("nowplaying").innerHTML = event.data;
            if (event.data == "No Track Playing") {
                document.getElementById("nowplayingdiv").classList.remove("bg-warning");
                document.getElementById("nowplayingdiv").classList.add("bg-secondary");
            } else {
                document.getElementById("nowplayingdiv").classList.remove("bg-secondary");
                document.getElementById("nowplayingdiv").classList.add("bg-warning");
            }
        };
    } else {
        document.getElementById("nowplaying").innerHTML = "Sorry, your browser does not support server-sent events...";
    };

    //SSE Event for system time feild
    if(typeof(EventSource) !== "undefined") {
        var source2 = new EventSource("<?php echo $siteconfig['baseurl'];?>stats.php/time");
        source2.onmessage = function(event) {
            document.getElementById("systemtime").innerHTML = event.data;
        };
    } else {
        document.getElementById("systemtime").innerHTML = "Sorry, your browser does not support server-sent events...";
    };
</script>