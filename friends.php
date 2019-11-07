<?php
include("includes/header.php");

if (isset($_GET['username'])) {
    $username = $_GET['username'];
} else {
    $username = $userLoggedIn;
}
?>

<div class="main_column column">
    <h4>My Friends</h4>
    <?php
    $user_obj = new User($con, $username);
    foreach ($user_obj->getFriendsList() as $friend) {
        $friend_obj = new User($con, $friend);
        echo "<a href='$friend'>
                <img id='friendsListProfilePic' src='" . $friend_obj->getProfilePic() ."'>" . $friend_obj->getFirstAndLastName() . "
              </a>
              <br>";
    }
    ?>
    <hr>
    <a href="requests.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<!-- closing wrapper div -->
</div>
</body>
</html>