<?php


    $servername="localhost";
    $username= "AdminLab12";
    $password = "4VPnroTOC6wOU3mn";

    //create a connection
    $connection = new mysqli($servername,$username,$password);

    if($connection->connect_error)
    {
        die("connection failed: " . $connection->connect_error);
    }

    //create a Database

    $sql = "CREATE DATABASE gomoku";
    $connection->query($sql);
    $dbname = "gomoku";
    $connection->close();
 
    $connection = new mysqli($servername, $username, $password, $dbname);

   $sql = "CREATE TABLE GomokuTable (
        pID INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(200) NOT NULL,
        pass VARCHAR(200) NOT NULL,
        pTurns INT(10),
        pTime INT(8),
        pWins INT(10),
        pGamesPlayed INT(10)
        )";
    
    $connection->query($sql);

    if($connection->query($sql)==TRUE)
    {
        echo "database and table created correctly";
    }else
    {
        echo "something went wrong making database lol";
    }

    $connection->close();
    
    header('Location: ' . $_SERVER["HTTP_REFERER"] );
    exit;

?>