<?php 
require 'config/config.php';
include("includes/classes/User.php");
include("includes/classes/Post.php");
if (isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);
} else {
    header("Location: register.php");
}

$profile_id = $user['username'];
$imgSrc = "";
$result_path = "";
$msg = "";

/***********************************************************
	0 - Remove The Temp image if it exists
***********************************************************/
	if (!isset($_POST['x']) && !isset($_FILES['image']['name']) ){
		//Delete users' default image
			$temppath = 'assets/images/profile_pics/'.$profile_id.'_temp.jpeg';
			if (file_exists ($temppath)){ @unlink($temppath); }
		} 


if(isset($_FILES['image']['name'])){	
/***********************************************************
	1 - Upload Original Image To Server
***********************************************************/	
	//Get Name | Size | Temp Location		    
		$ImageName = $_FILES['image']['name'];
		$ImageSize = $_FILES['image']['size'];
		$ImageTempName = $_FILES['image']['tmp_name'];
	//Get File Ext   
		$ImageType = @explode('/', $_FILES['image']['type']);
		$type = $ImageType[1]; //file type	
	//Set Upload directory    
		$uploaddir = $_SERVER['DOCUMENT_ROOT'].'/SocialNetwork/assets/images/profile_pics';
	//Set File name	
		$file_temp_name = $profile_id.'_original.'.md5(time()).'n'.$type; //the temp file name
		$fullpath = $uploaddir."/".$file_temp_name; // the temp file path
		$file_name = $profile_id.'_temp.jpeg'; //$profile_id.'_temp.'.$type; // for the final resized image
		$fullpath_2 = $uploaddir."/".$file_name; //for the final resized image
	//Move the file to correct location
		$move = move_uploaded_file($ImageTempName ,$fullpath) ; 
		chmod($fullpath, 0777);  
		//Check for valid uplaod
		if (!$move) { 
			die ('File didnt upload');
		} else { 
			$imgSrc= "assets/images/profile_pics/".$file_name; // the image to display in crop area
			$msg= "Upload Complete!";  	//message to page
			$src = $file_name;	 		//the file name to post from cropping form to the resize		
		} 

/***********************************************************
	2  - Resize The Image To Fit In Cropping Area
***********************************************************/		
		//get the uploaded image size	
			clearstatcache();				
			$original_size = getimagesize($fullpath);
			$original_width = $original_size[0];
			$original_height = $original_size[1];	
		// Specify The new size
			$main_width = 500; // set the width of the image
			$main_height = $original_height / ($original_width / $main_width);	// this sets the height in ratio									
		//create new image using correct php func			
			if($_FILES["image"]["type"] == "image/gif"){
				$src2 = imagecreatefromgif($fullpath);
			}elseif($_FILES["image"]["type"] == "image/jpeg" || $_FILES["image"]["type"] == "image/pjpeg"){
				$src2 = imagecreatefromjpeg($fullpath);
			}elseif($_FILES["image"]["type"] == "image/png"){ 
				$src2 = imagecreatefrompng($fullpath);
			}else{ 
				$msg .= "There was an error uploading the file. Please upload a .jpg, .gif or .png file. <br />";
			}
		//create the new resized image
			$main = imagecreatetruecolor($main_width,$main_height);
			imagecopyresampled($main,$src2,0, 0, 0, 0,$main_width,$main_height,$original_width,$original_height);
		//upload new version
			$main_temp = $fullpath_2;
			imagejpeg($main, $main_temp, 90);
			chmod($main_temp,0777);
		//free up memory
			imagedestroy($src2);
			imagedestroy($main);
			//imagedestroy($fullpath);
			@ unlink($fullpath); // delete the original upload					
									
}//ADD Image 	

