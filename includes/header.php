<?php
require 'config/config.php';
include("includes/classes/User.php");
include("includes/classes/Post.php");
include("includes/classes/Message.php");
include("includes/classes/Notification.php");
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
    <script src="vendors/js/bootbox.min.js"></script>
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
        <div class="search">
            <form action="search.php" method="GET" name="search_form">
                <input 
                    type="text" 
                    onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" 
                    name="q" 
                    placeholder="Search..." 
                    autocomplete="off"
                    id="search_text_input"
                >
                <div class="button_holder">
                    <img src="assets/images/icons/magnifying-glass.png" alt="Magnifying glass icon">
                </div>
            </form>
            <div class="search_results"></div>
            <div class="search_results_footer_empty"></div>
        </div>
        <nav>
            <?php 
            $messages = new Message($con, $userLoggedIn);
            $num_messages = $messages->getUnreadNumber();

            $notifications = new Notification($con, $userLoggedIn);
            $num_notifications = $notifications->getUnreadNumber();

            $user_obj = new User($con, $userLoggedIn);
            $num_requests = $user_obj->getNumberOfFriendRequests();
            ?>
            <a href="<?php echo $userLoggedIn; ?>" data-toggle="tooltip" data-placement="bottom" title="My Profile">
                <?php echo $user['first_name']; ?>
            </a>
            <a href="index.php" data-toggle="tooltip" data-placement="bottom" title="Home">
                <i class="fas fa-home fa-lg"></i>
            </a>
            <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">
                <i class="far fa-envelope fa-lg" id="messages_icon"></i>
                <?php if ($num_messages > 0) echo '<span class="notification_badge" id="unread_message">' . $num_messages . '</span>'; ?>
            </a>
            <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
                <i class="far fa-bell fa-lg" id="notifications_icon"></i>
                <?php if ($num_notifications > 0) echo '<span class="notification_badge" id="unread_notification">' . $num_notifications . '</span>'; ?>
            </a> 
            <a href="requests.php" data-toggle="tooltip" data-placement="bottom" title="Friends">
                <i class="fas fa-users fa-lg"></i>
                <?php if($num_requests > 0) echo '<span class="notification_badge" id="unread_requests">' . $num_requests . '</span>'; ?>
            </a>   
            <a href="settings.php" data-toggle="tooltip" data-placement="bottom" title="Settings">
                <i class="fas fa-cog fa-lg"></i>
            </a>
            <a href="includes/handlers/logout.php" data-toggle="tooltip" data-placement="bottom" title="Logout">
                <i class="fas fa-sign-out-alt fa-lg"></i>
            </a>
        </nav>
        <div class="dropdown_data_window" style="height: 0px; border: none;"></div>
        <input type="hidden" id="dropdown_data_type" value="">
    </div>

    <script>
	$(() =>{
 
	    let userLoggedIn = '<?php echo $userLoggedIn; ?>';
	    let dropdownInProgress = false;
 
	    const loadPosts = () => {
	        if(dropdownInProgress) { 
				return;
			}
			
			dropdownInProgress = true;
 
		    let page = $('.dropdown_data_window').find('.nextPageDropdownData').val() || 1; 
 
		    let pageName; 
		    let type = $('#dropdown_data_type').val();
 
			if(type == 'notification')
				pageName = "ajax_load_notifications.php";
			else if(type == 'message')
				pageName = "ajax_load_messages.php";
 
			$.ajax({
				url: "includes/handlers/" + pageName,
				type: "POST",
				data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
				cache:false,
 
				success: function(response) {
 
					$('.dropdown_data_window').find('.nextPageDropdownData').remove(); 
					$('.dropdown_data_window').find('.noMoreDropdownData').remove();
 
					$('.dropdown_data_window').append(response);
 
					dropdownInProgress = false;
				}
			});
	    }
 
	    const isElementInView = el => {
	        let rect = el.getBoundingClientRect();
 
	        return (
	            rect.top >= 0 &&
	            rect.left >= 0 &&
	            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
	            rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
	        );
        }
        
        $(".dropdown_data_window").scroll(() => {
	        let bottomElement = $(".dropdown_data_window a").last();
		    let noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();
 
	        if (isElementInView(bottomElement[0]) && noMoreData == 'false') {
	            loadPosts();
	        }
	    });
	});
    </script>

    <div class="wrapper">
