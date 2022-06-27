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
                    <li><a href="homepage.php?logout='1'">Sign-Out</a></li>
                </ul>
            </div>
        </nav>
    </header>


<div class="center">
<body>
    <?php  if (isset($_SESSION['username'])) : ?>
    	<h1>Welcome <strong><?php echo $_SESSION['username']; ?></strong></h1>
    <?php endif ?>
        <br>
        <br>
    <button onclick="location.href = 'game.php'">Play Game</button>
</body>
</div>

<footer>
    <h4>Created by Pete Solis and Martin Pantoja</h4>
</footer>

</html>