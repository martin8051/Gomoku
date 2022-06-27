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
                    <li><a href="help.php?logout='1'">Sign-Out</a></li>
                </ul>
            </div>
        </nav>
    </header>



    <body>
        <div class="contactbox3">
        
            <h1>Rules: </h1> <h5> Gomoku Game <br></h5><hr>
                <h3>How to play</h3> <br>
                <p>
                    <strong>Game Description</strong> Players alternate turns placing a stone of their color on an empty 
                    intersection (using the mouse).<br>
                    Black plays first. The winner is the first player to form an unbroken chain of five stones horizontally, vertically, 
                    or diagonally. Placing so that a line of more than five stones of the same color is created does not result 
                    in a win. <br><br>
                    The game is played with 2 players on the same screen. We will consider that the player who is logged
                    into the game will be the first player (black). The second player (white) will be played by another person 
                    on the same machine, same screen.<br><br>
                    At the end of the game, we will only record the information from Player 1.
                    At the end of each game, the score, the duration of the game, and the number of turns will be saved in 
                    the RDBMS on the server side, so they can be displayed in the leaderboard page.<br>
                    The server side will be 
                    only used to save the results of each game, keep information about Player 1. <br><br>
                </p>    
        </div>
    </body>

    <footer>
        <h4>Created by Pete Solis and Martin Pantoja</h4>
    </footer>

</html>