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

if(array_key_exists('status', $_POST)){
	$wall = $_POST['wall'];
	$privacy = $_POST['privacy'];
	$content = $_POST['content'];
	
	if ( $mysqli->query("INSERT INTO posts (author, wall, privacy, content) VALUES ('$myid', '$wall', '$privacy', \"$content\")") ){
		$_SESSION['message'] = 'Status Updated!';
    }
    else {
        $_SESSION['message'] = 'Failed to update status!';
    }
}

if(array_key_exists('like', $_POST)){
	$pid = $_POST['pid'];
	$act = $_POST['act'];
	
	if($act=='like'){
		$mysqli->query("INSERT INTO likes (post, liker) VALUES ('$pid', '$myid')");
		header("Refresh:0");
    }
	else{
		$mysqli->query("DELETE FROM likes WHERE post = $pid and liker = $myid");
		header("Refresh:0");
	}
}

if(array_key_exists('dltpost', $_POST)){
	$pid = $_POST['pid'];
	$mysqli->query("DELETE FROM posts WHERE id = '$pid'");
	header("Refresh:0");
}

$target_dir = "photos/";

if(array_key_exists('pstatus', $_POST)){
	$wall = $_POST['wall'];
	$privacy = $_POST['privacy'];
	$content = $_POST['content'];
	
	$target_file = $target_dir . $myid . basename($_FILES["userfile"]["name"]);
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
    if(!empty($_FILES["userfile"]["tmp_name"])) {
		if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
		$_SESSION['message'] = "Please select a JPG, JPEG or PNG file!";
		}
		else if((move_uploaded_file($_FILES["userfile"]["tmp_name"], $target_file)) && 
		($mysqli->query("INSERT INTO posts (author, wall, privacy, content, photo) VALUES ('$myid', '$wall', '$privacy', \"$content\", '$target_file')"))) 
		{
        $_SESSION['message'] = 'Status Updated!';
    }
    }
    else {
        $_SESSION['message'] = 'Failed to update status!';
    }
	
}


?>
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Pal Notes | <?= $first_name.' '.$last_name ?></title>
  <?php include 'css/css.html'; ?>
</head>

<body>
  <div class="form">

         <table style="width:100%"><td><h1>Pal Notes</h1></td>
		  <td><h2 align="right">
		  <a href="news.php"><button class="button"/>Home</button></a> | <a href="profile.php"><button class="button"/>Profile</button></a> | 
		  
		  <a href="messages.php"><button class="button"/>Messages</button></a> | 
		  
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
		  
		  <hr/>
		  
		 <div class="form">
		  
		  <ul class="tab-group">
		    <li class="tab active"><a href="#status">Status</a></li>
			<li class="tab"><a href="#photo">Add Photo</a></li>
		  </ul>
			
		<div class="tab-content">	
		 
		<div id="status">
		  
		  <form method="post" autocomplete="off">
			<textarea name="content">What's on your mind?</textarea>
			<input type="hidden" name="wall" value="<?= $myid ?>">
			
			<table style="width:100%"><tr><td style="width:5%">Privacy: </td>
			<td style="width:5%"><select name="privacy">
				<option value="0">Public</option>
				<option value="1" selected>Friends</option>
			</select> </td>
			<td style="width:90%"><h2 align="right"><button  class="button" name="status"/>Post</button></h2></td>
			</tr></table>
			
		  </form>
		  
		 </div>
		  
		  <div id="photo">
		  
			<form method="post" autocomplete="off"  enctype="multipart/form-data">
			
				<h3>Select image to upload:</h3>
				<input type="file" name="userfile" id="userfile">
			
			<textarea name="content">What's on your mind?</textarea>
			<input type="hidden" name="wall" value="<?= $myid ?>">
			
			<table style="width:100%"><tr><td style="width:5%">Privacy: </td>
			<td style="width:5%"><select name="privacy">
				<option value="0">Public</option>
				<option value="1" selected>Friends</option>
			</select> </td>
			<td style="width:90%"><h2 align="right"><button  class="button" name="pstatus"/>Post</button></h2></td>
			</tr></table>
			
		  </form>
		  
		 </div>
		  
		  </div>
		  </div>
		  
		  <hr/>
		  
		  <?php
		  
		  $result = $mysqli->query("select * from posts where wall = '$myid' ORDER BY id DESC");
		  
		  echo "<table border=0 style='width:100%'>"; 
		  if($result->num_rows > 0){
			while($row = $result->fetch_assoc()){
		echo '<tr>';
			$pid = $row['id'];
			$authid = $row['author'];
			$author = $mysqli->query("select * from users where id = '$authid'");
			$author = $author->fetch_assoc();
			$authfn = $author['first_name'];
			$authln = $author['last_name'];
			$authdp = $author['dp'];
			echo '<td style="width:6%"><div class="sdp" style="background-image:url('.$authdp.')"></div></td>
			<td style="width:71%"><h2>'.$authfn.' '.$authln.'</h2></td>';
			
			$sql = $mysqli->query("SELECT * FROM likes WHERE post = '$pid'");
			
			echo '<td style="width:15%"><form method="post">
			<input type="hidden" name="pid" value="'.$pid.'">
			<button name="dltpost">Delete</button></form></td></tr>';
			echo '<tr>
			<td>
			<h3>'.$sql->num_rows.' Likes</h3>
			<form method="post">
			<input type="hidden" name="pid" value="'.$pid.'">
			<button name="like">';
			$liker = $mysqli->query("SELECT * FROM likes WHERE post = '$pid' and liker = '$myid'");
			if($liker->num_rows>0){
				echo 'Dislike';
				echo '<input type="hidden" name="act" value="dislike">';
			}
			else{
				echo 'Like';
				echo '<input type="hidden" name="act" value="like">';
			}
			echo '</button></form>
			</td>
			<td>
			<h3>'.$row['content'].'</h3>';
			if($row['photo']!=NULL){
				echo '<div class="pic" style="background-image:url('.$row['photo'].')"></div>';
			}
			echo '</td><td><a href="post.php?fid='.$pid.'"><button>Write/See Comment</button></a></td></tr>';
			}
		  }
		  else{
			  echo '<p>No posts!</p>';
		  }
		  echo "</table>";
		  
		  ?>
		
		<hr/>
		
          
		<a href="logout.php"><button class="button"/>Logout</button></a>

    </div>
    
<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src="js/index.js"></script>

</body>
</html>
