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
    <h1>Iniciar sesión para administradores</h1>
    <form action="loguearse_admin.php" method="POST">
    <label for="admin_correo">Ingrese su correo electrónico:</label>
    <br><br>
    <input type="email" name="correo" id="admin_correo" required>
    <br><br>
    <label for="admin_password">Ingrese su contraseña:</label>
    <br><br>
    <input type="password" name="password" id="admin_password" required> <br><br>
    <button type="submit">Ingresar</button>
   
</form>
    </center>
</body>
</html>