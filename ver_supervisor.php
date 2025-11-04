<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'modelo/conexion.php';
session_start();

// Verificar conexión
if (!$conexion) {
    die("Error de conexión a la base de datos: " . mysqli_connect_error());
}

// Verificar si existe una sesión de administrador
if (!isset($_SESSION['username'])) {
    header("location: index.php");
    exit();
}

$nombre_usuario = $_SESSION['username'];

// Obtener datos del administrador
$query = "SELECT nombre, apellidos FROM administrador WHERE correo = ?";
$stmt = mysqli_prepare($conexion, $query);
mysqli_stmt_bind_param($stmt, "s", $nombre_usuario);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$datos = mysqli_fetch_array($resultado);

// Inicializar variables
$mensaje = '';
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

// Procesar formulario de búsqueda
if (isset($_GET['buscar'])) {
    $busqueda = mysqli_real_escape_string($conexion, $_GET['busqueda']);
}

// Procesar formularios de edición y eliminación
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Eliminar supervisor
    if (isset($_POST['eliminar'])) {
        $id_supervisor = mysqli_real_escape_string($conexion, $_POST['id_supervisor']);

        $verificar = "SELECT * FROM apoyo WHERE id_supervisor = $id_supervisor";
        $resultado_verificar = mysqli_query($conexion, $verificar);

        if ($resultado_verificar && mysqli_num_rows($resultado_verificar) > 0) {
            $mensaje = "No se puede eliminar este supervisor porque tiene registros asociados";
        } else {
            $eliminar = "DELETE FROM supervisor WHERE id_supervisor = $id_supervisor";
            if (mysqli_query($conexion, $eliminar)) {
                $mensaje = "Supervisor eliminado correctamente";
            } else {
                $mensaje = "Error al eliminar supervisor: " . mysqli_error($conexion);
            }
        }
    }

    // Actualizar supervisor
    if (isset($_POST['actualizar'])) {
        $id_supervisor = mysqli_real_escape_string($conexion, $_POST['id_supervisor']);
        $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
        $apellidos = mysqli_real_escape_string($conexion, $_POST['apellidos']);
        $doc_identidad = mysqli_real_escape_string($conexion, $_POST['doc_identidad']);
        $telefono = mysqli_real_escape_string($conexion, $_POST['telefono']);
        $correo = mysqli_real_escape_string($conexion, $_POST['correo']);
        $contrasena = mysqli_real_escape_string($conexion, $_POST['contrasena']);
        $id_sede = mysqli_real_escape_string($conexion, $_POST['id_sede']);
        $dependencia = mysqli_real_escape_string($conexion, $_POST['dependencia']);

        $verificar = "SELECT * FROM supervisor WHERE correo = '$correo' AND id_supervisor != $id_supervisor";
        $resultado_verificar = mysqli_query($conexion, $verificar);

        if ($resultado_verificar && mysqli_num_rows($resultado_verificar) > 0) {
            $mensaje = "El correo electrónico ya está registrado con otro supervisor";
        } else {
            // Si se proporcionó una nueva contraseña, la encriptamos
            $update_password = "";
            if (!empty($contrasena)) {
                $hash = password_hash($contrasena, PASSWORD_DEFAULT);
                $update_password = ", contrasena = '$hash'";
            }

            $actualizar = "UPDATE supervisor SET 
                          nombre = '$nombre', 
                          apellidos = '$apellidos', 
                          doc_identidad = '$doc_identidad', 
                          telefono = '$telefono',
                          correo = '$correo',
                          id_sede = $id_sede,
                          dependencia = '$dependencia'
                          $update_password
                          WHERE id_supervisor = $id_supervisor";

            if (mysqli_query($conexion, $actualizar)) {
                $mensaje = "Supervisor actualizado correctamente";
            } else {
                $mensaje = "Error al actualizar supervisor: " . mysqli_error($conexion);
            }
        }
    }
}

// Obtener datos de supervisores
$query_supervisores = "SELECT s.*, sd.nombre_sede 
                       FROM supervisor s 
                       LEFT JOIN sede sd ON s.id_sede = sd.id_sede";

if (!empty($busqueda)) {
    $query_supervisores .= " WHERE s.nombre LIKE '%$busqueda%' OR s.apellidos LIKE '%$busqueda%'";
}

$query_supervisores .= " ORDER BY s.nombre, s.apellidos";
$resultado_supervisores = mysqli_query($conexion, $query_supervisores);

// Obtener lista de sedes
$query_sedes = "SELECT id_sede, nombre_sede FROM sede ORDER BY nombre_sede";
$resultado_sedes = mysqli_query($conexion, $query_sedes);

$sedes = [];
if ($resultado_sedes) {
    while ($sede = mysqli_fetch_assoc($resultado_sedes)) {
        $sedes[] = $sede;
    }
}

