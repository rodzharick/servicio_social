<?php
    // script para crear una conexión con la BD

    // Parametros requeridos para la conexión con la BD

    // Parametros BD local
    define('USER', 'root'); //  Crea la constante USER con valor 'root'
    define('PW', '');
    define('HOST', 'localhost');
    define('BD', 'servicio_social_1');

    // Parametros BD remota (infinityfree)
    /*define('USER', ''); 
    define('PW', '');
    define('HOST', '');
    define('BD', '');*/

    // Conexión con la BD
    $conexion = mysqli_connect(HOST, USER, PW, BD); 

    // Establecer conjunto de caracteres para el hosting
    mysqli_set_charset($conexion, 'utf8mb4'); 

    // Verificar la conexión con la BD

if (!$conexion) 
    {
        die("La conexión con la BD falló: " + mysqli_error($conexion));  
    } 
    /*else 
    {
        die("Conexión a la BD exitosa!"); 
    }*/
?>