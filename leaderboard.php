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
    <style>
        table
        {
            background-color:white;
            margin-left:auto;
            margin-right:auto;
            margin-bottom:60px;
        }
        th, td {
        border: 1px solid black;
        padding: 5px;
        white-space:nowrap;
        }               

p {
    color: White;
}


   </style>
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
                    <li><a href="leaderboard.php?logout='1'">Sign-Out</a></li>
                </ul>
            </div>
        </nav>

    </header>

    <body>
    
    <form method = "post" action="leaderboard.php">
            <input type="hidden" name="choice" value= "Default">
            <input type="submit" name="Default Table" value="Default">
        </form>
        <p>Sort By: </p><br>
    <form method = "post" action="leaderboard.php">
            <input type="hidden" name="choice" value= "pWinsDesc">
            <input type="submit" name="Sort By Wins" value="Top Wins">
        </form>

        <form method = "post" action="leaderboard.php">
            <input type="hidden" name="choice" value= "pWinsAsc">
            <input type="submit" name="Sort By Wins" value="Lowest Wins">
        </form>

        <form method = "post" action="leaderboard.php">
            <input type="hidden" name="choice" value= "pTimeDesc">
            <input type="submit" name="Sort By Time" value="Highest Time">
        </form>

        <form method = "post" action="leaderboard.php">
            <input type="hidden" name="choice" value= "pTimeAsc">
            <input type="submit" name="Sort By Time" value="Lowest Time">
        </form>

        <form method = "post" action="leaderboard.php">
            <input type="hidden" name="choice" value= "pGPDesc">
            <input type="submit" name="Sort By GP" value="Most Games Played">
        </form>

        <form method = "post" action="leaderboard.php">
            <input type="hidden" name="choice" value= "pGPAsc">
            <input type="submit" name="Sort By GP" value="Least Games Played">
        </form>
        

        <form method = "post" action="leaderboard.php">
            <input type="hidden" name="choice" value= "Player">
            <input type="submit" name="Sort By Player" value="Player">
        </form>
        <br>
        <br>

    <?php

    $servername="localhost";
    $username= "AdminLab12";
    $password = "4VPnroTOC6wOU3mn";
    $dbname = "gomoku";

    $sessionName = $_SESSION['username'];

    // Create connection
    $connection = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($connection->connect_error) {
      die("Connection failed: " . $connection->connect_error);
    }


    if($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['choice'] == 'pWinsDesc'){
        $sql ="SELECT * FROM gomokutable ORDER BY pWins DESC";
    }

    else if ($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['choice'] == 'pWinsAsc'){
        $sql ="SELECT * FROM gomokutable ORDER BY pWins ASC";
    }
    else if ($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['choice'] == 'pTimeDesc'){
        $sql ="SELECT * FROM gomokutable ORDER BY pTime DESC";
    } 
    else if ($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['choice'] == 'pTimeAsc'){
        $sql ="SELECT * FROM gomokutable ORDER BY pTime ASC";
    }
    else if ($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['choice'] == 'pGPDesc'){
        $sql ="SELECT * FROM gomokutable ORDER BY pGamesPlayed DESC";
    }
    else if ($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['choice'] == 'pGPAsc'){
        $sql ="SELECT * FROM gomokutable ORDER BY pGamesPlayed ASC";
    }
    else if ($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['choice'] == 'Default'){
        $sql ="SELECT * FROM gomokutable";
    }
    else if ($_SERVER['REQUEST_METHOD'] == "POST" and $_POST['choice'] == 'Player'){
        $sql ="SELECT * FROM gomokutable WHERE username= '$sessionName'";
    }
    else{
        $sql ="SELECT * FROM gomokutable";
    }

    echo "<table class='lboard' border='1'>";
    echo "<tr>";
    echo "<td>Player Name</td>";
    echo "<td>Player Turns</td>";
    echo "<td>Player Time</td>";
    echo "<td>Player Wins</td>";
    echo "<td>Games Played</td>";
    echo "</tr>";
    if ($result = $connection->query($sql)) {
        while ($row = $result->fetch_assoc()) 
        {
            $name = $row["username"];
            $turns = $row["pTurns"];
            $time = $row["pTime"];
            $wins = $row["pWins"];
            $gp = $row["pGamesPlayed"];
            $time = convertTime2String($time); 
    
            echo '<tr> 
                        <td>'.$name.'</td> 
                        <td>'.$turns.'</td> 
                        <td>'.$time.'</td> 
                        <td>'.$wins.'</td> 
                        <td>'.$gp.'</td> 
                    </tr>';
        }
        echo "</table>";
    } else
    {
        echo "something went wrong loading database";
    }

    $connection->close();

    function convertTime2String($time) 
    { 
        $diffInHrs = $time / 3600000;
        $hh = floor($diffInHrs);

        $diffInMin = ($diffInHrs - $hh) * 60;
        $mm = floor($diffInMin);

        $diffInSec = ($diffInMin - $mm) * 60;
        $ss = floor($diffInSec);

        $diffInMs = ($diffInSec - $ss) * 100;
        $ms = floor($diffInMs);
        return sprintf('%02d:%02d:%02d:%02d',$hh, $mm, $ss,$ms);
    }
?>
    <button class='right' onclick="window.location.href='php/uploadJson.php'">upload dummy Json data</buttons><br>
    </body> 

    
</html>