<?php 
include("../../config/config.php");
$table_id = $_POST['id'];
$del_comment = mysqli_query($con, "DELETE FROM comments WHERE id='$table_id'");
?>