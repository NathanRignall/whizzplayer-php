<?php
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */

    //Load correct page
    $SetPage = $urlparts[4];

    $SetSites = array("main"=>"settings/main.php", 
                    "system"=>"settings/system.php",
                    "users"=>"settings/users.php");

    //If no site is found in index user is sent to home page
    if (empty($SetSites[$SetPage])) {
        $SettingPage = "settings/main.php";
        $SetPage = "main";
    } else {
        $SettingPage = $SetSites[$SetPage];
    }
?>

<h1>Settings</h1>

<br>

<ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link <?php echo ($SetPage=="main") ? "active" : ""; ?>" href="<?php echo$BASEURL;?>index.php/settings/main">Main</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($SetPage=="system") ? "active" : ""; ?>" href="<?php echo$BASEURL;?>index.php/settings/system">System</a>
  </li>
  <li class="nav-item">
    <a class="nav-link <?php echo ($SetPage=="users") ? "active" : ""; ?>" href="<?php echo$BASEURL;?>index.php/settings/users">Users</a>
  </li>
</ul>

<br>

<?php include_once $SettingPage ?>