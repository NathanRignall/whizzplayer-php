<?php 
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */
    
    $pageurl = htmlspecialchars($_SERVER['PHP_SELF']);
    $urlparts = Explode('/', $pageurl);

    //Include Important
    if (file_exists("config/config.php")) {
        require_once "config/config.php";
        require_once 'lib/sql_connect.php';
    }
    else {
        header("location: /" .  $urlparts[1] . "/setup.php");
    }

    //Initialize the session
    session_start();
    
    //vars
    $INFOURL = $siteconfig['baseurl'] . "index.php/info";
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
    if (empty($pageuri)) {
        $pageuri = "home";
    } elseif (empty($sites[$pageuri])) {
        $_SESSION["info-headertitle"] = "Error! 404";
        $_SESSION["info-bodyinfo"] = "Page Not Found!";
        $_SESSION["info-targeturl"] = "";
        $_SESSION["info-iserror"] = "y";
        header("Location: " . $INFOURL);
        ob_end_flush();
    }

    $page = $sites[$pageuri];
    
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

<nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark">
    <span class="navbar-brand mb-0 h1"><?php echo $siteconfig['systemname'];?></span>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link <?php echo ($pageuri=="home") ? "active" : ""; ?>" href="<?php echo $siteconfig['baseurl'];?>index.php/">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($pageuri=="cues") ? "active" : ""; ?>" href="<?php echo $siteconfig['baseurl'];?>index.php/cues">Cues</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo ($pageuri=="tracks") ? "active" : ""; ?>" href="<?php echo $siteconfig['baseurl'];?>index.php/tracks">Tracks</a>
            </li>
            <?php
                //Check if user is admin
                if($_SESSION["UserType"] == 1){
                    echo' <li class="nav-item"><a class="nav-link ';
                    echo ($pageuri=="settings") ? "active" : "";
                    echo '" href="' . $siteconfig['baseurl'] . 'index.php/settings">Settings</a></li> ';
                }
            ?>
        </ul>
        
        <form class="form-inline my-2 my-lg-0">
            <a class="btn btn-outline-light my-2 my-sm-0" href="<?php echo $siteconfig['baseurl'];?>logout.php">Logout</a>
            <span class="navbar-text pl-3">
                <?php echo $_SESSION["username"];?> 
            </span>
        </form>
    </div>
</nav>

<!-- Full contaner-->
<div class="container" style="margin-top:80px">
    <?php include_once $page ?>
</div>

</body>