<?php 
include("includes/header.php");

if (isset($_POST['post'])) {
    $post = new Post($con, $userLoggedIn);
    $post->submitPost($_POST['post_text'], 'none');
    header("Location: index.php");
}

?>
    <div class="user_details column">
        <a href="<?php echo $userLoggedIn; ?>"><img src="<?php echo $user['profile_pic']; ?>" alt="User profile picture"></a>
        <div class="user_details_left_right">
            <a href="<?php echo $userLoggedIn; ?>">
                <?php 
                echo $user['first_name'] . " " . $user['last_name'];
                ?>
            </a>
            <br>
            <?php 
            echo "Posts: " . $user['num_posts'] . "<br>";
            echo "Likes: " . $user['num_likes'];
            ?>
        </div>
    </div>

    <div class="main_column column">
        <form class="post_form" action="index.php" method="POST">
            <textarea name="post_text" id="post_text" placeholder="What's on your mind?"></textarea>
            <input type="submit" name="post" id="post_button" value="Post">
            <hr>
        </form>
        <div class="posts_area"></div>  
        <img id="loading" src="assets/images/icons/loading.gif" alt="Loading icon">
    </div>

<!-- Close wrapper div from header -->
</div>
<script src="assets/js/infinite_scroll.js"></script>
</body>
</html>