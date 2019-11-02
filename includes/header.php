<?php
require 'config/config.php';
include("includes/classes/User.php");
include("includes/classes/Post.php");
include("includes/classes/Message.php");
if (isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);
} else {
    header("Location: register.php");
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" integrity="sha256-+N4/V/SbAFiW1MPBCXnfnP9QSN3+Keu+NlB+0ev/YKQ=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="vendors/css/jquery.Jcrop.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="vendors/js/jquery.Jcrop.min.js"></script>
    <script src="vendors/js/jcrop_bits.js"></script>
    <script src="assets/js/script.js"></script>
    <link rel="icon" type="image/ico" href="favicon.ico">
    <title>Squirrel Feed</title>
</head>
<body>
    <div class="top_bar">
        <div class="logo">
            <a href="index.php">Squirrel Feed</a>
        </div>
        <nav>
            <a href="<?php echo $userLoggedIn; ?>" data-toggle="tooltip" data-placement="bottom" title="My Profile">
                <?php echo $user['first_name']; ?>
            </a>
            <a href="index.php" data-toggle="tooltip" data-placement="bottom" title="Home">
                <i class="fas fa-home fa-lg"></i>
            </a>
            <a href="javascript:void(0)" onclick="getDropdownData(<?php echo $userLoggedIn; ?>, 'message')" data-toggle="tooltip" data-placement="bottom" title="Messages">
                <i class="fas fa-envelope fa-lg"></i>
            </a>
            <a href="#" data-toggle="tooltip" data-placement="bottom" title="Notifications">
                <i class="fas fa-bell fa-lg"></i>
            </a> 
            <a href="requests.php" data-toggle="tooltip" data-placement="bottom" title="Friends">
                <i class="fas fa-users fa-lg"></i>
            </a>   
            <a href="#" data-toggle="tooltip" data-placement="bottom" title="Settings">
                <i class="fas fa-cog fa-lg"></i>
            </a>
            <a href="includes/handlers/logout.php" data-toggle="tooltip" data-placement="bottom" title="Logout">
                <i class="fas fa-sign-out-alt fa-lg"></i>
            </a>
        </nav>
        <div class="dropdown_data_window"></div>
        <input type="hidden" id="dropdown_data_type" value="">
    </div>

    <div class="wrapper">
