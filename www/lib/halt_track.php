<?php
  /*!
  * Whizz Player 0.1.0-alpha.1
  * Nathan Rignall
  * 18/09/2020
  */
?>

<div class="card bg-danger text-white">
    <div class="card-body text-center">
      <h1 class="card-text">Warning!</h1>
    </div>
</div>

<br>

<div class="card bg-warning">
    <div class="card-body text-center">
      <h3 class="card-text">Proceeding to halt playback will <b>instantly</b> stop the playback of the current track on this device.<br>Press <b>okay</b> to continue or return to abort halting track!</h3>
    </div>
</div>

<br>

<div class="text-center">
<a href="<?php echo $siteconfig['baseurl']; ?>index.php/halt-track-now" class="btn btn-danger p-4" role="button"><h1>Okay</h1></a>
<a href="<?php echo $siteconfig['baseurl']; ?>" class="btn btn-success p-4" role="button"><h1>Return</h1></a>
</div>