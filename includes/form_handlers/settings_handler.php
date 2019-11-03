<?php 
if (isset($_POST['update_details'])) {
    $first_name = strip_tags($_POST['first_name']);
    $last_name = strip_tags($_POST['last_name']);
    $email = strip_tags($_POST['email']);

    $email_check = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
    $row = mysqli_fetch_array($email_check);
    $matched_user = $row['username'];

    if ($matched_user == "" || $matched_user == $userLoggedIn) {
        $message = "Details updated!<br><br>";
        $query = mysqli_query($con, "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email' WHERE username='$userLoggedIn'");
    } else {
        $message = "Sorry, that email is already in use<br><br>";
    }
} else {
    $message = "";
}

if (isset($_POST['update_password'])) {
    $current_password = strip_tags($_POST['current_password']);
    $new_password_1 = strip_tags($_POST['new_password_1']);
    $new_password_2 = strip_tags($_POST['new_password_2']);
    
    $password_query = mysqli_query($con, "SELECT password FROM users WHERE username='$userLoggedIn'");
    $row = mysqli_fetch_array($password_query);
    $db_password = $row['password'];

    if(password_verify($current_password, $db_password)) {
        if ($new_password_1 == $new_password_2) {
            if (strlen($new_password_1) <= 4) {
                $password_message = "Your new password must be at least 5 characters long<br><br>";
            } else {
                $new_password_hashed = password_hash($new_password_1, PASSWORD_DEFAULT);
                $password_query = mysqli_query($con, "UPDATE users SET password='$new_password_hashed' WHERE username='$userLoggedIn'");
                $password_message = "Password has been changed<br><br>";
            }
        } else {
            $password_message = "Passwords do not match<br><br>";
        }
    } else {
        $password_message = "The value you entered for 'current password' is incorrect<br><br>";
    }
} else {
    $password_message = "";
}
?>