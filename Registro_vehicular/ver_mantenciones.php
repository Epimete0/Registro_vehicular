<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos (Usando PDO)
try {
    $db = new PDO('sqlite:' . __DIR__ . '/mibase.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener los datos del usuario logueado
    $user_id = $_SESSION['user_id'];
    $stmt = $db->prepare("SELECT nombre, correo FROM Usuarios WHERE id_usuario = :id_usuario");
    $stmt->bindParam(':id_usuario', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificar si se pasó el parámetro id_vehiculo en la URL
    if (isset($_GET['id_vehiculo']) && !empty($_GET['id_vehiculo'])) {
        $id_vehiculo = $_GET['id_vehiculo'];

        // Obtener las mantenciones para el vehículo
        $stmt_mantenciones = $db->prepare("SELECT * FROM Mantenciones WHERE id_vehiculo = :id_vehiculo");
        $stmt_mantenciones->bindParam(':id_vehiculo', $id_vehiculo);
        $stmt_mantenciones->execute();
        $mantenciones = $stmt_mantenciones->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Si no se pasa id_vehiculo, redirigir a la página de vehículos
        header("Location: ver_vehiculos.php");
        exit;
    }

} catch (PDOException $e) {
    echo "Error al conectar con la base de datos: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Mantenciones</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<!-- Barra de navegación -->
<div class="app-bar">
    <button id="menu-toggle" class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>
    <h1>Dashboard</h1>
</div>

<!-- Menú lateral -->
<nav class="drawer" id="drawer">
    <div class="drawer-header">
        <h2><?php echo htmlspecialchars($user['nombre']); ?></h2>
        <p><?php echo htmlspecialchars($user['correo']); ?></p>
    </div>
    <div class="drawer-content">
        <a href="dashboard.php" class="drawer-item"><i class="fas fa-home"></i> Inicio</a>
        <a href="ver_vehiculos.php" class="drawer-item"><i class="fas fa-car"></i> Ver Vehículos</a>
        <a href="agregar_vehiculo.php" class="drawer-item"><i class="fas fa-plus-circle"></i> Agregar Vehículo</a>
        <a href="ver_documentos.php" class="drawer-item"><i class="fas fa-file-alt"></i> Ver Documentos</a>
        <a href="login.php" class="drawer-item"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </div>
</nav>

<!-- Contenido principal -->
<div class="content">
    <h2>Mantenciones Realizadas</h2>
    <!-- Tabla de mantenciones -->
    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Kilometraje</th>
                <th>Costo</th>
                <th>Taller</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
    <?php
    if (count($mantenciones) > 0) {
        foreach ($mantenciones as $mantencion) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($mantencion['fecha']) . "</td>";
            echo "<td>" . htmlspecialchars($mantencion['tipo']) . "</td>";
            echo "<td>" . htmlspecialchars($mantencion['kilometraje']) . "</td>";
            echo "<td>" . htmlspecialchars($mantencion['costo']) . "</td>";
            echo "<td>" . htmlspecialchars($mantencion['taller']) . "</td>";
            echo "<td>
                    <a href='editar_mantencion.php?id_mantencion=" . $mantencion['id_mantencion'] . "' class='button'>Editar</a>
                    <a href='eliminar_mantencion.php?id_mantencion=" . $mantencion['id_mantencion'] . "&id_vehiculo=" . $id_vehiculo . "' 
                       class='button' 
                       onclick=\"return confirm('¿Estás seguro de que deseas eliminar esta mantención?');\">Eliminar</a>
                  </td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>No se han encontrado mantenciones para este vehículo.</td></tr>";
    }
    ?>
</tbody>

    </table>
    <a href="agregar_mantencion.php?id_vehiculo=<?php echo htmlspecialchars($id_vehiculo); ?>" class="button">Agregar Mantención</a>

</div>

</body>
</html>
