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
    <link rel="icon" type="image/x-icon" href="/img/favicon.ico">
    <img src="img/escudoColegio.png" alt="logo del colegio">

    <title>Servicio Social</title>
    <center>
        <h1>Bienvenido a servicio social <h1>
        <h3>Inicio de sesión</h3>
        <br><br>
        <a href="modelo/login_admin.php">
        <button type = "submit">Admin</button>
        </a>

        <a href="modelo/login_acudiente.php">
        <button type = "submit">Acudiente</button>
        </a>
        
        <a href="modelo/login_estudiante.php">
        <button type = "submit">Estudiante</button>
        </a>

        <a href="modelo/login_supervisor.php">
        <button type = "submit">Supervisor</button>
        </a>
    </center>
    <h1></h1>
</head>
<body>
    
</body>
</html>