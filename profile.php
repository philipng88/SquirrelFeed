<?php 
include("includes/header.php");

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
                    echo '<input type="submit" name="remove_friend" class="btn friend_btn btn-danger" value="Remove Friend"><br>';
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
        <div class="posts_area"></div>
        <img id="loading" src="assets/images/icons/loading.gif" alt="Loading icon">
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
<?php include("includes/scripts.php"); ?>
<script src="vendors/js/bootbox.min.js"></script>
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
</body>
</html>