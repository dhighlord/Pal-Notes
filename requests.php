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
  <title>Pal Notes | Requests</title>
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

$sql = "SELECT * FROM users WHERE id IN (SELECT rfrom FROM requests WHERE rto = '$myid')";
$result = $mysqli->query($sql);

echo "<table border = 0 style='width:100%'>";
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()){
		echo "<tr>";
			echo '<td><div class="sdp" style="background-image:url('.$row['dp'].')"></div></td>
			<td><h2><a href="pview.php?id='.$row['id'].'">'.$row['first_name'].' '.$row['last_name'].'</a></h2></td>
			<td style="width:20%"></td>
			<td style="width:25%"><form method="post">
				<h2><input type="submit" name="acceptreq" id="acceptreq" value="Accept" /></h2></td>
				<td style="width:25%">
				<h2><input type="submit" name="declinereq" id="declinereq" value="Decline" /></h2>
				</form></td>';
				if(array_key_exists('acceptreq', $_POST)){
					$rid = $row['id'];
					
				if ( $mysqli->query("INSERT INTO friends (frnd1, frnd2) VALUES('$rid', '$myid')") AND $mysqli->query("DELETE FROM requests where rfrom='$rid' AND rto='$myid'") ) {
					header("Refresh:0");
				}
				}
				
				if(array_key_exists('declinereq', $_POST)){
					$rid = $row['id'];
				if ( $mysqli->query("DELETE FROM requests where rfrom='$rid' AND rto='$myid'") ) {
					header("Refresh:0");
				}
				}
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