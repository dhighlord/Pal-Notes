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

if(array_key_exists('comment', $_POST)){
	$pid = $_POST['pid'];
	$ctext = $_POST['content']; 
	
		$mysqli->query("INSERT INTO comments (post, writer, text) VALUES ('$pid', '$myid', '$ctext')");
		header("Refresh:0");
}

if(array_key_exists('dltpost', $_POST)){
	$pid = $_POST['pid'];
	$mysqli->query("DELETE FROM posts WHERE id = '$pid'");
	header("Refresh:0");
}

if(array_key_exists('dltcmnt', $_POST)){
	$cid = $_POST['cid'];
	$mysqli->query("DELETE FROM comments WHERE id = '$cid'");
	header("Refresh:0");
}


?>
<!DOCTYPE html>
<html >
<head>
  <meta charset="UTF-8">
  <title>Pal Notes | Newsfeed</title>
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
			
			<p>
          <?php 
     
          if ( isset($_SESSION['message']) )
          {
              echo $_SESSION['message'];
              
              unset( $_SESSION['message'] );
          }
          
          ?>
          </p><hr/>
		 
		  <?php
		  
		if(isset($_GET['fid'])){
		
		$fid = $mysqli->escape_string($_GET['fid']); 
		
		$sql = "SELECT * FROM `posts` WHERE id='$fid'";
		  
		  $result = $mysqli->query($sql);
		  
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
			echo '</td></tr><tr><td>
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
			echo '</td><td></td></tr>';
			echo '<tr>
			<td></td>
			<td><form method="post">
			<input type="text" required autocomplete="off" name="content"/>
			<input type="hidden" name="pid" value="'.$pid.'">
			</td>
			<td><button name="comment">Comment</button></form></td>
			</tr>';
			$qr = $mysqli->query("SELECT * FROM `comments` WHERE post='$fid'");
			if($qr->num_rows > 0){
			while($qrow = $qr->fetch_assoc()){
				$cid = $qrow['id'];
				$cwriter = $qrow['writer'];
				$cw = $cwriter;
				$cwtxt = $qrow['text'];
				$cwriter = $mysqli->query("select * from users where id = '$cwriter'");
				$cwriter = $cwriter->fetch_assoc();
				$cwfn = $cwriter['first_name'];
				$cwln = $cwriter['last_name'];
				$cwdp = $cwriter['dp'];
		
				echo '<tr>
				<td><div class="sdp" style="background-image:url('.$cwdp.')"></div></td><td><h2>'.$cwfn.' '.$cwln.'</h2><br><h3>'.$cwtxt.'</h3></td>
				<td>';
				if($cw==$myid){
					echo '<form method="post">
					<input type="hidden" name="cid" value="'.$cid.'">
					<button name="dltcmnt">Delete</button></form>';
				}
				echo '</td></tr>';
			}
			}
			echo '</table>';
			}
		  }
		
	}else{
		header("location: news.php");
	}

	?>
		

    </div>
		
    
<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src="js/index.js"></script>

</body>
</html>
