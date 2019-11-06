<?php
require 'config/config.php';
require 'includes/form_handlers/register_handler.php';
require 'includes/form_handlers/login_handler.php';
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="assets/css/register_style.css">
    <link rel="icon" type="image/ico" href="favicon.ico">
    <title>Welcome to Squirrel Feed!</title>
</head>
<body>
    <?php 
    if (isset($_POST['register_button'])) {
        echo '
        <script>
            $(document).ready(() => {
                $("#login_form").hide();
                $("#register_form").show();
            });
        </script>
        ';
    }
    ?>
    <div class="wrapper">
        <div class="login_box">
            <div class="login_header">
                <h1>Squirrel Feed</h1>
                Login or sign up below!
            </div>
            <div id="login_form">
                <form action="register.php" method="POST">
                    <!-- <input type="email" name="log_email" placeholder="Email Address" required> -->
                    <input 
                        type="email" 
                        name="log_email" 
                        placeholder="Email Address"
                        required
                        value="<?php if (isset($_SESSION['log_email'])) { echo $_SESSION['log_email']; } ?>"
                    >
                    <br>
                    <input type="password" name="log_password" placeholder="Password">
                    <br>
                    <?php if (in_array("Email or password was incorrect<br>", $error_array)) echo "Email or password was incorrect<br>" ?>
                    <input type="submit" name="login_button" value="Login">
                    <br>
                    <a href="#" id="signup" class="signup">Need an account? Register here!</a>
                </form>
            </div>

            <div id="register_form">
                <form action="register.php" method="POST">
                    <!-- <input type="text" name="reg_fname" placeholder="First Name" required> -->
                    <input type="text" name="reg_fname" placeholder="First Name" value="<?php if (isset($_SESSION['reg_fname'])) { echo $_SESSION['reg_fname']; } ?>" required>
                    <br>
                    <?php if (in_array("First name must be between 2 and 25 characters<br>", $error_array)) echo "First name must be between 2 and 25 characters<br>"; ?>
                    
                    <!-- <input type="text" name="reg_lname" placeholder="Last Name" required> -->
                    <input type="text" name="reg_lname" placeholder="Last Name" value="<?php if (isset($_SESSION['reg_lname'])) { echo $_SESSION['reg_lname']; } ?>" required>
                    <br>
                    <?php if (in_array("Last name must be between 2 and 25 characters<br>", $error_array)) echo "Last name must be between 2 and 25 characters<br>"; ?>

                    <!-- <input type="email" name="reg_email" placeholder="Email" required> -->
                    <input type="email" name="reg_email" placeholder="Email" value="<?php if (isset($_SESSION['reg_email'])) { echo $_SESSION['reg_email']; } ?>" required>
                    <br>

                    <!-- <input type="email" name="reg_email2" placeholder="Confirm Email" required> -->
                    <input type="email" name="reg_email2" placeholder="Confirm Email" value="<?php if (isset($_SESSION['reg_email2'])) { echo $_SESSION['reg_email2']; } ?>" required>
                    <br>
                    <?php if (in_array("Email already in use<br>", $error_array)) echo "Email already in use<br>";
                    else if (in_array("Invalid email format<br>", $error_array)) echo "Invalid email format<br>";
                    else if (in_array("Emails do not match<br>", $error_array)) echo "Emails do not match<br>"; ?>
                    
                    <input type="password" name="reg_password" placeholder="Password" required>
                    <br>
                    <input type="password" name="reg_password2" placeholder="Confirm Password" required>
                    <br>
                    <?php if (in_array("Passwords do not match<br>", $error_array)) echo "Passwords do not match<br>";
                    else if (in_array("Password must be alphanumeric (i.e., contain only letters and numbers)<br>", $error_array)) echo "Password must be alphanumeric (i.e., contain only letters and numbers)<br>";
                    else if (in_array("Password must be at least 5 characters<br>", $error_array)) echo "Password must be at least 5 characters<br>"; ?>
                    
                    <input type="submit" name="register_button" value="Register">
                    <br>
                    <?php if (in_array("<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>", $error_array)) echo "<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>"; ?>
                    <a href="#" id="signin" class="signin">Already have an account? Sign in here!</a>
                </form>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <!-- <script src="assets/js/register.js"></script> -->
    <script>
        $(document).ready(() => {
            $("#signup").click(() => {
                $("#login_form").slideUp("slow", () => {
                    $("#register_form").slideDown("slow")
                })
            })

            $("#signin").click(() => {
                $("#register_form").slideUp("slow", () => {
                    $("#login_form").slideDown("slow")
                })
            })
        });
    </script>
</body>
</html>