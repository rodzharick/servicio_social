<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Servicio Social</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="container">
        <h1>Bienvenido a Servicio Social</h1>
        <h3>Inicio de sesión</h3>

        <a href="modelo/login_admin.php">
            <button>Administrador</button>
        </a>
        <a href="modelo/login_acudiente.php">
            <button>Acudiente</button>
        </a>
        <a href="modelo/login_estudiante.php">
            <button>Estudiante</button>
        </a>
        <a href="modelo/login_supervisores.php">
            <button>Supervisor</button>
        </a>
    </div>
</body>
</html>
