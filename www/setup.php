<?php 
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */

    require_once "config/config-sample.php";
?>

<!DOCTYPE html>
<html lang="en">

<?php require_once 'lib/main_header.php'; ?>

<body>
    <!-- Login Top Header -->
    <div class="jumbotron">
        <h1><?php echo $siteconfig['systemname'];?></h1>
        <span class="badge badge-primary mb-1"><?php echo $siteconfig['version']; ?></span>   
        <p><?php echo $siteconfig['systeminfo'];?></p>  
    </div>

    <div class="container">
        <h2>Enter Details</h2>
        <p>Please fill in details to setup Whizz Player</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>System Name</label>
                <input type="text" name="SystemName" class="form-control" value="<?php echo $SystemName_Set; ?>">
                <small class="form-text text-muted">
                    <?php echo $SystemName_Err; ?>
                </small>
            </div>
            <div class="form-group">
                <label>System Info</label>
                <input type="text" name="SystemInfo" class="form-control" value="<?php echo $SystemInfo_Set; ?>">
                <small class="form-text text-muted">
                    <?php echo $SystemInfo_Err; ?>
                </small>
            </div>
            <div class="form-group">
                <label>Databse Server</label>
                <input type="text" name="DatabaseServer" class="form-control" value="<?php echo $DatabaseServer_Set; ?>">
                <small class="form-text text-muted">
                    <?php echo $DatabaseServer_Err; ?>
                </small>
            </div> 
            <div class="form-group">
                <label>Databse Name</label>
                <input type="text" name="DatabaseName" class="form-control" value="<?php echo $DatabaseName_Set; ?>">
                <small class="form-text text-muted">
                    <?php echo $DatabaseName_Err; ?>
                </small>
            </div> 
            <div class="form-group">
                <label>Databse User</label>
                <input type="text" name="DatabaseUser" class="form-control" value="<?php echo $DatabaseUser_Set; ?>">
                <small class="form-text text-muted">
                    <?php echo $DatabaseUser_Err; ?>
                </small>
            </div> 
            <div class="form-group">
                <label>Database Password</label>
                <input type="password" name="DatabasePassword" class="form-control">
                <small class="form-text text-muted">
                    <?php echo $DatabasePassword_Err; ?>
                </small>
            </div>
            <div class="form-group">
                <label>Base URL</label>
                <input type="text" name="BaseURL" class="form-control" value="<?php echo $BaseURL_Set; ?>">
                <small class="form-text text-muted">
                    <?php echo $BaseURL_Err; ?>
                </small>
            </div>
            <div class="form-group">
                <label>Track URL</label>
                <input type="text" name="TrackURL" class="form-control" value="<?php echo $TrackURL_Set; ?>">
                <small class="form-text text-muted">
                    <?php echo $TrackURL_Err; ?>
                </small>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div> 
</body>
</html>