// Contar supervisores totales
$total_supervisores = 0;
$query_total = "SELECT COUNT(*) as total FROM supervisor";
$resultado_total = mysqli_query($conexion, $query_total);
if ($resultado_total) {
    $datos_total = mysqli_fetch_assoc($resultado_total);
    $total_supervisores = $datos_total['total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="ver.css">
    <title>Ver Supervisores</title>
</head>
<body>
    <h1>Lista de Supervisores</h1>
    <hr>
    <?php
        if (isset($datos['nombre']) && isset($datos['apellidos'])) {
            echo 'Administrador: ' . htmlspecialchars($datos['nombre']) . ' ' . htmlspecialchars($datos['apellidos']) . ' (' . htmlspecialchars($nombre_usuario) . ')';
        } else {
            echo 'Usuario: ' . htmlspecialchars($nombre_usuario);
        }
    ?>
    <hr>

    <?php if (!empty($mensaje)): ?>
        <div style="color: blue; margin: 10px 0;">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div>
        <p>Total de supervisores en la base de datos: <?php echo $total_supervisores; ?></p>
    </div>

    <!-- Formulario de búsqueda -->
    <h2>Buscar Supervisores</h2>
    <form method="GET" action="">
        <label for="busqueda">Buscar por nombre:</label>
        <input type="text" id="busqueda" name="busqueda" value="<?php echo htmlspecialchars($busqueda); ?>">
        <button type="submit" name="buscar">Buscar</button>
        <?php if (!empty($busqueda)): ?>
            <a href="ver_supervisores.php">Limpiar búsqueda</a>
        <?php endif; ?>
    </form>

    <h2>Lista de Supervisores</h2>
    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Documento ID</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Sede</th>
                <th>Dependencia</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($resultado_supervisores && mysqli_num_rows($resultado_supervisores) > 0) {
                while ($supervisor = mysqli_fetch_assoc($resultado_supervisores)): 
            ?>
            <tr>
                <td><?php echo $supervisor['id_supervisor']; ?></td>
                <td><?php echo htmlspecialchars($supervisor['nombre']); ?></td>
                <td><?php echo htmlspecialchars($supervisor['apellidos']); ?></td>
                <td><?php echo htmlspecialchars($supervisor['doc_identidad']); ?></td>
                <td><?php echo htmlspecialchars($supervisor['telefono']); ?></td>
                <td><?php echo htmlspecialchars($supervisor['correo']); ?></td>
                <td><?php echo $supervisor['id_sede'] ? htmlspecialchars($supervisor['nombre_sede']) : "Sin sede asignada"; ?></td>
                <td><?php echo htmlspecialchars($supervisor['dependencia']); ?></td>
                <td>
                    <form method="POST" action="">
                        <input type="hidden" name="id_supervisor" value="<?php echo $supervisor['id_supervisor']; ?>">
                        <button type="submit" name="eliminar">Eliminar</button>
                    </form>

                    <button onclick="mostrarFormularioEdicion(<?php echo $supervisor['id_supervisor']; ?>)">Editar</button>

                    <div id="editar-<?php echo $supervisor['id_supervisor']; ?>" style="display: none; border: 1px solid #ccc; padding: 10px; margin-top: 5px;">
                        <form method="POST" action="">
                            <input type="hidden" name="id_supervisor" value="<?php echo $supervisor['id_supervisor']; ?>">
                            <label>Nombre:</label>
                            <input type="text" name="nombre" value="<?php echo htmlspecialchars($supervisor['nombre']); ?>" required><br>
                            <label>Apellidos:</label>
                            <input type="text" name="apellidos" value="<?php echo htmlspecialchars($supervisor['apellidos']); ?>" required><br>
                            <label>Documento de Identidad:</label>
                            <input type="text" name="doc_identidad" value="<?php echo htmlspecialchars($supervisor['doc_identidad']); ?>" required><br>
                            <label>Teléfono:</label>
                            <input type="text" name="telefono" value="<?php echo htmlspecialchars($supervisor['telefono']); ?>" required><br>
                            <label>Correo:</label>
                            <input type="email" name="correo" value="<?php echo htmlspecialchars($supervisor['correo']); ?>" required><br>
                            <label>Nueva contraseña (opcional):</label>
                            <input type="text" name="contrasena"><br>
                            <label>Sede:</label>
                            <select name="id_sede" required>
                                <option value="">Seleccione una sede</option>
                                <?php foreach ($sedes as $sede): ?>
                                    <option value="<?php echo $sede['id_sede']; ?>" <?php echo ($supervisor['id_sede'] == $sede['id_sede']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($sede['nombre_sede']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select><br>
                            <label>Dependencia:</label>
                            <input type="text" name="dependencia" value="<?php echo htmlspecialchars($supervisor['dependencia']); ?>" required><br>
                            <button type="submit" name="actualizar">Guardar Cambios</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php 
                endwhile;
            } else {
                echo "<tr><td colspan='9'>No se encontraron supervisores</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <hr>
    <a href="gestionar_supervisores.php">Agregar Nuevo Supervisor</a><br>
    <a href="pagina_administrador.php">Volver al Panel de Administrador</a>

    <script>
        function mostrarFormularioEdicion(id) {
            var formulario = document.getElementById('editar-' + id);
            formulario.style.display = (formulario.style.display === 'none') ? 'block' : 'none';
        }
    </script>
</body>
</html>
