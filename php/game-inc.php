<?php
// THIS IS WHERE WE WILL UDATE GAME STATS ON COMPLETION OF GAME 
// WILL BE SENT AS POST FROM GAME.HTML
session_start(); 

require 'database.php';
$errors = array(); 

if (isset($_POST['winner'])) 
{
    $seshName= $_SESSION['username'];

    // get number of turns, time, wins, games played
    $query = "SELECT * FROM gomokutable WHERE username='$seshName'";
    $results = mysqli_query($conn, $query);
   $serverStats = $results->fetch_assoc();

    $time = $serverStats['pTime'];
    $turns = $serverStats['pTurns'];
    $wins = $serverStats['pWins'];
    $numGames = $serverStats['pGamesPlayed'];

    if($_POST['winner']=='black')
    {
        echo "winner is black";

        $time = $time + $_POST['time'];
        $turns = $turns + $_POST['turns'];
        $wins = $wins + 1;
        $numGames = $numGames + 1;

    }else
    {
        echo "winner is white";
        $time = $time + $_POST['time'];
        $turns = $turns + $_POST['turns'];
        $numGames = $numGames + 1;
    }

    //submit results into mysql 
    $query = "UPDATE gomokutable SET pWins = $wins, pTurns = $turns, pTime = $time, pGamesPlayed = $numGames  WHERE username='$seshName'";
    $conn->query($query);
    
    header('location: ../leaderboard.php');
}

?>