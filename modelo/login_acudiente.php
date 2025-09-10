<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Login</title>
</head>
<body>
    <center>
        <h1>Iniciar sesión para Acudiente</h1>
        <form action="loguearse_acudiente.php" method="POST">
            <label for="">Ingrese su correo electrónico:</label>
            <br><br>
            <input type="text" name="email" id="" required>
            <br><br>
            <label for="">Ingrese su contraseña:</label>
            <br><br>
            <input type="text" name="contraseña" id="" required>
            <br><br>
            <button type="submit">Ingresar</button>
        </form>
    </center>
</body>
</html>