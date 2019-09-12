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
	
	$pv_id = $_GET['id'];
	$sql = "select * from users where '$pv_id' = id";
	$result = $mysqli->query($sql);
	$row = $result->fetch_assoc();
	$pv_fn = $row['first_name'];
	$pv_ln = $row['last_name'];
	$pv_dp = $row['dp'];
	
	$frnds = 0;
	
}
			if(array_key_exists('acceptreq', $_POST)){
				if ( $mysqli->query("INSERT INTO friends (frnd1, frnd2) VALUES('$pv_id', '$myid')") AND $mysqli->query("DELETE FROM requests where rfrom='$pv_id' AND rto='$myid'") ) {
				
				} else {
					$_SESSION['message'] = "Request Failed!";
				}
			}
			if(array_key_exists('sendreq', $_POST)){
				$sql = "INSERT INTO requests (rfrom, rto)".
				"VALUES ('$myid', '$pv_id')";
				if ( $mysqli->query($sql) ) {
				
				} else {
					$_SESSION['message'] = "Request Failed!";
				}
			}	
			if(array_key_exists('cancelreq', $_POST)){
				$sql = "DELETE FROM requests where rfrom='$myid' AND rto='$pv_id'";
				if ( $mysqli->query($sql) ) {
				
				} else {
					$_SESSION['message'] = "Request Failed!";
				}
			}
			if(array_key_exists('unfrnd', $_POST)){
				$frnds = 0;
				if ( $mysqli->query("DELETE FROM friends where (frnd1='$pv_id' AND frnd2='$myid') OR (frnd1='$myid' AND frnd2='$pv_id')") ) {
					header("Refresh:0");
				}
			}
			if(array_key_exists('msg', $_POST)){
					header("location: messages.php?fid=$pv_id");
			}
			if(array_key_exists('declinereq', $_POST)){
					$rid = $row['id'];
				if ( $mysqli->query("DELETE FROM requests where rfrom='$pv_id' AND rto='$myid'") ) {
					header("Refresh:0");
				}
			}				
?>

	
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Pal Notes | <?= $pv_fn.' '.$pv_ln ?></title>
  <?php include 'css/css.html'; ?>
</head>
<body>

  <div class="form">

          <table style="width:100%"><td><h1 align="left">Pal Notes</h1></td>
		  <td><h2 align="right">
		  <a href="news.php"><button class="button"/>Home</button></a> | <a href="profile.php"><button class="button"/>Profile</button></a> | <button class="button"/>Messages</button> | 
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
		  
          
          <table style="width:100%"><td>
		  <div class="dp" style="background-image:url('<?= $pv_dp ?>')"></div>
		  </td><td>
		  <h2 align="right"><?php echo $pv_fn.' '.$pv_ln ?></h2>
		  </td></table>
          
		  
		<?php
		  
		echo '<p>';
     
          if ( isset($_SESSION['message']) )
          {
              echo $_SESSION['message'];
              
              unset( $_SESSION['message'] );
          }
          
		echo '</p>';
		  
		  $result = $mysqli->query("select * from requests where '$myid' = rto and '$pv_id' = rfrom");
		  
		  if($result->num_rows > 0){
			echo '<form method="post"><table "width:100%"><td>
				<h2><input type="submit" name="acceptreq" id="acceptreq" value="Accept Friend Request" /></h2></td>
				<td>
				<h2><input type="submit" name="declinereq" id="declinereq" value="Decline Friend Request" /></h2></td>
			</table></form>';  
		  }
		  else{
			$result = $mysqli->query("select * from requests where '$pv_id' = rto and '$myid' = rfrom");
	
			if($result->num_rows > 0){
				echo '<form method="post">
				<h2><input type="submit" name="cancelreq" id="cancelreq" value="Cancel Request" /></h2>
				</form>';
			}
			else {
				$sql = "select * from friends where ('$pv_id' = frnd1 and '$myid' = frnd2) OR ('$pv_id' = frnd2 and '$myid' = frnd1)";
				$result = $mysqli->query($sql);
				if($result->num_rows > 0){
					$frnds = 1;
					echo '<form method="post">
					<h2><input type="submit" name="unfrnd" id="unfrnd" value="Unfriend" /></h2>
					<h2><input type="submit" name="msg" id="msg" value="Message" /></h2><br/>
					</form>';
				}
				else{
					echo '<form method="post">
					<h2><input type="submit" name="sendreq" id="sendreq" value="Send Friend Request" /></h2><br/>
					</form>';
				}
			}
		  }
		  
		echo '<hr/>';
		
		if($frnds==1){
			$qry = "SELECT * FROM `posts` WHERE (wall = '$pv_id') AND author IN
				(
				SELECT frnd1 as author FROM friends WHERE (frnd2='$myid')
				UNION
				SELECT frnd2 as author FROM friends WHERE (frnd1='$myid')
				)
				ORDER BY id DESC";
		}
		else{
			$qry = "select * from posts where wall = '$pv_id' and privacy = 0 ORDER BY id DESC";
		}
			
		$result = $mysqli->query($qry);
		  
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
			
			echo '<td style="width:15%">';
			if($authid==$myid){
			echo '<form method="post">
			<input type="hidden" name="pid" value="'.$pid.'">
			<button name="dltpost">Delete</button></form>';
			}
			echo '</td></tr>';
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
		  

	
		<br/>
		  
		<hr/>
		
		<a href="logout.php"><button class="button"/>Logout</button></a>

    </div>
    
<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src="js/index.js"></script>


</body>
</html>