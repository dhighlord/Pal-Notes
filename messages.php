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

if(array_key_exists('msgSend', $_POST)){
	$to = $_POST['to'];
	$text = $_POST['content'];
	
	$msgid = $mysqli->query("SELECT id FROM friends WHERE (frnd1 = '$myid' and frnd2='$to') OR (frnd2 = '$myid' and frnd1='$to')");
	$msgid = $msgid->fetch_assoc();
	$msgid = $msgid['id'];
	
	if ( $mysqli->query("INSERT INTO messages (msgid, mfrom, mto, text) VALUES ('$msgid','$myid', '$to', '$text')") ){
		header("Refresh:0");
    }
	}
	
	if(array_key_exists('fdltmsg', $_POST)){
					$did = $_POST['rid'];
				if ( $mysqli->query("UPDATE messages SET fdelete = 1 where id='$did'") ) {
					header("Refresh:0");
				}
			}
	if(array_key_exists('tdltmsg', $_POST)){
					$did = $_POST['rid'];
				if ( $mysqli->query("UPDATE messages SET tdelete = 1 where id='$did'") ) {
					header("Refresh:0");
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
		  <a href="news.php"><button class="button"/>Home</button></a> | <a href="profile.php"><button class="button"/>Profile</button></a> | <button class="button"/>Messages</button> | 
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
		  
		  <hr/>
		  
		 <div class="form">
		 
		  <?php
		  
if(isset($_GET['fid']))
{
	
    $fid = $mysqli->escape_string($_GET['fid']); 
	
    $result = $mysqli->query("SELECT id, first_name, dp FROM users WHERE id = '$fid'");
	$row = $result->fetch_assoc();
	$to = $row['id'];
	$fn = $row['first_name'];
	$fdp = $row['dp'];
	
	echo '<h2><a href="pview.php?id='.$fid.'">'.$fn.'</a></h2>';
	
	$result = $mysqli->query("SELECT * from messages where msgid = (SELECT id FROM friends WHERE (frnd1 = '$myid' and frnd2='$fid') OR (frnd2 = '$myid' and frnd1='$fid'))");
	
	
    if ( $result->num_rows > 0 ){
		echo '<div class="scroll"><table style="width:100%">';
		while($row = $result->fetch_assoc()){
			$rid = $row["id"];
		echo '<tr>';
		if($row['mfrom']==$myid && $row['fdelete']==0){
		echo '<td style="width:6%"><div class="sdp" style="background-image:url('.$mydp.')"></div></td>
			<td style="width:84%">'.$row['text'].'</td>
			<td style="width:10%">
			<form method="post" autocomplete="off">
			  <input type="hidden" name="rid" value="'.$rid.'">
			  <button class="button" name="fdltmsg" />Delete</button>	
	        </form>';
		}
		if($row['mto']==$myid && $row['tdelete']==0){
		echo '<td style="width:6%"><div class="sdp" style="background-image:url('.$fdp.')"></div></td>
			<td style="width:84%">'.$row['text'].'</td>
			<td style="width:10%">
			<form method="post" autocomplete="off">
			  <input type="hidden" name="rid" value="'.$rid.'">
			  <button class="button" name="tdltmsg" />Delete</button>	
	        </form>';
		}
		echo '</tr>';
		}
		echo '</table></div>';
    }
	
	else{
		echo '<div class="scroll"><table border = 0>';
		echo '<tr>';
			echo '<td><p>No Messages!</p></td>';
		echo '</tr>';
		echo '</table></div>';
	}
	echo '
	<form method="post" autocomplete="off">
			<textarea name="content"></textarea>
			<input type="hidden" name="to" value="'.$to.'">
			<button class="button" name="msgSend" />Send</button>	
	</form>';
		  
}else{
	
	$result = $mysqli->query("SELECT * FROM users WHERE id IN (
SELECT id FROM 
(SELECT frnd1 as id from (SELECT frnd1, frnd2 from friends where id IN (SELECT msgid FROM messages)) t1
where t1.frnd2 = '$myid') v1
UNION
(SELECT frnd2 as id from (SELECT frnd1, frnd2 from friends where id IN (SELECT msgid FROM messages)) t2
where t2.frnd1 = '$myid'))");
	
	echo "
	<h2>Conversations</h2><hr/>
	<table border = 0>";
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()){
			echo "<tr>";
				echo '<td><div class="sdp" style="background-image:url('.$row['dp'].')"></div></td>
				<td><h2><a href="messages.php?fid='.$row['id'].'">'.$row['first_name'].' '.$row['last_name'].'</a></h2></td>';
			echo "</tr>";
			}
		echo "</table>";
	} else {
		echo "<p>No Messages!</p>";
	}
}

?>
	
		  
		 </div>
		
		<hr/>
          
		<a href="logout.php"><button class="button"/>Logout</button></a>

    </div>
    
<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src="js/index.js"></script>

</body>
</html>