/***********************************************************
	3- Cropping & Converting The Image To Jpg
***********************************************************/
if (isset($_POST['x'])){
	
	//the file type posted
		$type = $_POST['type'];	
	//the image src
		$src = 'assets/images/profile_pics/'.$_POST['src'];	
		$finalname = $profile_id.md5(time());	
	
	if($type == 'jpg' || $type == 'jpeg' || $type == 'JPG' || $type == 'JPEG'){	
	
		//the target dimensions 150x150
			$targ_w = $targ_h = 150;
		//quality of the output
			$jpeg_quality = 90;
		//create a cropped copy of the image
			$img_r = imagecreatefromjpeg($src);
			$dst_r = imagecreatetruecolor( $targ_w, $targ_h );
			imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
			$targ_w,$targ_h,$_POST['w'],$_POST['h']);
		//save the new cropped version
			imagejpeg($dst_r, "assets/images/profile_pics/".$finalname."n.jpeg", 90); 	
			 		
	}else if($type == 'png' || $type == 'PNG'){
		
		//the target dimensions 150x150
			$targ_w = $targ_h = 150;
		//quality of the output
			$jpeg_quality = 90;
		//create a cropped copy of the image
			$img_r = imagecreatefrompng($src);
			$dst_r = imagecreatetruecolor( $targ_w, $targ_h );		
			imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
			$targ_w,$targ_h,$_POST['w'],$_POST['h']);
		//save the new cropped version
			imagejpeg($dst_r, "assets/images/profile_pics/".$finalname."n.jpeg", 90); 	
						
	}else if($type == 'gif' || $type == 'GIF'){
		
		//the target dimensions 150x150
			$targ_w = $targ_h = 150;
		//quality of the output
			$jpeg_quality = 90;
		//create a cropped copy of the image
			$img_r = imagecreatefromgif($src);
			$dst_r = imagecreatetruecolor( $targ_w, $targ_h );		
			imagecopyresampled($dst_r,$img_r,0,0,$_POST['x'],$_POST['y'],
			$targ_w,$targ_h,$_POST['w'],$_POST['h']);
		//save the new cropped version
			imagejpeg($dst_r, "assets/images/profile_pics/".$finalname."n.jpeg", 90); 	
		
	}
		//free up memory
			imagedestroy($img_r); // free up memory
			imagedestroy($dst_r); //free up memory
			@ unlink($src); // delete the original upload					
		
		//return cropped image to page	
		$result_path ="assets/images/profile_pics/".$finalname."n.jpeg";

		//Insert image into database
		$insert_pic_query = mysqli_query($con, "UPDATE users SET profile_pic='$result_path' WHERE username='$userLoggedIn'");
		header("Location: ".$userLoggedIn);
														
}// post x
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" integrity="sha256-+N4/V/SbAFiW1MPBCXnfnP9QSN3+Keu+NlB+0ev/YKQ=" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
	<link rel="stylesheet" href="vendors/css/jquery.Jcrop.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/ico" href="favicon.ico">
    <title>Squirrel Feed | Upload Picture</title>
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
            <a href="#" data-toggle="tooltip" data-placement="bottom" title="Messages">
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
    </div>

	<div class="wrapper">
		<div id="Overlay"></div>
		<div class="main_column column">
			<div id="formExample">
				<form action="upload.php" method="POST" enctype="multipart/form-data">
					<p class="mb-3 h5">Upload Picture</p>
					<div id="profile_picture_upload" class="file has-name is-info is-fullwidth">
						<label class="file-label">
							<input type="file" name="image" id="image" class="file-input">
							<span class="file-cta">
								<span class="file-icon">
									<i class="fas fa-upload"></i>
								</span>
								<span class="file-label">
									Choose a file...
								</span>
							</span>
							<span class="file-name">
								No file chosen
							</span>
						</label>
					</div>
					<input type="submit" value="Submit" class="button is-primary">
					<p><b><?=$msg?></b></p>
				</form><br /><br />
				
			</div>

			<?php
			if($imgSrc){ //if an image has been uploaded display cropping area?>
				<script>
					$('#Overlay').show();
					$('#formExample').hide();
				</script>
				<div id="CroppingContainer">  
					<div id="CroppingArea">	
						<img src="<?=$imgSrc?>" border="0" id="jcrop_target">
					</div>  
					<div id="InfoArea">	
						<p>          
							<b>Crop Profile Image</b>
							<br>
							<br>
							<span>
								Crop / resize your uploaded profile image.
								<br>
								Once you are happy with your profile image, please click save.
							</span>
						</p>
					</div>  
					<br>
					<div id="CropImageForm">  
						<form action="upload.php" method="post" onsubmit="return checkCoords();">
							<input type="hidden" id="x" name="x" />
							<input type="hidden" id="y" name="y" />
							<input type="hidden" id="w" name="w" />
							<input type="hidden" id="h" name="h" />
							<input type="hidden" value="jpeg" name="type" /> <?php // $type ?> 
							<input type="hidden" value="<?=$src?>" name="src" />
							<input type="submit" value="Save" class="button is-success w-100">
						</form>
					</div>
					<div id="CropImageForm2">  
						<form action="upload.php" method="post" onsubmit="return cancelCrop();">
							<input type="submit" value="Cancel" class="button is-danger w-100 mt-2">
						</form>
					</div>            
				</div>
			<?php 
			} ?>
		</div>
		<?php if($result_path) { ?>
			<img src="<?=$result_path?>" style="position:relative; margin:10px auto; width:150px; height:150px;" />
		<?php } ?>
		<br /><br />
	<!-- wrapper closing div -->
	</div>
	<?php include("includes/scripts.php"); ?>
	<script>
		const fileInput = document.querySelector('#profile_picture_upload input[type=file]');
		fileInput.onchange = () => {
			if (fileInput.files.length > 0) {
				const fileName = document.querySelector('#profile_picture_upload .file-name');
				fileName.textContent = fileInput.files[0].name;
			}
		}
	</script>
</body>
</html>