<?php 
    /*!
    * Whizz Player 0.1.0-alpha.1
    * Nathan Rignall
    * 19/09/2020
    */

    //Get SQL data for users
    $sqlUsers = "SELECT Username, LastLogin, UserType FROM Users";
    $resultUsers = $conn->query($sqlUsers);
?>

<a href="<?php echo $siteconfig['baseurl']?>index.php/settings/users/new/" class="btn btn-primary" role="button">Create User</a>

<br><br>

<?php
    if ($resultUsers->num_rows > 0) {
        while($rowUsers = $resultUsers->fetch_assoc()) {
            echo $rowUsers["Username"] . $rowUsers["LastLogin"];
            echo ($rowUsers["UserType"]==1) ? " Admin" :" Operator";
            echo "<a class='btn btn-secondary' href='" . $siteconfig['baseurl'] . "index.php/settings/users/reset/" . $rowUsers["Username"] . "'>Reset Password</a><br>";

        }
    }
?>