<?php
error_reporting(E_ALL);
ini_set('display_errors','1');
?>


<?php
    require "conexion.php";

    // iniciar sesion para guardar los datos del usuario
    session_start();

    $usuario = $_POST['correo'];
    $password = $_POST['password'];

    $query_1 = "SELECT correo, COUNT(*) AS contar FROM administrador WHERE correo = '$usuario' AND contraseña = '$password'";

    $consulta = mysqli_query($conexion, $query_1) or trigger_error("Error en la consulta MYSQL: " + mysqli_error($conexion));

    $resultado = mysqli_fetch_array($consulta);

    if($resultado['contar'] > 0)
    {
        $_SESSION['username'] = $usuario;
        //redirigir el usuario a su pagina
        header("location: ../pagina_administrador.php");

        echo "El usuario existe en la BD <br>";
        echo $resultado ['correo'];
    }
    else
    {
        echo "El usuario no existe, o hay un error en el nombre de usuario o la contraseña";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
   
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    
</body>
</html>