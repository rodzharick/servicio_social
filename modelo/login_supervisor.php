<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
    <title>Login</title>
</head>
<body>
    <center>
        <h1>Iniciar sesión para supervisor</h1>
        <form action="loguearse_supervisor.php" method="POST">
    <label for="supervisor_correo">Ingrese su correo electrónico:</label>
    <br><br>
    <input type="email" name="correo" id="supervisor_correo" required>
    <br><br>
    <label for="supervisor_password">Ingrese su contraseña:</label>
    <br><br>
    <input type="password" name="password" id="supervisor_password" required> <br><br>
    <button type="submit">Ingresar</button>    
</form>
    </center>
</body>
</html>