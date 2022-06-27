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
    <title>Gomoku 15x15</title>
    <h2>Gomoku </h2> 
        <nav id="navigation">
            <div class="menu">
                <ul>
                    <li><a href="homepage.php">HomePage</a></li>
                    <li><a href="help.php">Help</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="leaderboard.php">Leaderboard</a></li>
                    <li><a href="game.php?logout='1'">Sign-Out</a></li>
                </ul>
            </div>
        </nav>

    <style>
        button {
            width: 240px;
            height: 60px;
            background-color: #00235D;
            color: #ffffff;
            border-radius: 15px;
        }

        header{
            background-color:#00235D;
            font-size: 25px;
            text-align: center;
            color: white;
            
        }

        #navigation {        
            background-color: #ffffff;
            margin-right: auto;    
            margin-left: auto;    
            max-width: 100%;    
            min-width: 1000px;
        }

        #navigation ul{
            padding: 8;
            list-style: none;
        }

        #navigation li{
            display: inline;
        }

        #navigation a:link{
            color: rgb(158, 5, 5);
            text-decoration: none;
            font-weight: bold;
            font-size: 30px;
            padding-right: 25px;
            padding-left: 50px;
        }

        #navigation a:visited{
            color: rgb(95, 0, 95);
        }

        #navigation a:hover{
            color: rgb(0,0,238);
        }

        #navigation a:active{
            color: green;
        }

        table{
            border-collapse: collapse; 
        }

        .center 
        {
            margin-left: auto;
             margin-right: auto;
         }

        td {
             padding: 0px;
             border: 2px solid black;
             background-color: #de891a;
        }

        td .winCells{
            border: 4px solid #CC0033;
        }

        .whosTurnIsIt{
            text-align: center;
        }

        .player{
            font-size: 50px;
            outline-color: white;
        }

        .white {
            color: white;
        }

        .time{
            font-size: 50px;
        }

        .turns{
            font-size:  50px;
        }
        #gameboardbottom{
            text-align: center;
            padding-bottom: 20px;
        }
        .backRed{
            background-color: #CC0033;
        }

        .backBlue{
            background-color: #00235D;
        }

        .highlight {
            border: 2px solid yellow;
        }
        .options{
            font-size: 35px;
        }
    </style>


    <script defer>
        var playerSel;
        var boardSize = 15;
        var rows = boardSize;
        var columns = boardSize;

        playerSel = 'pblack'; //keeps track of who's turn it is
        playerCount = 0;    // keeps track of how many turns the black player has made
        var gameRunning = true;

        var black3Ray = new Array();
        var black4Ray = new Array();

        var white3Ray = new Array();
        var White4Ray = new Array();

        var timerOn = false;
       
        function isTrue(main, ray2Check) //checks to see if the elments of arr2 are all inside arr; returns true or false
        {
            for(var x = 0; x<main.length; x++)
            {
                if(main[x].length<=ray2Check.length)
                {
                    for(var i=0;i<main[x].length;i++)
                    {
                        if(!(ray2Check.indexOf(main[x][i])>=0))
                        {
                            return false;
                        }
                    }
                }
                else
                {
                    return false;
                }
                return true;
            }
        }

        function someoneWon(winnerString, winCoords)
        {
            
            // do soemthing when someone wins
            stopTimer();
           
            for(var x = 0; x < 5; x ++) // higlight cells
            {
                document.getElementById(winCoords[x]).classList.add("winCells");
            }
            update(winnerString);

            
        }
        function update(winnerString)
        {
            requestAnimationFrame(() => {
	    

	            requestAnimationFrame(() => {
                    
                    if (confirm(winnerString + " Has won the game! Click Okay to update Player Stats and Go to the Leaderboard!")) 
                     {
                        if(winnerString =='black')
                        {
                            //submit form won
                            document.getElementById("winner").value = "black"
                            document.getElementById("turns").value = playerCount;
                            document.getElementById("time").value = timeElapsed;
                            document.getElementById("gform").submit();
                            
                        }else{
                            //submit form lost
                            document.getElementById("winner").value = "white"
                            document.getElementById("turns").value = playerCount;
                            document.getElementById("time").value = timeElapsed;
                            document.getElementById("gform").submit();
                         }
                     }
	            });
        });
    }

        function pSet() // set inital H tag message to Black's turn
        {
            document.getElementById("playerTxt").innerHTML = "BLACK";
        }

        function selection(id)
        {
            if(gameRunning)
            {
                if(timerOn == false)
                {
                    startTimer();
                    timerOn = true;
                }
                unhighlight();

                var cell = document.getElementById(id).src;

                if( cell.search("s.png") != -1 ) // check to see that square hasn't been clicked yet
                {
                    if(playerSel == "pblack")   //if black's turn
                    {
                        playerCount++;
                        document.getElementById(id).src = "images/sblack.png";
                        document.getElementById(id).alt = "black";
                        playerSel = "pwhite";
                        didWin();
                    } else // if white's turn 
                    {
                        document.getElementById(id).src = "images/swhite.png";
                        playerSel = "pblack";
                        document.getElementById(id).alt = "white";
                        didWin();
                    }
                }
                onClickFunc();
            }
        }

        function didWin() // check to see if there is a winner
        {
            var winners = new Array();
            var lastChecked = 'empty';
            var inARow = 0;

            //check horizontal======================================================================================
            for(var x = 0; x <boardSize; x++)
            {
                for(var y = 0; y <boardSize; y++) //check each row
                {
                    
                    var cellID = "r" + x + "c" + y;
                    var cellImg = document.getElementById(cellID).src;
                    var cell = document.getElementById(cellID);

                    if(cell.alt == 'black' || cell.alt == 'white' )//check cell not null
                    {
                        if(cell.alt == 'black')//cell is black
                        {
                            if(lastChecked == 'empty')// last checked was empty 
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }
                            else if(lastChecked == 'black') //last checked was black
                            {
                                winners.push(cellID);
                            }else // last checked was white, reset
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }

                            inARow++;

                            lastChecked = 'black';

                            if (inARow == 3 && !isTrue(black3Ray,winners))
                            {
                                black3Ray.push(winners);
                            }

                            if(inARow == 4 && !isTrue(black4Ray,winners))
                            {
                                black4Ray.push(winners);
                            }

                            if(inARow == 5) // WINNER ======================================
                            {
                                //console.log(winners);
                                //alert("black has won! horizontally!");
                                gameRunning = false;
                                someoneWon("black",winners);
                                //black has wone
                            }

                        }else //cell is whilte
                        {
                            if(lastChecked == 'empty')// last checked was empty 
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }
                            else if(lastChecked == 'white') //last checked was white
                            {
                                winners.push(cellID);
                            }else // last checked was black, reset
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }

                            lastChecked = 'white';
                            inARow++
                            
                            if (inARow == 3 && !isTrue(white3Ray,winners))
                            {
                                white3Ray.push(winners);
                            }

                            if(inARow == 4 && !isTrue(White4Ray,winners))
                            {
                                White4Ray.push(winners);
                            }

                            if(inARow == 5) // WINNER ======================================
                            {
                                //white has won
                               // console.log(winners);
                                //alert("White has won horizontally! horizontally!");
                                gameRunning = false;
                                someoneWon("white",winners);
                            }
                        }
                    } else // cell was null
                    {
                        inARow = 0;
                       lastChecked = '';
                       winners = new Array();
                    }
                    // here if Cell is equal to null
                    
                    
                }
                inARow = 0;
                winners = new Array();; //reset array for new row search
                lastChecked = '';
            }
            //check vertical ======================================================================================
            for(var x = 0; x <boardSize; x++)
            {
                for(var y = 0; y <boardSize; y++)// CHECK EACH VERTICAL ROW
                {
                    
                    var cellID = "r" + y + "c" + x;
                    var cellImg = document.getElementById(cellID).src;
                    var cell = document.getElementById(cellID);

                    if(cell.alt == 'black' || cell.alt == 'white' )//check cell not null
                    {
                        if(cell.alt == 'black')//cell is black
                        {
                            if(lastChecked == 'empty')// last checked was empty 
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }
                            else if(lastChecked == 'black') //last checked was black
                            {
                                winners.push(cellID);
                            }else // last checked was white, reset
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }

                            inARow++;

                            lastChecked = 'black';
                            
                            if (inARow == 3 && !isTrue(black3Ray,winners))
                            {
                                black3Ray.push(winners);
                            }

                            if(inARow == 4 && !isTrue(black4Ray,winners))
                            {
                                black4Ray.push(winners);
                            }

                            if(inARow == 5)
                            {
                              //  console.log(winners);
                               // alert("black has won! vertically!"); // WINNER ======================================
                                gameRunning = false;
                                someoneWon("black",winners);
                                //black has wone
                            }

                        }else //cell is whilte
                        {
                            if(lastChecked == 'empty')// last checked was empty 
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }
                            else if(lastChecked == 'white') //last checked was white
                            {
                                winners.push(cellID);
                            }else // last checked was black, reset
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }

                            lastChecked = 'white';
                            inARow++
                            
                            if (inARow == 3 && !isTrue(white3Ray,winners))
                            {
                                white3Ray.push(winners);
                            }

                            if(inARow == 4 && !isTrue(White4Ray,winners))
                            {
                                White4Ray.push(winners);
                            }

                            if(inARow == 5) // WINNER ======================================
                            {
                                //white has won
                               // console.log(winners);
                               // alert("White has won! vertically!");
                                gameRunning = false;
                                someoneWon("white",winners);
                            }
                        }
                    } else // cell was null
                    {
                        inARow = 0;
                       lastChecked = '';
                       winners = new Array();
                    }
                    
                    
                    
                }
                inARow = 0;
                winners = new Array();; //reset array for new row search
                lastChecked = '';
            }
            //check diagonal wins ======================================================================================

            for(var cellStart = 0; cellStart < rows; cellStart++) // loops starting on rows/collums: TOP LEFT going DOWN; checking towards UP and RIGHT side
            {
                var x = cellStart;
                var y = 0;
                
                while(x >= 0) // CHECK EACH ROW DIAGONALY
                {
                    //[x][y]
                   // console.log("[" + x + "][" + y + "]");

                   var cellID = "r" + x + "c" + y;
                    var cellImg = document.getElementById(cellID).src;
                    var cell = document.getElementById(cellID);

                    if(cell.alt == 'black' || cell.alt == 'white' )//check cell not null
                    {
                        if(cell.alt == 'black')//cell is black
                        {
                            if(lastChecked == 'empty')// last checked was empty 
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }
                            else if(lastChecked == 'black') //last checked was black
                            {
                                winners.push(cellID);
                            }else // last checked was white, reset
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }

                            inARow++;

                            lastChecked = 'black';
                            
                            if (inARow == 3 && !isTrue(black3Ray,winners))
                            {
                                black3Ray.push(winners);
                            }

                            if(inARow == 4 && !isTrue(black4Ray,winners))
                            {
                                black4Ray.push(winners);
                            }

                            if(inARow == 5)
                            {
                               // console.log(winners);
                               // alert("black has won! diagonaly!"); // WINNER ======================================
                                gameRunning = false;
                                someoneWon("black",winners);
                                //black has wone
                            }

                        }else //cell is whilte
                        {
                            if(lastChecked == 'empty')// last checked was empty 
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }
                            else if(lastChecked == 'white') //last checked was white
                            {
                                winners.push(cellID);
                            }else // last checked was black, reset
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }

                            lastChecked = 'white';
                            inARow++

                            if (inARow == 3 && !isTrue(white3Ray,winners))
                            {
                                white3Ray.push(winners);
                            }

                            if(inARow == 4 && !isTrue(White4Ray,winners))
                            {
                                White4Ray.push(winners);
                            }
                            
                            if(inARow == 5) // WINNER ======================================
                            {
                                //white has won
                                //console.log(winners);
                                //alert("White has won! diagonaly!");
                                gameRunning = false;
                                someoneWon("white",winners);
                            }
                        }
                    } else // cell was null
                    {
                        inARow = 0;
                       lastChecked = '';
                       winners = new Array();
                    }

                   // GET NEXT CELL TO CHECK
                    x = x - 1;
                    y = y + 1;
                } //FINISH CHECKING A ROW HERE
                inARow = 0;
                winners = new Array();; //reset array for new row search
                lastChecked = '';
                
            }
           
            for(var cellStart = 1; cellStart < columns; cellStart++) // loops starting on rows/collums: BOTTOM left going RIGHT; checking towards UP and RIGHT side
            {
                var x = rows -1;
                var y = cellStart; 

                while(y < columns)//CHECKING EACH ROW DIAGONALY
                {
                    //[x][y]
                    //console.log("[" + x + "][" + y + "]");
                    var cellID = "r" + x + "c" + y;
                    var cellImg = document.getElementById(cellID).src;
                    var cell = document.getElementById(cellID);

                    if(cell.alt == 'black' || cell.alt == 'white' )//check cell not null
                    {
                        if(cell.alt == 'black')//cell is black
                        {
                            if(lastChecked == 'empty')// last checked was empty 
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }
                            else if(lastChecked == 'black') //last checked was black
                            {
                                winners.push(cellID);
                            }else // last checked was white, reset
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }

                            inARow++;

                            lastChecked = 'black';

                            if (inARow == 3 && !isTrue(black3Ray,winners))
                            {
                                black3Ray.push(winners);
                            }

                            if(inARow == 4 && !isTrue(black4Ray,winners))
                            {
                                black4Ray.push(winners);
                            }
                            
                            if(inARow == 5)
                            {
                               // console.log(winners);
                                //alert("black has won! diagonaly!"); // WINNER ======================================
                                gameRunning = false;
                                someoneWon("black",winners);
                                //black has wone
                            }

                        }else //cell is whilte
                        {
                            if(lastChecked == 'empty')// last checked was empty 
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }
                            else if(lastChecked == 'white') //last checked was white
                            {
                                winners.push(cellID);
                            }else // last checked was black, reset
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }

                            lastChecked = 'white';
                            inARow++

                            if (inARow == 3 && !isTrue(white3Ray,winners))
                            {
                                white3Ray.push(winners);
                            }

                            if(inARow == 4 && !isTrue(White4Ray,winners))
                            {
                                White4Ray.push(winners);
                            }
                            
                            if(inARow == 5) // WINNER ======================================
                            {
                                //white has won
                               // console.log(winners);
                               // alert("White has won! diagonaly!");
                                gameRunning = false;
                                someoneWon("white",winners);
                            }
                        }
                    } else // cell was null
                    {
                        inARow = 0;
                       lastChecked = '';
                       winners = new Array();
                    }
                    //GETTING NEXT CELL TO CHECK
                    x = x - 1;
                    y = y + 1;
                }//FINISH CHECKING A ROW HERE
                inARow = 0;
                winners = new Array();; //reset array for new row search
                lastChecked = '';
                

            }
            
            for(var cellStart = 0; cellStart < columns; cellStart++) // loops starting on rows/columns: BOTTOM LEFT going RIGHT; Checking towards TOP and LEFT side
            {
                var x = rows -1; 
                var y = cellStart;

                while(y >= 0) //CHECKING EACH ROW DIAGONALY
                {
                    //[x][y]
                    //console.log("[" + x + "][" + y + "]");

                    var cellID = "r" + x + "c" + y;
                    var cellImg = document.getElementById(cellID).src;
                    var cell = document.getElementById(cellID);

                    if(cell.alt == 'black' || cell.alt == 'white' )//check cell not null
                    {
                        if(cell.alt == 'black')//cell is black
                        {
                            if(lastChecked == 'empty')// last checked was empty 
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }
                            else if(lastChecked == 'black') //last checked was black
                            {
                                winners.push(cellID);
                            }else // last checked was white, reset
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }

                            inARow++;

                            lastChecked = 'black';

                            if (inARow == 3 && !isTrue(black3Ray,winners))
                            {
                                black3Ray.push(winners);
                            }

                            if(inARow == 4 && !isTrue(black4Ray,winners))
                            {
                                black4Ray.push(winners);
                            }
                            
                            if(inARow == 5)
                            {
                                //console.log(winners);
                                //alert("black has won! diagonaly!"); // WINNER ======================================
                                gameRunning = false;
                                someoneWon("black",winners);
                                //black has wone
                            }

                        }else //cell is whilte
                        {
                            if(lastChecked == 'empty')// last checked was empty 
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }
                            else if(lastChecked == 'white') //last checked was white
                            {
                                winners.push(cellID);
                            }else // last checked was black, reset
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }

                            lastChecked = 'white';
                            inARow++

                            if (inARow == 3 && !isTrue(white3Ray,winners))
                            {
                                white3Ray.push(winners);
                            }

                            if(inARow == 4 && !isTrue(White4Ray,winners))
                            {
                                White4Ray.push(winners);
                            }
                            
                            if(inARow == 5) // WINNER ======================================
                            {
                                //white has won
                               // console.log(winners);
                                //alert("White has won! diagonaly!");
                                gameRunning = false;
                                someoneWon("white",winners);
                            }
                        }
                    } else // cell was null
                    {
                        inARow = 0;
                       lastChecked = '';
                       winners = new Array();
                    }

                    //GETTING NEXT CELL TO CHECK
                    y = y - 1;
                    x = x - 1;
                } //FINISH CHECKING A ROW HERE
                inARow = 0;
                winners = new Array();; //reset array for new row search
                lastChecked = '';
                

            }
            
            for(var cellStart =  columns -2; cellStart >= 0; cellStart--) // loops starting on rows/collums on  BOTTOM RIGHT  going UP; Checking towards TOP and LEFT side
            {
                var x = cellStart; 
                var y = columns -1;

                while(x >= 0)//CHECK EACH ROW DIAGONALY
                {
                    //[x][y]
                    //console.log("[" + x + "][" + y + "]");

                    var cellID = "r" + x + "c" + y;
                    var cellImg = document.getElementById(cellID).src;
                    var cell = document.getElementById(cellID);

                    if(cell.alt == 'black' || cell.alt == 'white' )//check cell not null
                    {
                        if(cell.alt == 'black')//cell is black
                        {
                            if(lastChecked == 'empty')// last checked was empty 
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }
                            else if(lastChecked == 'black') //last checked was black
                            {
                                winners.push(cellID);
                            }else // last checked was white, reset
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }

                            inARow++;

                            lastChecked = 'black';

                            if (inARow == 3 && !isTrue(black3Ray,winners))
                            {
                                black3Ray.push(winners);
                            }

                            if(inARow == 4 && !isTrue(black4Ray,winners))
                            {
                                black4Ray.push(winners);
                            }
                            
                            if(inARow == 5)
                            {
                               // console.log(winners);
                                //alert("black has won! diagonaly!"); // WINNER ======================================
                                gameRunning = false;
                                someoneWon("black",winners);
                                //black has wone
                            }

                        }else //cell is whilte
                        {
                            if(lastChecked == 'empty')// last checked was empty 
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }
                            else if(lastChecked == 'white') //last checked was white
                            {
                                winners.push(cellID);
                            }else // last checked was black, reset
                            {
                                inARow = 0;
                                winners = new Array();;
                                winners.push(cellID);
                            }

                            lastChecked = 'white';
                            inARow++

                            if (inARow == 3 && !isTrue(white3Ray,winners))
                            {
                                white3Ray.push(winners);
                            }

                            if(inARow == 4 && !isTrue(White4Ray,winners))
                            {
                                White4Ray.push(winners);
                            }
                            
                            if(inARow == 5) // WINNER ======================================
                            {
                                //white has won
                               // console.log(winners);
                                //alert("White has won! diagonaly!");
                                gameRunning = false;
                                someoneWon("white",winners);
                            }
                        }
                    } else // cell was null
                    {
                        inARow = 0;
                       lastChecked = '';
                       winners = new Array();
                    }

                    //GETTING NEXT CELL TO CHECK
                    x = x - 1;
                    y = y - 1;
                }
                inARow = 0;
                winners = new Array();; //reset STUFF FOR NEXT SEARCH
                lastChecked = '';

            }
           

        }

        function onClickFunc()
        {
            if(gameRunning)
            {
                if (playerSel == "pblack")
                {
                    document.getElementById("playerTxt").innerHTML = "BLACK";
                    document.getElementById("playerTxt").classList.remove("white");
                }else
                {
                    document.getElementById("playerTxt").innerHTML = "WHITE";
                    document.getElementById("playerTxt").classList.add("white");
                }
            } else// game has ended
            {
                if (playerSel == "pwhite") // actually means player that won is black, since this func gets called after playersel switch
                {
                    document.getElementById("ptxt1").innerHTML = "";
                    document.getElementById("ptxt2").innerHTML = "";
                    document.getElementById("playerTxt").innerHTML = "BLACK Has Won the Game!";
                    
                    document.getElementById("playerTxt").classList.remove("white");
                }else
                {
                    document.getElementById("ptxt1").innerHTML = "";
                    document.getElementById("ptxt2").innerHTML = "";
                    document.getElementById("playerTxt").innerHTML = "WHITE Has Won the Game!";
                    document.getElementById("playerTxt").classList.add("white");
                }
            }

           document.getElementById("turnTxt").innerHTML = playerCount;

        }

        function unhighlight()
        {
            white3Ray = [];
            White4Ray = [];
            black4Ray = [];
            black3Ray = [];

            for(var x = 0; x <boardSize; x++)
            {
                for(var y = 0; y <boardSize; y++) //check each row
                {
                    
                    var cellID = "r" + x + "c" + y;
                    var cell = document.getElementById(cellID);
                    cell.classList.remove("highlight");
                }
            }
        }

        function Hint()
        {
            if(playerSel == 'pwhite')
            {
                for(var x = 0; x < white3Ray.length; x++)
                {
                    for(var y = 0; y < white3Ray[x].length; y++)
                    {
                        document.getElementById(white3Ray[x][y]).classList.add("highlight");
                    }
                }
            }else
            {
                for(var x = 0; x < black3Ray.length; x++)
                {
                    for(var y = 0; y < black3Ray[x].length; y++)
                    {
                        document.getElementById(black3Ray[x][y]).classList.add("highlight");
                    }
                }
            }
        }

        function fourHint()/// not in use, probably delete?
        {
            if(playerSel == 'pwhite')
            {
                for(var x = 0; x < White4Ray.length; x++)
                {
                    for(var y = 0; y < White4Ray[x].length; y++)
                    {
                        document.getElementById(White4Ray[x][y]).classList.add("highlight");
                    }
                }
            }else
            {
                for(var x = 0; x < black4Ray.length; x++)
                {
                    for(var y = 0; y < black4Ray[x].length; y++)
                    {
                        document.getElementById(black4Ray[x][y]).classList.add("highlight");
                    }
                }
            }
        }

        //timer stuff ======================================================================
        var timerStart;
        var timeElapsed = 0; // this will be used to store total time on the database
        var timeUpdater;

        function refreshTxt(txt) {
        document.getElementById("timer").innerHTML = txt;
        }

        function startTimer() {
        timerStart = Date.now() - timeElapsed;
        timeUpdater = setInterval(function () { timeElapsed = Date.now() - timerStart; refreshTxt(convertTime2String(timeElapsed)); }, 10);
        }

        function stopTimer() {
        clearInterval(timeUpdater);
        }

        function convertTime2String(time) { 
        let diffInHrs = time / 3600000;
        let hh = Math.floor(diffInHrs);

        let diffInMin = (diffInHrs - hh) * 60;
        let mm = Math.floor(diffInMin);

        let diffInSec = (diffInMin - mm) * 60;
        let ss = Math.floor(diffInSec);

        let diffInMs = (diffInSec - ss) * 100;
        let ms = Math.floor(diffInMs);

        let formattedMM = mm.toString().padStart(2, "0");
        let formattedSS = ss.toString().padStart(2, "0");
        let formattedMS = ms.toString().padStart(2, "0");

        return `${formattedMM}:${formattedSS}:${formattedMS}`;
        }

    </script>
