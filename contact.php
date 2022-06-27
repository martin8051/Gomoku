<?php 
  session_start(); 

  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: login.php");
  }
  
?>


<!DOCTYPE html>
    <header> 
        <h2>Gomoku </h2> 
        <link rel="stylesheet" href="css/style.css">
        <nav id="navigation">
            <div class="menu">
                <ul>
                    <li><a href="homepage.php">HomePage</a></li>
                    <li><a href="help.php">Help</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="leaderboard.php">Leaderboard</a></li>
                    <li><a href="contact.php?logout='1'">Sign-Out</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <body>
    <p>

        <div class="contactbox2">
            <h3>Contact Page <br><hr><br>
            Site Creators:</h3> <br>
        <strong>Pete Solis</strong>: A Computer Science Senior studying at Fresno State. <br> 
            Likes: Reading and baseball <br>
            <a href="mailto:psoliss@mail.fresnostate.edu"> Pete's email</a><br><br>
        <strong>Martin Pantoja-Saldana</strong>: A Computer Science Senior studying at Fresno State. <br> 
            Likes: Fishing and playing golf. <br>
            <a href="mailto:martin8051@mail.fresnostate.edu"> Martin's email</a><br>
            
        
        </div>
        
        </p>    

    
    </body>

    <footer>
        <h4>Don't Hesitate to Reach out</h4>
    
    </html>