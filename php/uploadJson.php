<?php

require 'database.php';

 //read the json file contents
 $jsondata = file_get_contents('storage.json');
 
 //convert json object to php associative array
 $data = json_decode($jsondata, true);

 //get values and store them into appropriate arrays
    $pID = array();
    $pUsername= array();
    $pPass = array();
    $pTurns = array();
    $pTime = array();
    $pWins = array();
    $pGamesPlayed = array();

    for($x = 0; $x< count($data) ; $x++)
    {
        array_push($pID,$data[$x]['pID']);
        array_push($pUsername,$data[$x]['username']);
        array_push($pPass,$data[$x]['pass']);
        array_push($pTurns,$data[$x]['pTurns']);
        array_push($pTime,$data[$x]['pTime']);
        array_push($pWins,$data[$x]['pWins']);
        array_push($pGamesPlayed,$data[$x]['pGamesPlayed']);
    }

    $sql = "";

    for($x = 0; $x< count($data) ; $x++) //create multi query sql
    {
        $sql .= "INSERT INTO `gomokutable`(`pID`, `username`, `pass`, `pTurns`, `pTime`, `pWins`, `pGamesPlayed`) VALUES ('$pID[$x]',
        '$pUsername[$x]','$pPass[$x]','$pTurns[$x]','$pTime[$x]','$pWins[$x]','$pGamesPlayed[$x]');";
    }
    echo $sql;

  $conn->multi_query($sql);

  header('Location: ' . $_SERVER["HTTP_REFERER"] );
  exit;
 
?>
