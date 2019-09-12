<?php
require 'db.php';
session_start();

if ( $_SESSION['logged_in'] != 1 ) {
  $_SESSION['message'] = "You must log in before viewing your profile page!";
  header("location: error.php");    
}
else {
	$myid = $_SESSION['id'];
    $first_name = $_SESSION['first_name'];
    $last_name = $_SESSION['last_name'];
    $email = $_SESSION['email'];
    $active = $_SESSION['active'];
}
?>
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Pal Notes | Search</title>
  <?php include 'css/css.html'; ?>
</head>

<body>
<div class="form">
		<table style="width:100%"><td><h1 align="left">Pal Notes</h1></td>
		  <td><h2  align="right">
		  <button class="button"/>Home</button> | <a href="profile.php"><button class="button"/>Profile</button></a> | <button class="button"/>Messages</button> | 
		  <?php $result = $mysqli->query("SELECT rfrom FROM requests WHERE rto = '$myid'"); ?>
		  <a href="requests.php"><button class="button"/>Requests(<?php echo $result->num_rows; ?>)</button></a> | 
			
		  <a href="logout.php"><button class="button"/>Logout</button></a>
		  </h2></td>
		  </table>
		  
	<h2 align="right"><?php echo $first_name.' '.$last_name; ?></h2>
	 
	 <div class="form">
		  <form action="Search.php" method="post" autocomplete="off">
            <div class="field-wrap">
				<label>Search </label>
				<input type="text" required autocomplete="off" name="search"/>
			</div>
		    </form>
			</div>
	 
	 <div class="form">
          <hr/>
		  <div class="field-wrap">
		  <?php

$search=$_POST['search'];
$sql = "select * from users where (first_name LIKE '%$search%' OR last_name LIKE '%$search%'  OR email LIKE '%$search%') AND (email != '$email')  LIMIT 0 , 10";
$result = $mysqli->query($sql);

echo '<h2>People</h2>';
echo "<table border = 0>";
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()){
		echo "<tr>";
			echo '<td><div class="sdp" style="background-image:url('.$row['dp'].')"></div></td>
			<td><h2><a href="pview.php?id='.$row['id'].'">'.$row['first_name'].' '.$row['last_name'].'</a></h2></td>';
		echo "</tr>";
		}
    echo "</table>";
} else {
    echo "<p>0 results</p>";
}

$sql = "select * from posts where (content LIKE '%$search%') AND (author != '$myid')  LIMIT 0 , 10";
$result = $mysqli->query($sql);

echo '<hr/><h2>Posts</h2>';
echo "<table border = 0>";
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()){
		$qr = $row['author'];
		$qr = $mysqli->query("SELECT * FROM users WHERE id = '$qr'");
		$qr = $qr->fetch_assoc();
		
		echo "<tr>";
			echo '<td><div class="sdp" style="background-image:url('.$qr['dp'].')"></div></td>
			<td><h2><a href="post.php?fid='.$row['id'].'">'.$qr['first_name'].' '.$qr['last_name'].'</a></h2></td>
			<td></td>
			<td><h3>'.$row['content'].'</h3></td>';
		echo "</tr>";
		}
    echo "</table>";
} else {
    echo "<p>0 results</p>";
}


?> 
		  </div>
		  </div>
		  <br/>
		  <a href="logout.php"><button class="button"/>Logout</button></a>
</div>

 <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

    <script src="js/index.js"></script>

</body>
</html>