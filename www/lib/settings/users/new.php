<?php
// Define variables and initialize with empty values
$username = $password = $confirm_password = $username_err = $password_err = $confirm_password_err = "";
 
// Server side for when user is created
if($_SERVER["REQUEST_METHOD"] == "POST"){
     // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        $sql = "SELECT UserID FROM Users WHERE Username = ?";
        
        if($stmt = $conn->prepare($sql)){
            $stmt->bind_param("s", $param_username);
            
            $param_username = trim($_POST["username"]);
            
            // Execute the prepared statement
            if($stmt->execute()){
                // store result
                $stmt->store_result();
                
                if($stmt->num_rows == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        $sql = "INSERT INTO Users (Username, Password) VALUES (?, ?)";
         
        if($stmt = $conn->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("ss", $param_username, $param_password);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Redirect to login page
                //header("location: login.php");
                echo "Created successfully";
            } else{
                echo "Something went wrong. Please try again later.";
            }
            $stmt->close();
        }
    }
}
?>
 
<h3>Create User</h3>
<p>Please fill this form to create an account.</p>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
        <label>Username</label>
        <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
        <span class="help-block"><?php echo $username_err; ?></span>
    </div>    
    <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
        <label>Password</label>
        <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
        <span class="help-block"><?php echo $password_err; ?></span>
    </div>
    <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
        <span class="help-block"><?php echo $confirm_password_err; ?></span>
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Create">
    </div>
</form>