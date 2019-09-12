<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'projectdb';
$mysqli = new mysqli($host,$user,$pass,$db) or die($mysqli->error);

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }