<?php 
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */
    
    //Include Important
    require_once 'config/config.php';
    require_once 'lib/sql_connect.php';

    //Initialize the session
    session_start();
    
    //vars
    $INFOURL = $siteconfig['baseurl'] . "index.php/info";

    $pageurl = htmlspecialchars($_SERVER['PHP_SELF']);
    $urlparts = Explode('/', $pageurl);
    $pageuri = $urlparts[3];

    $sites = array("cues"=>"lib/cue_list.php", 
                   "cue-edit"=>"lib/cue_edit.php",
                   "tracks"=>"lib/track_list.php",
                   "track-edit"=>"lib/track_edit.php",
                   "upload"=>"lib/tracks_upload.php",
                   "info"=>"lib/info_page.php",
                   "halt-track"=>"lib/halt_track.php",
                   "halt-track-now"=>"lib/halt_track_now.php",
                   "settings"=>"lib/system_settings.php",
                   "instant-play"=>"lib/instant_play.php",
                   "home"=>"lib/home_page.php");

    //If no site is found in index user is sent to home page
    if (empty($sites[$pageuri])) {
        $page = "lib/home_page.php";
    } else {
        $page = $sites[$pageuri];
    }
    
    //Initialize the session
    session_start();
    
    //Check if user is  logged in, if not send to login page
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: " . $siteconfig['baseurl'] . "login.php");
        exit;
    }

    //Inculde the header
    require_once 'lib/main_header.php';
?>

<body>

<!-- Main navbar-->
<nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top">
<a class="navbar-brand" href="<?php echo $siteconfig['baseurl'];?>index.php">Music Player</a>
<ul class="navbar-nav">
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $siteconfig['baseurl'];?>index.php/cues">Cues</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $siteconfig['baseurl'];?>index.php/tracks">Tracks</a>
    </li>
    <?php
        //Check if user is admin
        if($_SESSION["UserType"] == 1){
            echo' <li class="nav-item"><a class="nav-link" href="' . $siteconfig['baseurl'] . 'index.php/settings">Settings</a></li> ';
        }
    ?>
    <li class="nav-item">
        <a class="nav-link" href="<?php echo $siteconfig['baseurl'];?>logout.php">Logout</a>
    </li>
</ul>
</nav>

<!-- Full contaner-->
<div class="container" style="margin-top:80px">
    <?php include_once $page ?>
</div>

</body>