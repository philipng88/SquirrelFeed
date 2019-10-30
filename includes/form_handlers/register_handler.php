<?php
$fname = ""; // First name
$lname = ""; // Last name
$em = ""; // Email
$em2 = ""; // Email confirm
$password = ""; // Password
$password2 = ""; // Password confirm
$date = ""; // Sign up date
$error_array = array(); // Hold error messages

if (isset($_POST['register_button'])) {
    // First name
    $fname = strip_tags($_POST['reg_fname']);
    $fname = str_replace(' ', '', $fname);
    $fname = ucfirst(strtolower($fname));
    $_SESSION['reg_fname'] = $fname;

    // Last name
    $lname = strip_tags($_POST['reg_lname']);
    $lname = str_replace(' ', '', $lname);
    $lname = ucfirst(strtolower($lname));
    $_SESSION['reg_lname'] = $lname;

    // Email
    $em = strip_tags($_POST['reg_email']);
    $em = str_replace(' ', '', $em);
    $em = ucfirst(strtolower($em));
    $_SESSION['reg_email'] = $em;

    // Email confirmation
    $em2 = strip_tags($_POST['reg_email2']);
    $em2 = str_replace(' ', '', $em2);
    $em2 = ucfirst(strtolower($em2));
    $_SESSION['reg_email2'] = $em2;

    // Password
    $password = strip_tags($_POST['reg_password']); 
    $password2 = strip_tags($_POST['reg_password2']);

    $date = date("Y-m-d"); // Current date

    if ($em == $em2) {
        if (filter_var($em, FILTER_VALIDATE_EMAIL)) {
            $em = filter_var($em, FILTER_VALIDATE_EMAIL);
            $e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");
            $num_rows = mysqli_num_rows($e_check);
            if ($num_rows > 0) {
                array_push($error_array, "Email already in use<br>");
            }
        } else {
            array_push($error_array, "Invalid email format<br>");
        }

    } else {
        array_push($error_array, "Emails do not match<br>");
    }

    if (strlen($fname) > 25 || strlen($fname) < 2) {
        array_push($error_array, "First name must be between 2 and 25 characters<br>");
    }

    if (strlen($lname) > 25 || strlen($lname) < 2) {
        array_push($error_array, "Last name must be between 2 and 25 characters<br>");
    }

    if ($password != $password2) {
        array_push($error_array, "Passwords do not match<br>");
    } else {
        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            array_push($error_array, "Password must be alphanumeric (i.e., contain only letters and numbers)<br>");
        }
    }

    if (strlen($password) < 5) {
        array_push($error_array, "Password must be at least 5 characters<br>");
    }

    if (empty($error_array)) {
        // $password = md5($password);
        $password = password_hash($password, PASSWORD_DEFAULT);
        $username = strtolower($fname . "_" .$lname);
        $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
        
        $i = 0;
        while(mysqli_num_rows($check_username_query) != 0) {
            $i++;
            $username = $username . "_" . $i;
            $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
        }

        $profile_pic = "assets/images/profile_pics/defaults/default.jpg";
        $query = mysqli_query($con, "INSERT INTO users VALUES ('', '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");
        array_push($error_array, "<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>");

        // Clear session variables
        $_SESSION['reg_fname'] = "";
        $_SESSION['reg_lname'] = "";
        $_SESSION['reg_email'] = "";
        $_SESSION['reg_email2'] = "";
    }
}
?>