</header>

<body onload="pSet()" class="backBlue">

        <form method="post" action="php/game-inc.php" id="gform">
        <input type="hidden" id="winner" name ="winner" value ="">
        <input type="hidden" id="turns" name ="turns" value ="">
        <input type="hidden" id="time" name ="time" value ="">
        </form>

        <div class="whosTurnIsIt backRed">
            <span class="player" id="ptxt1">Its player: </span>
            <span class="player" id="playerTxt"></span>
            <span class="player" id="ptxt2">'S turn</span><br>
        </div>

        <div >
            <table id="gameboard" class="center">
                <tr><!-- ====================================================================================================== ROW 0 -->
                    <td ><img onclick="selection('r0c0')" id="r0c0" src="images/s.png" alt="" width="40" height="40"></td>
                    <td ><img onclick="selection('r0c1')" id="r0c1" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r0c2')" id="r0c2" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r0c3')" id="r0c3" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r0c4')" id="r0c4" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r0c5')" id="r0c5" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r0c6')" id="r0c6" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r0c7')" id="r0c7" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r0c8')" id="r0c8" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r0c9')" id="r0c9" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r0c10')" id="r0c10" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r0c11')" id="r0c11" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r0c12')" id="r0c12" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r0c13')" id="r0c13" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r0c14')" id="r0c14" src="images/s.png" width="40" height="40"></td>
                </tr>
        <!-- ====================================================================================================== ROW 1 -->
                <tr>
                    <td ><img onclick="selection('r1c0')" id="r1c0" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r1c1')" id="r1c1" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r1c2')" id="r1c2" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r1c3')" id="r1c3" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r1c4')" id="r1c4" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r1c5')" id="r1c5" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r1c6')" id="r1c6" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r1c7')" id="r1c7" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r1c8')" id="r1c8" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r1c9')" id="r1c9" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r1c10')" id="r1c10" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r1c11')" id="r1c11" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r1c12')" id="r1c12" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r1c13')" id="r1c13" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r1c14')" id="r1c14" src="images/s.png" width="40" height="40"></td>
                </tr>
        <!-- ====================================================================================================== ROW 2 -->
                <tr>
                    <td ><img onclick="selection('r2c0')" id="r2c0" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r2c1')" id="r2c1" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r2c2')" id="r2c2" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r2c3')" id="r2c3" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r2c4')" id="r2c4" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r2c5')" id="r2c5" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r2c6')" id="r2c6" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r2c7')" id="r2c7" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r2c8')" id="r2c8" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r2c9')" id="r2c9" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r2c10')" id="r2c10" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r2c11')" id="r2c11" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r2c12')" id="r2c12" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r2c13')" id="r2c13" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r2c14')" id="r2c14" src="images/s.png" width="40" height="40"></td>    
                </tr>
        <!-- ====================================================================================================== ROW 3 -->
                <tr>
                    <td ><img onclick="selection('r3c0')" id="r3c0" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r3c1')" id="r3c1" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r3c2')" id="r3c2" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r3c3')" id="r3c3" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r3c4')" id="r3c4" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r3c5')" id="r3c5" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r3c6')" id="r3c6" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r3c7')" id="r3c7" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r3c8')" id="r3c8" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r3c9')" id="r3c9" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r3c10')" id="r3c10" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r3c11')" id="r3c11" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r3c12')" id="r3c12" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r3c13')" id="r3c13" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r3c14')" id="r3c14" src="images/s.png" width="40" height="40"></td>
                </tr>
        <!-- ====================================================================================================== ROW 4 -->
                <tr>
                    <td ><img onclick="selection('r4c0')" id="r4c0" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r4c1')" id="r4c1" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r4c2')" id="r4c2" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r4c3')" id="r4c3" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r4c4')" id="r4c4" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r4c5')" id="r4c5" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r4c6')" id="r4c6" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r4c7')" id="r4c7" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r4c8')" id="r4c8" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r4c9')" id="r4c9" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r4c10')" id="r4c10" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r4c11')" id="r4c11" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r4c12')" id="r4c12" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r4c13')" id="r4c13" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r4c14')" id="r4c14" src="images/s.png" width="40" height="40"></td>    
                </tr>
        <!-- ====================================================================================================== ROW 5 -->
                <tr>
                    <td ><img onclick="selection('r5c0')" id="r5c0" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r5c1')" id="r5c1" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r5c2')" id="r5c2" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r5c3')" id="r5c3" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r5c4')" id="r5c4" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r5c5')" id="r5c5" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r5c6')" id="r5c6" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r5c7')" id="r5c7" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r5c8')" id="r5c8" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r5c9')" id="r5c9" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r5c10')" id="r5c10" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r5c11')" id="r5c11" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r5c12')" id="r5c12" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r5c13')" id="r5c13" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r5c14')" id="r5c14" src="images/s.png" width="40" height="40"></td>
                </tr>
        <!-- ====================================================================================================== ROW 6 -->
                <tr>
                    <td ><img onclick="selection('r6c0')" id="r6c0" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r6c1')" id="r6c1" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r6c2')" id="r6c2" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r6c3')" id="r6c3" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r6c4')" id="r6c4" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r6c5')" id="r6c5" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r6c6')" id="r6c6" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r6c7')" id="r6c7" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r6c8')" id="r6c8" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r6c9')" id="r6c9" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r6c10')" id="r6c10" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r6c11')" id="r6c11" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r6c12')" id="r6c12" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r6c13')" id="r6c13" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r6c14')" id="r6c14" src="images/s.png" width="40" height="40"></td>    
                </tr>
        <!-- ====================================================================================================== ROW 7 -->
                <tr>
                    <td ><img onclick="selection('r7c0')" id="r7c0" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r7c1')" id="r7c1" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r7c2')" id="r7c2" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r7c3')" id="r7c3" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r7c4')" id="r7c4" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r7c5')" id="r7c5" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r7c6')" id="r7c6" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r7c7')" id="r7c7" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r7c8')" id="r7c8" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r7c9')" id="r7c9" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r7c10')" id="r7c10" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r7c11')" id="r7c11" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r7c12')" id="r7c12" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r7c13')" id="r7c13" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r7c14')" id="r7c14" src="images/s.png" width="40" height="40"></td>
                </tr>
        <!-- ====================================================================================================== ROW 8 -->
                <tr>
                    <td ><img onclick="selection('r8c0')" id="r8c0" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r8c1')" id="r8c1" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r8c2')" id="r8c2" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r8c3')" id="r8c3" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r8c4')" id="r8c4" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r8c5')" id="r8c5" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r8c6')" id="r8c6" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r8c7')" id="r8c7" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r8c8')" id="r8c8" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r8c9')" id="r8c9" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r8c10')" id="r8c10" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r8c11')" id="r8c11" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r8c12')" id="r8c12" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r8c13')" id="r8c13" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r8c14')" id="r8c14" src="images/s.png" width="40" height="40"></td>    
                </tr>
        <!-- ====================================================================================================== ROW 9 -->
                <tr>
                    <td ><img onclick="selection('r9c0')" id="r9c0" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r9c1')" id="r9c1" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r9c2')" id="r9c2" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r9c3')" id="r9c3" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r9c4')" id="r9c4" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r9c5')" id="r9c5" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r9c6')" id="r9c6" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r9c7')" id="r9c7" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r9c8')" id="r9c8" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r9c9')" id="r9c9" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r9c10')" id="r9c10" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r9c11')" id="r9c11" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r9c12')" id="r9c12" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r9c13')" id="r9c13" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r9c14')" id="r9c14" src="images/s.png" width="40" height="40"></td>
                </tr>
        <!-- ====================================================================================================== ROW 10 -->
                <tr>
                    <td ><img onclick="selection('r10c0')" id="r10c0" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r10c1')" id="r10c1" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r10c2')" id="r10c2" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r10c3')" id="r10c3" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r10c4')" id="r10c4" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r10c5')" id="r10c5" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r10c6')" id="r10c6" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r10c7')" id="r10c7" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r10c8')" id="r10c8" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r10c9')" id="r10c9" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r10c10')" id="r10c10" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r10c11')" id="r10c11" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r10c12')" id="r10c12" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r10c13')" id="r10c13" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r10c14')" id="r10c14" src="images/s.png" width="40" height="40"></td>    
                </tr>
        <!-- ====================================================================================================== ROW 11 -->
                <tr>
                    <td ><img onclick="selection('r11c0')" id="r11c0" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r11c1')" id="r11c1" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r11c2')" id="r11c2" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r11c3')" id="r11c3" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r11c4')" id="r11c4" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r11c5')" id="r11c5" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r11c6')" id="r11c6" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r11c7')" id="r11c7" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r11c8')" id="r11c8" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r11c9')" id="r11c9" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r11c10')" id="r11c10" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r11c11')" id="r11c11" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r11c12')" id="r11c12" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r11c13')" id="r11c13" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r11c14')" id="r11c14" src="images/s.png" width="40" height="40"></td>
                </tr>
        <!-- ====================================================================================================== ROW 12 -->
                <tr>
                    <td ><img onclick="selection('r12c0')" id="r12c0" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r12c1')" id="r12c1" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r12c2')" id="r12c2" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r12c3')" id="r12c3" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r12c4')" id="r12c4" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r12c5')" id="r12c5" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r12c6')" id="r12c6" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r12c7')" id="r12c7" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r12c8')" id="r12c8" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r12c9')" id="r12c9" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r12c10')" id="r12c10" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r12c11')" id="r12c11" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r12c12')" id="r12c12" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r12c13')" id="r12c13" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r12c14')" id="r12c14" src="images/s.png" width="40" height="40"></td>    
                </tr>
        <!-- ====================================================================================================== ROW 13 -->
                <tr>
                    <td ><img onclick="selection('r13c0')" id="r13c0" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r13c1')" id="r13c1" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r13c2')" id="r13c2" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r13c3')" id="r13c3" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r13c4')" id="r13c4" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r13c5')" id="r13c5" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r13c6')" id="r13c6" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r13c7')" id="r13c7" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r13c8')" id="r13c8" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r13c9')" id="r13c9" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r13c10')" id="r13c10" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r13c11')" id="r13c11" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r13c12')" id="r13c12" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r13c13')" id="r13c13" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r13c14')" id="r13c14" src="images/s.png" width="40" height="40"></td>
                </tr>
        <!-- ====================================================================================================== ROW 14 -->
                <tr>
                    <td ><img onclick="selection('r14c0')" id="r14c0" src="images/s.png" width="40" height="40"></td> 
                    <td ><img onclick="selection('r14c1')" id="r14c1" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r14c2')" id="r14c2" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r14c3')" id="r14c3" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r14c4')" id="r14c4" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r14c5')" id="r14c5" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r14c6')" id="r14c6" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r14c7')" id="r14c7" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r14c8')" id="r14c8" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r14c9')" id="r14c9" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r14c10')" id="r14c10" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r14c11')" id="r14c11" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r14c12')" id="r14c12" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r14c13')" id="r14c13" src="images/s.png" width="40" height="40"></td>
                    <td ><img onclick="selection('r14c14')" id="r14c14" src="images/s.png" width="40" height="40"></td>    
                </tr>


            </table>
        </div>

    <div id="gameboardbottom" class="backRed">
        <span class="time" id="timer">00:00:00</span><br>
        <span class="turns">Number of Turns:</span>
        <span class="turns" id="turnTxt"></span><br>

        
        <span class="options">Options: </span>
        <button onclick="Hint()">Hint: 3 or 4 in a Row</button>
        <button onclick="window.location.reload();">restart with a 15x15 game</button>

    </div>

</body>
</html>