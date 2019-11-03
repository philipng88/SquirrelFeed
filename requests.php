<?php
include("includes/header.php");
?>

<div class="main_column column" id="main_column">
    <h4>Requests sent</h4>
        <?php
        $pending = mysqli_query($con, "SELECT user_to FROM friend_requests WHERE user_from='$userLoggedIn'");
        if (mysqli_num_rows($pending) > 0) {
            while ($row = mysqli_fetch_array($pending)) {
                $sent = $row['user_to'];
                $dataQuery = mysqli_query($con, "SELECT * FROM users WHERE username='$sent'");
                    while ($name = mysqli_fetch_array($dataQuery)) {
                    echo "<a href='" . $name['username'] . "'><img src='" . $name['profile_pic'] . "' style='height: 50px;'></a>
                        <a href='" . $name['username'] . "'>" . $name['first_name'] . " " . $name['last_name'] . "</a><br>";
                    }
                }
            }
        else {
            echo "You have no pending requests at this time";
            }	
        ?>
    <hr>
    <h4>Friend Requests</h4>
    <?php
    $query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
    if (mysqli_num_rows($query) == 0) {
        echo "You have no friend requests at this time";
    } else {
        while ($row = mysqli_fetch_array($query)) {
            $user_from = $row['user_from'];
            $user_from_obj = new User($con, $user_from);
            $user_name = $user_from_obj->getUserName();
            $full_name = $user_from_obj->getFirstAndLastName();
            // echo $user_from_obj->getFirstAndLastName() . " sent you a friend request!";
            echo "<a href='" . $user_name . "'>" . $full_name . " </a>sent you a friend request!";
            $user_from_friend_array = $user_from_obj->getFriendArray();

            if (isset($_POST['accept_request' . $user_from])) {
                $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$user_from,') WHERE username='$userLoggedIn'");
                $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$userLoggedIn,') WHERE username='$user_from'");
                $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
                echo "You are now friends!";
                header("Location: requests.php");
            }

            if (isset($_POST['ignore_request' . $user_from])) {
                $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
                echo "Request Ignored";
                header("Location: requests.php");
            }
    ?>
            <form action="requests.php" method="POST" class="mb-2">
                <input type="submit" name="accept_request<?php echo $user_from; ?>" id="accept_button" value="Accept">
                <input type="submit" name="ignore_request<?php echo $user_from; ?>" id="ignore_button" value="Ignore">
            </form>
    <?php
        }
    }
    ?>
</div>

<!-- Close wrapper div from header -->
</div>
</body>
</html>