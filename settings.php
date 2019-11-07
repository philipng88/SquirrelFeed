<?php 
include("includes/header.php");
include("includes/form_handlers/settings_handler.php");
?>

<div class="main_column column">
    <h2>Account Settings</h2>
    <?php
    echo "<img src='" . $user['profile_pic'] ."' id='small_profile_pic'>";
    ?>
    <br>
    <a href="upload.php">Upload new profile picture</a><br><br><br>
    <h4 class="mt-3">Edit Account Details</h4>
    <?php
    $user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
    $row = mysqli_fetch_array($user_data_query);
    $first_name = $row['first_name'];
    $last_name = $row['last_name'];
    $email = $row['email'];
    ?>
    <form action="settings.php" method="POST">
        First Name: <input type="text" name="first_name" class="settings_input mb-2" value="<?php echo $first_name; ?>" autocomplete="off"><br>
        Last Name: <input type="text" name="last_name" class="settings_input mb-2" value="<?php echo $last_name; ?>" autocomplete="off"><br>
        Email: <input type="email" name="email" class="settings_input mb-2" value="<?php echo $email; ?>" autocomplete="off"><br>
        <strong><?php echo $message; ?></strong>
        <input type="submit" value="Submit" name="update_details" class="alt-btn-info settings_submit"><br>
    </form>

    <h4 class="mt-3">Change Password</h4>
    <form action="settings.php" method="POST">
        Current Password: <input type="password" name="current_password" class="settings_input mb-2" required><br>
        New Password: <input type="password" name="new_password_1" class="settings_input mb-2" required><br>
        Confirm New Password: <input type="password" name="new_password_2" class="settings_input mb-2" required><br>
        <strong><?php echo $password_message; ?></strong>
        <input type="submit" value="Submit" name="update_password" class="alt-btn-info settings_submit"><br>
    </form>

    <h4 class="mt-3">Close Account</h4>
    <form action="settings.php" method="POST">
        <input type="submit" name="close_account" id="close_account" value="Close Account" class="alt-btn-danger settings_submit">
    </form>

<!-- closing wrapper div -->
</div>
</body>
</html>