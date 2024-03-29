<?php 
include("includes/header.php");

if (isset($_GET['q'])) {
    $query = $_GET['q'];
} else {
    $query = "";
}

if (isset($_GET['type'])) {
    $type = $_GET['type'];
} else {
    $type = "name";
}
?>

<div class="main_column column" id="main_column">
    <?php 
    if ($query == "") {
        echo "You must enter something in the search box";
    } else {
        if ($type == "username") {
            $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
        } else {
            $names = explode(" ", $query);
            if (count($names) == 3) {
                $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%') AND user_closed='no'");
            }
            else if (count($names) == 2) {
                $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no'");
            }
            else {
                $usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no'");
            }
        }
        if (mysqli_num_rows($usersReturnedQuery) == 0) {
            echo "We cannot find anyone with a " . $type . " like: " .$query;
        } else {
            echo mysqli_num_rows($usersReturnedQuery) . " results found: <br><br>";
        }
        echo "<p class='text-muted'>Try searching for:</p>";
        echo "<a href='search.php?q=" . $query . "&type=name'>Names</a>, <a href='search.php?q=" . $query . "&type=username'>Usernames</a><br><hr class='mb-0'>";
        while ($row = mysqli_fetch_array($usersReturnedQuery)) {
            $user_obj = new User($con, $user['username']);
            $button = "";
            $mutual_friends = "";
            if ($user['username'] != $row['username']) {
                if ($user_obj->isFriend($row['username'])) {
                    $button = "<input type='submit' name='" . $row['username'] . "' class='alt-btn-danger' value='Remove Friend'>";
                }
                else if ($user_obj->didReceiveRequest($row['username'])) {
                    $button = "<input type='submit' name='" . $row['username'] . "' class='alt-btn-warning' value='Respond to Request'>";
                }
                else if ($user_obj->didSendRequest($row['username'])) {
                    $button = "<input type='submit' class='alt-btn-default' value='Friend Request Sent' disabled>";
                }
                else {
                    $button = "<input type='submit' name='" . $row['username'] . "' class='alt-btn-success' value='Add Friend'>";
                }
                $mutual_friends = $user_obj->getMutualFriends($row['username']) . " mutual friends";

                if (isset($_POST[$row['username']])) {
                    if ($user_obj->isFriend($row['username'])) {
                        $user_obj->removeFriend($row['username']);
                        header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                    }
                    else if ($user_obj->didReceiveRequest($row['username'])) {
                        header("Location: requests.php");
                    }
                    else if ($user_obj->didSendRequest($row['username'])) {
                        // Cancel request?
                    }
                    else {
                        $user_obj->sendRequest($row['username']);
                        header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                    }
                }
            }

            echo "<div class='search_result'>
                    <div class='searchPageFriendButtons float-right'>
                        <form action='' method='POST'>
                            " . $button . "
                        </form>
                    </div>
                    <div class='result_profile_pic float-left mr-2'>
                        <a href='" . $row['username'] . "'><img src='" . $row['profile_pic'] ."' style='height: 100px;'></a>
                    </div>
                        <a href='" . $row['username'] ."'> " . $row['first_name'] . " " . $row['last_name'] . "
                            <p class='text-muted'> " . $row['username'] . "</p>
                        </a>
                        <br>
                        " . $mutual_friends ."
                        <br>
                </div>
                <hr>";
        }
    }
    ?>
</div>

<!-- closing wrapper div -->
</div>
</body>
</html>