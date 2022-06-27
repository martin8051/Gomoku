<?php
    ?>

<html>
    <header>
    <link rel="stylesheet"  href="css/style2.css">
    <div>
        <h1><i>Gomoku</i></h1>
    </div>
    </header>
<body>
    <div>
    <h1>Log in</h1>
    <p> No account? <a href="register.php">Register here.</a>
    </p>

    <form action="php/login-inc.php" method="post">
        <input type="text" name ="username" placeholder="Username">
        <input type="password" name="password" placeholder="Password">
        <button type="submit" name="userlogin">Login</button>
    </form>
    </div>
</body>

<footer>
    <h4>Created by Pete Solis and Martin Pantoja</h4>
</footer>
</html>