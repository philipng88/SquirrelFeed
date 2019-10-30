<?php 
include("includes/header.php");
include("includes/classes/User.php");
include("includes/classes/Post.php");

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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="assets/js/infinite_scroll.js"></script>
<script>
    $('[data-toggle="tooltip"]').tooltip();
</script>
</body>
</html>