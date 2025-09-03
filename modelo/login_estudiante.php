<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/index.css">
    <title>Login</title>
</head>
<body>
    <center>
        <h1>Iniciar sesión para Estudiantes</h1>
        <form action="loguearse_estudiante.php" method="POST">
    <label for="estudiante_correo">Ingrese su correo electrónico:</label>
    <br><br>
    <input type="email" name="correo" id="estudiante_correo" required>
    <br><br>
    <label for="estudiante_password">Ingrese su contraseña:</label>
    <br><br>
    <input type="password" name="password" id="admin_password" required> <br><br>
    <button type="submit">Ingresar</button>
</form> 
    </center>
</body>
</html>