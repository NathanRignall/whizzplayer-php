<?php 
  /*!
  * Whizz Player 0.1.0-alpha.1
  * Nathan Rignall
  * 18/09/2020
  */
  //Put session info into vars to display
  $headertitle = $_SESSION["info-headertitle"];
  $bodyinfo = $_SESSION["info-bodyinfo"];
  $targeturl = $_SESSION["info-targeturl"];
  $iserror = $_SESSION["info-iserror"];

  $refreshurl = $siteconfig['baseurl'] . "index.php/" . $targeturl;

  //Put session info into vars to display
  if ($iserror == "y") {
    $cardcol = "bg-danger";
  } else {
    $cardcol = "bg-success";
    header('Refresh: 1.5; url='.$refreshurl);
  }
  $_SESSION["info-headertitle"] = $_SESSION["info-bodyinfo"] = $_SESSION["info-targeturl"] = $_SESSION["info-iserror"] = "";
?>

<br>
<br>

<!-- Main Info Card-->
<div class="card">
  <div class="card-header text-white <?php echo $cardcol;?>">
      <h2 class="card-title"><?php echo $headertitle;?></h2>
  </div>
  <div class="card-body">
  <p class="card-text"><?php echo $bodyinfo;?></p>
    <?php 
        if ($iserror == "y") {
          echo "<br><a href='" . $refreshurl . "' class='btn btn-primary mt-1'>Okay</a>";
        }
    ?>
  </div>
</div>