<?php

require 'database.php';

session_start();

$username = "";
$errors = array(); 

if (isset($_POST['register'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $password_1 = mysqli_real_escape_string($conn, $_POST['password']);
  $password_2 = mysqli_real_escape_string($conn, $_POST['confirmPassword']);

  // form validation: ensure that the form is correctly filled
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
	array_push($errors, "The two passwords do not match");
  }
  
  // first check the database to make sure  a user does not already exist with the same username
  $user_check_query = "SELECT * FROM gomokutable WHERE username= '$username' LIMIT 1";
  $result = mysqli_query($conn, $user_check_query);
  $user = mysqli_fetch_assoc($result);

  {
  if ($user) { // if user exists
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }
  }
    }

  
  if (count($errors) == 0) { // If no errors register user
  	$password = md5($password_1);//encrypt the password before saving in the database

  	$query = "INSERT INTO gomokutable (username, pass) 
  			  VALUES('$username', '$password')";
  	mysqli_query($conn, $query);
 
     
  	header('location: ../login.php');
  } else
  {
    print_r($errors);
    echo "<br>";
    echo '<button onclick="history.go(-1);">go back and try again </button>';
  }
}
?>