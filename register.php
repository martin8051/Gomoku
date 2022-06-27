<html>
    <header>
    <link rel="stylesheet"  href="css/style2.css">
    <div>
        <h1><i>Gomoku</i></h1>
    </div>
    </header>
<body>
    <div>
    <h1>Register Account</h1>
    <p> Already have an account? <a href="login.php">Login here.</a>
    </p>

    <form action="php/register-inc.php" method="post">
        <input type="text" name ="username" placeholder="Username">
        <input type="password" name="password" placeholder="Password">
        <input type="password" name="confirmPassword" placeholder="Confirm Password">
        <button type="submit" name="register">Register</button>
    </form>
    </div>
    
    <div>
        <h4>If this is your first time launching, click button below to set up Database</h4>
        <button onclick="location.href = 'php/CreateDBnTBL.php'">Set-Up DataBase</button>
    </div>
</body>

<footer>
    <h4>Created by Pete Solis and Martin Pantoja</h4>
</footer>
