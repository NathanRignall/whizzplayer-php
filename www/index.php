<?php 
    /*!
    * Whizz Player v0.1.0
    * Nathan Rignall
    * 18/09/2020
    */
    
    //Include Header
    require_once 'lib/vars.php';
    require_once 'lib/sql_connect.php';

    ob_start();
    session_start();

    //global vars
    $INFOURL = $BASEURL . "index.php/info";
    $pageuri = basename(($_SERVER['REQUEST_URI']));
    $pageuri = strtok($pageuri, '?');

    $sites = array("cues"=>"lib/cue_list.php", 
                   "cue-edit"=>"lib/cue_edit.php",
                   "tracks"=>"lib/track_list.php",
                   "track-edit"=>"lib/track_edit.php",
                   "upload"=>"lib/tracks_upload.php",
                   "info"=>"lib/info_page.php",
                   "halt-track"=>"lib/halt_track_now.php",
                   "settings"=>"lib/system_settings.php",
                   "home"=>"lib/home_page.php");

    if (empty($sites[$pageuri])) {
        $page = "lib/home_page.php";
    } else {
        $page = $sites[$pageuri];
    }
    
    require_once 'lib/main_header.php';

    // Initialize the session
    session_start();
    
    // Check if the user is logged in, if not then redirect him to login page
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: " . $BASEURL . "login.php");
        exit;
    }
?>

<body>

<!-- Main navbar-->
<nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top">
<a class="navbar-brand" href="<?php echo$BASEURL;?>index.php">Music Player</a>
<ul class="navbar-nav">
    <li class="nav-item">
    <a class="nav-link" href="<?php echo$BASEURL;?>index.php/cues">Cues</a>
    </li>
    <li class="nav-item">
    <a class="nav-link" href="<?php echo$BASEURL;?>index.php/tracks">Tracks</a>
    </li>
    <li class="nav-item">
    <a class="nav-link" href="<?php echo$BASEURL;?>index.php/settings">Settings</a>
    </li>
    <li class="nav-item">
    <a class="nav-link" href="<?php echo$BASEURL;?>logout.php">Logout</a>
    </li>
</ul>
</nav>

<!-- Full contaner-->
<div class="container" style="margin-top:80px">
    <?php include_once $page ?>
</div>

</body>