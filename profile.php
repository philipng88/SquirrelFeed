<?php 
include("includes/header.php");
$message_obj = new Message($con, $userLoggedIn);

if (isset($_GET['profile_username'])) {
    $username = $_GET['profile_username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
    $user_array = mysqli_fetch_array($user_details_query);
    $num_friends = (substr_count($user_array['friend_array'], ",")) - 1;
}

if (isset($_POST['remove_friend'])) {
    $user = new User($con, $userLoggedIn);
    $user->removeFriend($username);
}

if (isset($_POST['add_friend'])) {
    $user = new User($con, $userLoggedIn);
    $user->sendRequest($username);
}

if (isset($_POST['respond_request'])) {
    header("Location: requests.php");
}

if (isset($_POST['post_message'])) {
    if (isset($_POST['message_body'])) {
        $body = mysqli_real_escape_string($con, $_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($username, $body, $date);
    }
    $link = '#profileTabs a[href="#messages_div"]';
    echo "<script>
            $(function() {
                $('" . $link ."').tab('show');
            });
          </script>";
}
?>

    <style>
        .wrapper {
            margin-left: 0;
            padding-left: 0;
        }
    </style>

    <div class="profile_left">
        <img src="<?php echo $user_array['profile_pic']; ?>" alt="Profile picture">
        <div class="profile_info">
            <p><?php echo "Posts: " . $user_array['num_posts']; ?></p>
            <p><?php echo "Likes: " . $user_array['num_likes']; ?></p>
            <p><?php echo "Friends: " . $num_friends; ?></p>
        </div>
        <form action="<?php echo $username; ?>" method="POST">
            <?php 
            $profile_user_obj = new User($con, $username); 
            if ($profile_user_obj->isClosed()) {
                header("Location: user_closed.php");
            }
            $logged_in_user_obj = new User($con, $userLoggedIn);
            if ($userLoggedIn != $username) {
                if ($logged_in_user_obj->isFriend($username)) {
                    echo '<input type="submit" name="remove_friend" class="btn friend_btn btn-danger" value="Remove Friend"><br>
                            <a class="btn friend_btn btn-secondary" href="messages.php?u=' . $username . '">Send Message</a>';
                }
                else if ($logged_in_user_obj->didReceiveRequest($username)) {
                    echo '<input type="submit" name="respond_request" class="btn friend_btn btn-warning" value="Respond to Request"><br>';
                }
                else if ($logged_in_user_obj->didSendRequest($username)) {
                    echo '<input type="submit" name="" class="btn friend_btn req_sent_btn" value="Friend Request Sent" disabled><br>';
                }
                else {
                    echo '<input type="submit" name="add_friend" class="btn friend_btn btn-success" value="Add Friend"><br>';
                }
            } 
            ?> 
        </form>
        <input type="submit" class="btn btn-primary profile_add_post_btn" data-toggle="modal" data-target="#post_form" value="Add Post">
        <?php 
        if ($userLoggedIn != $username) {
            echo "<div class='profile_info_bottom'>";
            echo $logged_in_user_obj->getMutualFriends($username) . " Mutual Friends"; 
            echo "</div>";
        }
        ?>
    </div>

    <div class="profile_main_column column">
        <ul class="nav nav-tabs" role="tablist" id="profileTabs">
            <li class="nav-item">
                <a class="nav-link active" href="#wall_div" aria-controls="#wall_div" role="tab" data-toggle="tab">Wall</a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="#messages_div" aria-controls="#messages_div" role="tab" data-toggle="tab">Messages</a>
            </li> -->
            <?php if ($username !== $userLoggedIn) { ?>
                <li class="nav-item">
                    <a class="nav-link" href="#messages_div" aria-controls="#messages_div" role="tab" data-toggle="tab">Messages</a>
                </li>
            <?php } ?>
        </ul>
        <div class="tab-content mt-3">
            <div role="tabpanel" class="tab-pane fade in show active" id="wall_div">
                <div class="posts_area"></div>
                <img id="loading" src="assets/images/icons/loading.gif" alt="Loading icon">
            </div>
            <div role="tabpanel" class="tab-pane fade" id="messages_div">
                <?php 
                echo "<h4>Your conversation with " . $profile_user_obj->getFirstAndLastName() . "</a></h4><hr><br>";
                echo "<div class='loaded_messages' id='scroll_messages'>";
                    echo $message_obj->getMessages($username);
                echo "</div>";
                ?>

                <div class="message_post">
                    <form action="" method="POST">
                        <textarea name='message_body' id='message_textarea' placeholder='Write your message...'></textarea>
                        <input type='submit' name='post_message' class='info' id='message_submit' value='Send' style="width: 10.75%;">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="postModalLabel">Add Post</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form class="profile_post" action="profile.php" method="POST">
                        <div class="form-group">
                            <textarea class="form-control" name="post_body" id="post_form_content"></textarea>
                            <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
                            <input type="hidden" name="user_to" value="<?php echo $username; ?>">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
                </div>
            </div>
        </div>
    </div>

<!-- Close wrapper div from header -->
</div>
<script>
    $(() => {
        let userLoggedIn = "<?php echo $userLoggedIn; ?>";
        let profileUsername = "<?php echo $username; ?>";
        let inProgress = false;

        loadPosts = () => {
            if (inProgress) {
                return;
            }

            inProgress = true;
            $("#loading").show();

            let page =
                $(".posts_area")
                    .find(".nextPage")
                    .val() || 1;
            $.ajax({
                url: "includes/handlers/ajax_load_profile_posts.php",
                type: "POST",
                data:
                    "page=" +
                    page +
                    "&userLoggedIn=" +
                    userLoggedIn +
                    "&profileUsername=" +
                    profileUsername,
                cache: false,

                success: response => {
                    $(".posts_area")
                        .find(".nextPage")
                        .remove();
                    $(".posts_area")
                        .find(".noMorePosts")
                        .remove();
                    $(".posts_area")
                        .find(".noMorePostsText")
                        .remove();

                    $("#loading").hide();
                    $(".posts_area").append(response);

                    inProgress = false;
                }
            });
        };

        isElementInView = el => {
            if (el == null) {
                return;
            }

            let rect = el.getBoundingClientRect();

            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <=
                    (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
                rect.right <=
                    (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
            );
        };

        loadPosts();

        $(window).scroll(() => {
            let bottomElement = $(".status_post").last();
            let noMorePosts = $(".posts_area")
                .find(".noMorePosts")
                .val();

            if (isElementInView(bottomElement[0]) && noMorePosts == "false") {
                loadPosts();
            }
        });
    });
</script>
<script>
    let div = document.getElementById("scroll_messages")
    if (div != null) div.scrollTop = div.scrollHeight;
</script>
</body>
</html>