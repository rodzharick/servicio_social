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
    $busqueda = '';
    $filtro = 'nombre';

    // Manejar búsqueda
    if (isset($_GET['busqueda'])) {
        $busqueda = mysqli_real_escape_string($conexion, $_GET['busqueda']);
    }
    
    if (isset($_GET['filtro'])) {
        $filtro = mysqli_real_escape_string($conexion, $_GET['filtro']);
        if (!in_array($filtro, ['nombre', 'grado'])) {
            $filtro = 'nombre';
        }
    }

    // Procesar formularios
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Agregar nuevo estudiante
        if (isset($_POST['agregar'])) {
            $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
            $apellidos = mysqli_real_escape_string($conexion, $_POST['apellidos']);
            $docc_ident = mysqli_real_escape_string($conexion, $_POST['docc_ident']);
            $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
            $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
            $id_acudiente = mysqli_real_escape_string($conexion, $_POST['id_acudiente']);
            $id_grado = mysqli_real_escape_string($conexion, $_POST['id_grado']);
            $contrasena = mysqli_real_escape_string($conexion, $_POST['contrasena']);
            
            // Verificar si el correo ya existe
            $verificar = "SELECT * FROM estudiante WHERE correo = '$correo'";
            $resultado_verificar = mysqli_query($conexion, $verificar);
            
            if (mysqli_num_rows($resultado_verificar) > 0) {
                $mensaje = "El correo electrónico ya está registrado";
            } else {
                $insertar = "INSERT INTO estudiante (nombre, apellidos, docc_ident, telefono, correo, id_acudiente, contraseña) 
                            VALUES ('$nombre', '$apellidos', '$docc_ident', '$telefono', '$correo', '$id_acudiente', '$contrasena')";
                
                if (mysqli_query($conexion, $insertar)) {
                    // Obtener el ID del estudiante recién insertado
                    $id_estudiante = mysqli_insert_id($conexion);
                    
                    // Insertar en la tabla grupo_estudiante para asociar con el grado
                    if (!empty($id_grado)) {
                        $insertar_grupo = "INSERT INTO grupo_estudiante (id_estudiante, id_grupo) 
                                          VALUES ($id_estudiante, $id_grado)";
                        mysqli_query($conexion, $insertar_grupo);
                    }
                    
                    $mensaje = "Estudiante agregado correctamente";
                } else {
                    $mensaje = "Error al agregar estudiante: " . mysqli_error($conexion);
                }
            }
        }
        
        // Eliminar estudiante
        if (isset($_POST['eliminar'])) {
            $id_estudiante = mysqli_real_escape_string($conexion, $_POST['id_estudiante']);
            
            // Primero eliminar las relaciones en grupo_estudiante
            $eliminar_grupo = "DELETE FROM grupo_estudiante WHERE id_estudiante = $id_estudiante";
            mysqli_query($conexion, $eliminar_grupo);
            
            // Luego eliminar el estudiante
            $eliminar = "DELETE FROM estudiante WHERE id_estudiante = $id_estudiante";
            
            if (mysqli_query($conexion, $eliminar)) {
                $mensaje = "Estudiante eliminado correctamente";
            } else {
                $mensaje = "Error al eliminar estudiante: " . mysqli_error($conexion);
            }
        }
        
        // Actualizar estudiante
        if (isset($_POST['actualizar'])) {
            $id_estudiante = mysqli_real_escape_string($conexion, $_POST['id_estudiante']);
            $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
            $apellidos = mysqli_real_escape_string($conexion, $_POST['apellidos']);
            $docc_ident = mysqli_real_escape_string($conexion, $_POST['docc_ident']);
            $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
            $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
            $id_acudiente = mysqli_real_escape_string($conexion, $_POST['id_acudiente']);
            $id_grado = mysqli_real_escape_string($conexion, $_POST['id_grado']);
            $contrasena = mysqli_real_escape_string($conexion, $_POST['contrasena']);
            
            // Verificar si el correo ya existe (pero no es el mismo estudiante)
            $verificar = "SELECT * FROM estudiante WHERE correo = '$correo' AND id_estudiante != $id_estudiante";
            $resultado_verificar = mysqli_query($conexion, $verificar);
            
            if (mysqli_num_rows($resultado_verificar) > 0) {
                $mensaje = "El correo electrónico ya está registrado con otro estudiante";
            } else {
                $actualizar = "UPDATE estudiante SET 
                              nombre = '$nombre', 
                              apellidos = '$apellidos', 
                              docc_ident = '$docc_ident', 
                              telefono = '$telefono', 
                              correo = '$correo', 
                              id_acudiente = '$id_acudiente'";
                
                // Solo actualiza la contraseña si se proporciona una nueva
                if (!empty($contrasena)) {
                    $actualizar .= ", contraseña = '$contrasena'";
                }
                
                $actualizar .= " WHERE id_estudiante = $id_estudiante";
                
                if (mysqli_query($conexion, $actualizar)) {
                    // Actualizar relación con grado
                    if (!empty($id_grado)) {
                        // Verificar si ya existe una relación
                        $verificar_grupo = "SELECT * FROM grupo_estudiante WHERE id_estudiante = $id_estudiante";
                        $resultado_verificar_grupo = mysqli_query($conexion, $verificar_grupo);
                        
                        if (mysqli_num_rows($resultado_verificar_grupo) > 0) {
                            // Actualizar relación existente
                            $actualizar_grupo = "UPDATE grupo_estudiante SET id_grupo = $id_grado 
                                               WHERE id_estudiante = $id_estudiante";
                            mysqli_query($conexion, $actualizar_grupo);
                        } else {
                            // Crear nueva relación
                            $insertar_grupo = "INSERT INTO grupo_estudiante (id_estudiante, id_grupo) 
                                              VALUES ($id_estudiante, $id_grado)";
                            mysqli_query($conexion, $insertar_grupo);
                        }
                    }
                    
                    $mensaje = "Estudiante actualizado correctamente";
                } else {
                    $mensaje = "Error al actualizar estudiante: " . mysqli_error($conexion);
                }
            }
        }
    }

    // Obtener lista de acudientes para el select
    $query_acudientes = "SELECT id_acudiente, nombre, apellidos FROM acudiente ORDER BY nombre, apellidos";
    $resultado_acudientes = mysqli_query($conexion, $query_acudientes);

    // Obtener lista de grados para el select
    $query_grados = "SELECT id_grado, nombre FROM grado ORDER BY nombre";
    $resultado_grados = mysqli_query($conexion, $query_grados);

    // Construir la consulta para obtener estudiantes con filtro de búsqueda
    $query_estudiantes = "SELECT e.*, a.nombre as nombre_acudiente, a.apellidos as apellidos_acudiente,
                         g.nombre as nombre_grado, ge.id_grupo
                         FROM estudiante e 
                         LEFT JOIN acudiente a ON e.id_acudiente = a.id_acudiente 
                         LEFT JOIN grupo_estudiante ge ON e.id_estudiante = ge.id_estudiante
                         LEFT JOIN grupo gr ON ge.id_grupo = gr.id_grupo
                         LEFT JOIN grado g ON gr.id_grado = g.id_grado";
    
    // Aplicar filtro de búsqueda si existe
    if (!empty($busqueda)) {
        if ($filtro == 'nombre') {
            $query_estudiantes .= " WHERE e.nombre LIKE '%$busqueda%' OR e.apellidos LIKE '%$busqueda%'";
        } else if ($filtro == 'grado') {
            $query_estudiantes .= " WHERE g.nombre LIKE '%$busqueda%'";
        }
    }
    
    $query_estudiantes .= " ORDER BY e.nombre, e.apellidos";
    $resultado_estudiantes = mysqli_query($conexion, $query_estudiantes);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Estudiantes</title>
    
</head>
<body>
    <h1>Gestionar Estudiantes</h1>
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
        <div class="mensaje">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>

    <h2>Agregar Nuevo Estudiante</h2>
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
            <label for="docc_ident">Documento de Identidad:</label>
            <input type="text" id="docc_ident" name="docc_ident" required>
        </div>
        <div>
            <label for="telefono">Teléfono:</label>
            <input type="text" id="telefono" name="telefono" required>
        </div>
        <div>
            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" required>
        </div>
        <div>
            <label for="id_acudiente">Acudiente:</label>
            <select id="id_acudiente" name="id_acudiente">
                <option value="">Seleccione un acudiente</option>
                <?php while($acudiente = mysqli_fetch_assoc($resultado_acudientes)): ?>
                    <option value="<?php echo $acudiente['id_acudiente']; ?>">
                        <?php echo $acudiente['nombre'] . ' ' . $acudiente['apellidos']; ?>
                    </option>
                <?php endwhile; ?>
                <?