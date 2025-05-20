<?php
    require 'modelo/conexion.php';

    session_start();

    // Verificar si existe una sesión de administrador
    if(!isset($_SESSION['username']))
    {
        header("location: index.php");
        exit();
    }

    $nombre_usuario = $_SESSION['username'];
    
    // Obtener datos del administrador
    $query = "SELECT nombre, apellidos FROM administrador WHERE correo = '$nombre_usuario'";
    $resultado = mysqli_query($conexion, $query);
    $datos = mysqli_fetch_array($resultado);

    // Inicializar variables
    $mensaje = '';

    // Procesar formularios
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Agregar nuevo acudiente
        if (isset($_POST['agregar'])) {
            $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
            $apellidos = mysqli_real_escape_string($conexion, $_POST['apellidos']);
            $cedula = mysqli_real_escape_string($conexion, $_POST['cedula']);
            $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
            $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
            $contrasena = mysqli_real_escape_string($conexion, $_POST['contrasena']);
            
            // Verificar si el correo ya existe
            $verificar = "SELECT * FROM acudiente WHERE correo = '$correo'";
            $resultado_verificar = mysqli_query($conexion, $verificar);
            
            if (mysqli_num_rows($resultado_verificar) > 0) {
                $mensaje = "El correo electrónico ya está registrado";
            } else {
                $insertar = "INSERT INTO acudiente (nombre, apellidos, cedula, correo, telefono, contraseña) 
                            VALUES ('$nombre', '$apellidos', '$cedula', '$correo', '$telefono', '$contrasena')";
                
                if (mysqli_query($conexion, $insertar)) {
                    $mensaje = "Acudiente agregado correctamente";
                } else {
                    $mensaje = "Error al agregar acudiente: " . mysqli_error($conexion);
                }
            }
        }
        
        // Eliminar acudiente
        if (isset($_POST['eliminar'])) {
            $id_acudiente = mysqli_real_escape_string($conexion, $_POST['id_acudiente']);
            
            // Comprobar si tiene estudiantes asociados
            $verificar = "SELECT * FROM estudiante WHERE id_acudiente = $id_acudiente";
            $resultado_verificar = mysqli_query($conexion, $verificar);
            
            if (mysqli_num_rows($resultado_verificar) > 0) {
                $mensaje = "No se puede eliminar este acudiente porque tiene estudiantes asociados";
            } else {
                $eliminar = "DELETE FROM acudiente WHERE id_acudiente = $id_acudiente";
                
                if (mysqli_query($conexion, $eliminar)) {
                    $mensaje = "Acudiente eliminado correctamente";
                } else {
                    $mensaje = "Error al eliminar acudiente: " . mysqli_error($conexion);
                }
            }
        }
        
        // Actualizar acudiente
        if (isset($_POST['actualizar'])) {
            $id_acudiente = mysqli_real_escape_string($conexion, $_POST['id_acudiente']);
            $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
            $apellidos = mysqli_real_escape_string($conexion, $_POST['apellidos']);
            $cedula = mysqli_real_escape_string($conexion, $_POST['cedula']);
            $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
            $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
            $contrasena = mysqli_real_escape_string($conexion, $_POST['contrasena']);
            
            // Verificar si el correo ya existe (pero no es el mismo acudiente)
            $verificar = "SELECT * FROM acudiente WHERE correo = '$correo' AND id_acudiente != $id_acudiente";
            $resultado_verificar = mysqli_query($conexion, $verificar);
            
            if (mysqli_num_rows($resultado_verificar) > 0) {
                $mensaje = "El correo electrónico ya está registrado con otro acudiente";
            } else {
                $actualizar = "UPDATE acudiente SET 
                              nombre = '$nombre', 
                              apellidos = '$apellidos', 
                              cedula = '$cedula', 
                              correo = '$correo', 
                              telefono = '$telefono'";
                
                // Solo actualiza la contraseña si se proporciona una nueva
                if (!empty($contrasena)) {
                    $actualizar .= ", contraseña = '$contrasena'";
                }
                
                $actualizar .= " WHERE id_acudiente = $id_acudiente";
                
                if (mysqli_query($conexion, $actualizar)) {
                    $mensaje = "Acudiente actualizado correctamente";
                } else {
                    $mensaje = "Error al actualizar acudiente: " . mysqli_error($conexion);
                }
            }
        }
    }

    // Obtener datos de acudientes para la tabla
    $query_acudientes = "SELECT * FROM acudiente ORDER BY nombre, apellidos";
    $resultado_acudientes = mysqli_query($conexion, $query_acudientes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Acudientes</title>
</head>
<body>
    <h1>Gestionar Acudientes</h1>
    <hr>
    <?php
        if(isset($datos['nombre']) && isset($datos['apellidos'])) {
            echo 'Administrador: ' . $datos['nombre'] . ' ' . $datos['apellidos'] . ' (' . $nombre_usuario . ')';
        } else {
            echo 'Usuario: ' . $nombre_usuario;
        }
    ?>
    <hr>

    <?php if(!empty($mensaje)): ?>
        <div>
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <h2>Agregar Nuevo Acudiente</h2>
    <form method="POST" action="">
        <div>
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>
        <div>
            <label for="apellidos">Apellidos:</label>
            <input type="text" id="apellidos" name="apellidos" required>
        </div>
        <div>
            <label for="cedula">Cédula:</label>
            <input type="text" id="cedula" name="cedula" required>
        </div>
        <div>
            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" required>
        </div>
        <div>
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>
        </div>
        <div>
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>
        </div>
        <div>
            <button type="submit" name="agregar">Agregar Acudiente</button>
        </div>
    </form>

    <h2>Lista de Acudientes</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Cédula</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($acudiente = mysqli_fetch_assoc($resultado_acudientes)): ?>
            <tr>
                <td><?php echo $acudiente['id_acudiente']; ?></td>
                <td><?php echo $acudiente['nombre']; ?></td>
                <td><?php echo $acudiente['apellidos']; ?></td>
                <td><?php echo $acudiente['cedula']; ?></td>
                <td><?php echo $acudiente['correo']; ?></td>
                <td><?php echo $acudiente['telefono']; ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="id_acudiente" value="<?php echo $acudiente['id_acudiente']; ?>">
                        <button type="submit" name="eliminar">Eliminar</button>
                    </form>
                    
                    <form method="POST" action="">
                        <input type="hidden" name="id_acudiente" value="<?php echo $acudiente['id_acudiente']; ?>">
                        <input type="hidden" name="nombre" value="<?php echo $acudiente['nombre']; ?>">
                        <input type="hidden" name="apellidos" value="<?php echo $acudiente['apellidos']; ?>">
                        <input type="hidden" name="cedula" value="<?php echo $acudiente['cedula']; ?>">
                        <input type="hidden" name="correo" value="<?php echo $acudiente['correo']; ?>">
                        <input type="hidden" name="telefono" value="<?php echo $acudiente['telefono']; ?>">
                        <input type="text" name="contrasena" placeholder="Nueva contraseña">
                        <button type="submit" name="actualizar">Actualizar</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    
    <hr>
    <a href="pagina_administrador.php">Volver al Panel de Administrador</a>
</body>
</html>