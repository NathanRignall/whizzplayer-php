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

<a href="users/new/" class="btn btn-primary" role="button">Create User</a>
<input type="submit" value="Delete Selected Users" class="btn btn-danger" name="submit">

<br><br>

<?php
    if ($resultUsers->num_rows > 0) {
        while($rowUsers = $resultUsers->fetch_assoc()) {
            echo $rowUsers["Username"] . $rowUsers["LastLogin"] . $rowUsers["UserType"]. "<br>";
        }
    }
?>