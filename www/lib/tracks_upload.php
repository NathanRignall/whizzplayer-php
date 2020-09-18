<?php
  /*!
  * Whizz Player 0.1.0-alpha.1
  * Nathan Rignall
  * 18/09/2020
  */

  $TrackDisplayNameAdd = $UploadFileName = "";
  $target_dir = $UPLOADURL;
  $uploadOk = 1;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $TrackDisplayNameAdd = test_input($_POST["trackdisplayname"]);
    $UploadFileName = test_input(basename($_FILES["fileToUpload"]["name"]));
    $target_file = $target_dir . $UploadFileName;
    $songFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

    // Check if file already exists
    if (file_exists($target_file)) {
      $_SESSION["info-headertitle"] = "Error!";
      $_SESSION["info-bodyinfo"] = "Sorry, file already exists. Try renaming the file before upload.  Your file was not uploaded.";
      $_SESSION["info-targeturl"] = "tracks";
      $_SESSION["info-iserror"] = "y";
      $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 134217728) {
      $_SESSION["info-headertitle"] = "Error!";
      $_SESSION["info-bodyinfo"] = "Sorry, your file is too large.  Your file was not uploaded.";
      $_SESSION["info-targeturl"] = "tracks";
      $_SESSION["info-iserror"] = "y";
      $uploadOk = 0;
    }

    // Allow certain file formats
    if($songFileType != "mp3" && $songFileType != "wav") {
      $_SESSION["info-headertitle"] = "Error!";
      $_SESSION["info-bodyinfo"] = "Sorry, only MP3, WAV files are allowed. You need to convert the song into the correct format. Your file was not uploaded.";
      $_SESSION["info-targeturl"] = "tracks";
      $_SESSION["info-iserror"] = "y";
      $uploadOk = 0;
    }

    $sql = "INSERT INTO Tracks (TrackDisplayName,SongFile)
    VALUES ('". $TrackDisplayNameAdd . "', '". $UploadFileName . "')";

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
      echo "<br> Sorry, your file was not uploaded.";
    } else {
      if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "<br> The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        if ($conn->query($sql) === TRUE) {
          $_SESSION["info-headertitle"] = "Success!";
          $_SESSION["info-bodyinfo"] = "Track uploaded successfully";
          $_SESSION["info-targeturl"] = "tracks";
          $_SESSION["info-iserror"] = "n";
        } else {
          unlink($target_file);
          $_SESSION["info-headertitle"] = "Error!";
          $_SESSION["info-bodyinfo"] = "Error... Your file was not uploaded. Internal SQL Error. : " . $conn->error;
          $_SESSION["info-targeturl"] = "tracks";
          $_SESSION["info-iserror"] = "y";
        }
      } else {
        $_SESSION["info-headertitle"] = "Error!";
        $_SESSION["info-bodyinfo"] = "Error... Your file was not uploaded. Upload Error.";
        $_SESSION["info-targeturl"] = "tracks";
        $_SESSION["info-iserror"] = "y";
      }
    }
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

?>