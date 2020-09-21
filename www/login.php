<?php
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 18/09/2020
    */

    //Include Important
    require_once 'lib/vars.php';
    require_once 'lib/sql_connect.php';

    //Initialize the session
    session_start();
    
    //Check if user is already logged in. If so send to index.php  
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
        header("location:" . $BASEURL . "index.php");
        exit;
    }
    
    //Define vars
    $username = $password = $username_err = $password_err = "";
    
    // Server side for logging into the site
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        // Check if username is empty
        if(empty(trim($_POST["username"]))){
            $username_err = "Please enter username.";
        } else{
            $username = trim($_POST["username"]);
        }
        // Check if password is empty
        if(empty(trim($_POST["password"]))){
            $password_err = "Please enter your password.";
        } else{
            $password = trim($_POST["password"]);
        }
        //Validate credentials
        if(empty($username_err) && empty($password_err)){
            $sql = "SELECT UserID, Username, Password, UserType FROM Users WHERE Username = ?";
            if($stmt = $conn->prepare($sql)){
                $stmt->bind_param("s", $param_username);
                $param_username = $username;
                //Execute the prepared statement
                if($stmt->execute()){
                    $stmt->store_result();
                    //Check if username exists, if yes then verify password
                    if($stmt->num_rows == 1) {
                        $stmt->bind_result($id, $username, $hashed_password, $UserType);
                        if($stmt->fetch()){
                            if(password_verify($password, $hashed_password)){
                                //Password is correct and start a new session
                                session_start();
                                //Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["username"] = $username;       
                                $_SESSION["UserType"] = $UserType;                      
                                // Redirect user to welcome page
                                header("location:" . $BASEURL . "index.php");
                            } else{
                                //Error message if password is not valid
                                $password_err = "The password you entered was incorrect.";
                            }
                        }
                    } else {
                        //Error message if username doesn't exist
                        $username_err = "No account found with that username.";
                    }
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                    //IMPROVE HERE
                }
                $stmt->close();
            }
        }
        $conn->close();
    }
?>
 
<!DOCTYPE html>
<html lang="en">

<?php require_once 'lib/main_header.php'; ?>

<body>
    <!-- Login Top Header -->
    <div class="jumbotron">
        <h1><?php echo $systemname;?></h1>
        <span class="badge badge-primary mb-1"><?php echo $version; ?></span>   
        <p><?php echo $systeminfo;?></p>  
    </div>

    <div class="container" style="margin-top:80px">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <small class="form-text text-muted">
                    <?php echo $username_err; ?>
                </small>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <small class="form-text text-muted">
                    <?php echo $password_err; ?>
                </small>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div> 
</body>
</html>