<?php 
include("includes/header.php");
include("includes/form_handlers/settings_handler.php");
?>

<div class="main_column column">
    <h4>Account Settings</h4>
    <?php
    echo "<img src='" . $user['profile_pic'] ."' id='small_profile_pics'>";
    ?>
    <br>
    <a href="upload.php">Upload new profile picture</a><br><br><br>
    <h6>Edit Account Details</h6>
    <?php
    $user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
    $row = mysqli_fetch_array($user_data_query);
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $email = $row['email'];
    ?>
    <form action="settings.php" method="POST">
        First Name: <input type="text" name="first_name" value="<?php echo $first_name; ?>"><br>
        Last Name: <input type="text" name="last_name" value="<?php echo $last_name; ?>"><br>
        Email: <input type="email" name="email" value="<?php echo $email; ?>"><br>
        <?php echo $message; ?>
        <input type="submit" value="Submit" name="update_details" class="save_account_details"><br>
    </form>

    <h4>Change Password</h4>
    <form action="settings.php" method="POST">
        Current Password: <input type="password" name="current_password"><br>
        New Password: <input type="password" name="new_password_1"><br>
        Confirm New Password: <input type="password" name="new_password_2"><br>
        <?php echo $password_message; ?>
        <input type="submit" value="Submit" name="update_password" class="save_account_details"><br>
    </form>

    <h4>Close Account</h4>
    <form>
        <input type="submit" name="close_account" id="close_account" value="Close Account">
    </form>

<!-- closing wrapper div -->
</div>
</body>
</html>