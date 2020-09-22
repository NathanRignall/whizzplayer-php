<?php
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 19/09/2020
    */

    // Initialize the session
    session_start();
    
    // Define variables and initialize with empty values
    $new_password = $confirm_password = $new_password_err = $confirm_password_err = "";
 
    //Get username
    $ResetUser = htmlspecialchars($urlparts[6]);

    // Processing form data when form is submitted
    if($_SERVER["REQUEST_METHOD"] == "POST"){
    
        // Validate new password
        if(empty(trim($_POST["new_password"]))){
            $new_password_err = "Please enter the new password.";     
        } elseif(strlen(trim($_POST["new_password"])) < 6){
            $new_password_err = "Password must have atleast 6 characters.";
        } else{
            $new_password = trim($_POST["new_password"]);
        }
        
        // Validate confirm password
        if(empty(trim($_POST["confirm_password"]))){
            $confirm_password_err = "Please confirm the password.";
        } else{
            $confirm_password = trim($_POST["confirm_password"]);
            if(empty($new_password_err) && ($new_password != $confirm_password)){
                $confirm_password_err = "Password did not match.";
            }
        }
            
        // Check input errors before updating the database
        if(empty($new_password_err) && empty($confirm_password_err)){
            // Prepare an update statement
            $sql = "UPDATE Users SET Password = ? WHERE Username = ?";
            
            if($stmt = $conn->prepare($sql)){
                $stmt->bind_param("ss", $param_password, $param_username);
                
                $param_password = password_hash($new_password, PASSWORD_DEFAULT);
                $param_username = $ResetUser;
                
                // Attempt to execute the prepared statement
                if($stmt->execute()){
                    // Password updated successfully. Destroy the session, and redirect to user page
                    $redirecturl = $siteconfig['baseurl'] . "index.php/settings/users";
                    header("location: " . $redirecturl);
                } else{
                    echo "Oops! Something went wrong. Please try again.";
                }
                $stmt->close();
            }
        }
        $conn->close();
    }
?>

<h2>Reset Password for <?php echo $ResetUser; ?></h2>
<p>Please fill out this form to reset their password.</p>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
    <div class="form-group">
        <label>New Password</label>
        <input type="password" name="new_password" class="form-control" value="<?php echo $new_password; ?>">
        <small class="form-text text-muted">
            <?php echo $new_password_err; ?>
        </small>
    </div>
    <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control">
        <small class="form-text text-muted">
            <?php echo $confirm_password_err; ?>
        </small>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Submit">
        <a class="btn btn-link" href="<?php echo $siteconfig['baseurl'] ?>index.php/settings/users/">Cancel</a>
    </div>
</form>