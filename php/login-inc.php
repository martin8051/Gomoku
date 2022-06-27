<?php
require 'database.php';
$errors = array(); 

if (isset($_POST['userlogin'])) {
    
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
  
    if (empty($username)) {
        array_push($errors, "Username is required");
    }
    if (empty($password)) {
        array_push($errors, "Password is required");
    }
    
    if (count($errors) == 0) {
        
        $password = md5($password);
        $query = "SELECT * FROM gomokutable WHERE username='$username' AND pass='$password'";
        $results = mysqli_query($conn, $query);
        print_r($results);
        if (mysqli_num_rows($results) == 1) {
        session_start(); 
          $_SESSION['username'] = $username;
          $_SESSION['success'] = "You are now logged in";
          
          header('location: ../homepage.php');
        }else {
            array_push($errors, "Wrong username/password combination");
            print_r($errors);
            echo "<br>";
            echo '<button onclick="history.go(-1);"> Login failed, go back and try again </button>';
        }
    }
    else
    {
        print_r($errors);
        echo "<br>";
        echo '<button onclick="history.go(-1);"> Login failed, go back and try again </button>';
    }
  }
  
  ?>
