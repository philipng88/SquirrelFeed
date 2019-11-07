<?php 
include("includes/header.php");

$message_obj = new Message($con, $userLoggedIn);

if(isset($_GET['u'])) {
    $user_to = $_GET['u'];
} else {
    $user_to = $message_obj->getMostRecentUser();
    if ($user_to == false) {
        $user_to = 'new';
    }
}

if ($user_to != "new") {
    $user_to_obj = new User($con, $user_to);
}

if (isset($_POST['post_message']) && $user_to !== $userLoggedIn) {
    if (isset($_POST['message_body'])) {
        $body = mysqli_real_escape_string($con, $_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($user_to, $body, $date);
    }
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

<div class="main_column column" id="main_column">
    <?php 
    if ($user_to != "new") {
        $open_query = mysqli_query($con, "SELECT opened, id FROM messages WHERE user_from='$userLoggedIn' AND user_to='$user_to' ORDER BY id DESC LIMIT 1");
        $latest_query_rec = mysqli_query($con, "SELECT id FROM messages WHERE user_to='$userLoggedIn' AND user_from='$user_to' ORDER BY id DESC LIMIT 1");
        $check_message = mysqli_fetch_array($open_query);
        $check_latest = mysqli_fetch_array($latest_query_rec);
        $seen = $check_message['opened'] === 'yes' ? "Message read" : "";

        echo "<h4>You and <a href='$user_to'>" . $user_to_obj->getFirstAndLastName() . "</a></h4><hr><br>";
        echo "<div class='loaded_messages' id='scroll_messages'>";
            echo $message_obj->getMessages($user_to);
        if ($check_message['id'] > $check_latest['id']) {
            echo "<div style='float:right; position:relative; bottom:5px; right:3px;' class='small text-muted'>" . $seen . "</div><br>";
        }
        echo "</div>";
    } else {
        echo "<h4>New Message</h4>";
    }
    ?>

    <div class="message_post">
        <form action="" method="POST">
            <?php
            if ($user_to == "new") {
                echo "Select who you would like to message <br><br>";
            ?> 
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">To:</span>
                    </div>
                    <input 
                        type="text" 
                        class="form-control" 
                        onkeyup="getUsers(this.value, '<?php echo $userLoggedIn; ?>')" 
                        name="q" 
                        placeholder="Name" 
                        autocomplete="off" 
                        id="search_text_input"
                    >
                </div>
            <?php
                echo "<div class='results'></div>";
            } else {
                echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message...' class='mb-1'></textarea>";
                echo "<input type='submit' name='post_message' class='info float-right' id='message_submit' value='Send'>";
            }
            ?>
        </form>
    </div>
</div>

<div class="user_details column" id="conversations">
    <h4>Conversations</h4>
    <div class="loaded_conversations">
        <?php echo $message_obj->getConversations(); ?>
    </div>
    <br>
    <a class="btn btn-outline-primary" role="button" href="messages.php?u=new">New Message</a>
</div>

<!-- Closing wrapper div -->
</div>
<script>
    let div = document.getElementById("scroll_messages")
    if (div != null) div.scrollTop = div.scrollHeight;
</script>
</body>
</html>