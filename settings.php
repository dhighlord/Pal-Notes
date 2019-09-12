<?php
require 'db.php';
session_start();

if ( $_SESSION['logged_in'] != 1 ) {
  $_SESSION['message'] = "You must log in before viewing your profile page!";
  header("location: error.php");    
}
else {
	$myid = $_SESSION['id'];
	$mydp = $_SESSION['dp'];
    $first_name = $_SESSION['first_name'];
    $last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
}

$target_dir = "dp/";
if(isset($_POST["submit"])) {
	$target_file = $target_dir . $myid . basename($_FILES["userfile"]["name"]);
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    if(!empty($_FILES["userfile"]["tmp_name"])) {
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
		$_SESSION['message'] = "Please select a JPG, JPEG or PNG file!";
		}
		else if((move_uploaded_file($_FILES["userfile"]["tmp_name"], $target_file)) && ($mysqli->query("UPDATE users SET dp = '$target_file' where id='$myid'"))) {
		$_SESSION['dp'] = $target_file;
		$mydp = $target_file;
        $_SESSION['message'] = "Profile picture updated!";
    }
}
}
?>
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Pal Notes | Settings</title>
  <?php include 'css/css.html'; ?>
</head>

<body>
  <div class="form">

         <table style="width:100%"><td><h1>Pal Notes</h1></td>
		  <td><h2 align="right">
		  <button class="button"/>Home</button> | <a href="profile.php"><button class="button"/>Profile</button></a> | <button class="button"/>Messages</button> | 
		  <?php $result = $mysqli->query("SELECT rfrom FROM requests WHERE rto = '$myid'"); ?>
		  <a href="requests.php"><button class="button"/>Requests(<?php echo $result->num_rows; ?>)</button></a> | 
			
		  <a href="logout.php"><button class="button"/>Logout</button></a>
		  </h2></td>
		  </table>
		  
		  <table style="width:100%"><td>
		  <div class="dp" style="background-image:url('<?= $mydp ?>')"></div>
		  </td><td>
		  <h2 align="right"><?php echo $first_name.' '.$last_name; ?></h2>
		  <h3 align="right"><?= $email ?><br><a href="settings.php"><img src="img/gear.png" alt="settings" height="30" width="30"/></a></h3></td></table>
		  
		  <div class="form">
		  <form action="Search.php" method="post" autocomplete="off">
            <div class="field-wrap">
				<label>Search </label>
				<input type="text" required autocomplete="off" name="search"/>
			</div>
		    </form>
			</div>
			<p>
          <?php 
     
          if ( isset($_SESSION['message']) )
          {
              echo $_SESSION['message'];
              
              unset( $_SESSION['message'] );
          }
          
          ?>
          </p>
          
          <?php
          
          if ( !$active ){
              echo
              '<div class="info">
              Account is unverified, please confirm your email by clicking
              on the email link!
              </div>';
          }
          
          ?>
		  
		  <div class="form">
			<form method="post" enctype="multipart/form-data">
    <p>Select image to upload:</p>
    <input type="file" name="userfile" id="userfile">
    <input type="submit" value="Upload" name="submit">
			
			</form>
		  </div>
          
		<a href="logout.php"><button class="button"/>Logout</button></a>

    </div>
    
<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src="js/index.js"></script>

</body>
</html>
