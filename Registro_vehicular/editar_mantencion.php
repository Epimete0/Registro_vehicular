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

    // Verificar si se pasó el parámetro id_mantencion en la URL
    if (isset($_GET['id_mantencion']) && !empty($_GET['id_mantencion'])) {
        $id_mantencion = $_GET['id_mantencion'];

        // Obtener la mantención a editar
        $stmt_mantencion = $db->prepare("SELECT * FROM Mantenciones WHERE id_mantencion = :id_mantencion");
        $stmt_mantencion->bindParam(':id_mantencion', $id_mantencion);
        $stmt_mantencion->execute();
        $mantencion = $stmt_mantencion->fetch(PDO::FETCH_ASSOC);

        if (!$mantencion) {
            // Si no se encuentra la mantención, redirigir al listado
            header("Location: ver_mantenciones.php");
            exit;
        }

        // Procesar la actualización de la mantención
        if (isset($_POST['actualizar_mantencion'])) {
            $fecha = $_POST['fecha'];
            $tipo = $_POST['tipo'];
            $kilometraje = $_POST['kilometraje'];
            $costo = $_POST['costo'];
            $taller = $_POST['taller'];

            // Actualizar la mantención en la base de datos
            $stmt_update = $db->prepare("UPDATE Mantenciones SET fecha = :fecha, tipo = :tipo, kilometraje = :kilometraje, costo = :costo, taller = :taller WHERE id_mantencion = :id_mantencion");
            $stmt_update->bindParam(':fecha', $fecha);
            $stmt_update->bindParam(':tipo', $tipo);
            $stmt_update->bindParam(':kilometraje', $kilometraje);
            $stmt_update->bindParam(':costo', $costo);
            $stmt_update->bindParam(':taller', $taller);
            $stmt_update->bindParam(':id_mantencion', $id_mantencion);
            $stmt_update->execute();

            // Redirigir a la página de mantenciones
            header("Location: ver_mantenciones.php?id_vehiculo=" . $mantencion['id_vehiculo']);
            exit;
        }

    } else {
        // Si no se pasa id_mantencion, redirigir a la página de mantenciones
        header("Location: ver_mantenciones.php");
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
    <title>Editar Mantención</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<!-- Barra de navegación -->
<div class="app-bar">
    <button id="menu-toggle" class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>
    <h1>Editar Mantención</h1>
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
    <h2>Modificar Mantención</h2>
    <form action="editar_mantencion.php?id_mantencion=<?php echo $id_mantencion; ?>" method="POST">
        <label for="fecha">Fecha</label>
        <input type="date" name="fecha" value="<?php echo htmlspecialchars($mantencion['fecha']); ?>" required><br>

        <label for="tipo">Tipo</label>
        <input type="text" name="tipo" value="<?php echo htmlspecialchars($mantencion['tipo']); ?>" required><br>

        <label for="kilometraje">Kilometraje</label>
        <input type="number" name="kilometraje" value="<?php echo htmlspecialchars($mantencion['kilometraje']); ?>" required><br>

        <label for="costo">Costo</label>
        <input type="number" step="0.01" name="costo" value="<?php echo htmlspecialchars($mantencion['costo']); ?>" required><br>

        <label for="taller">Taller</label>
        <input type="text" name="taller" value="<?php echo htmlspecialchars($mantencion['taller']); ?>" required><br>

        <button type="submit" name="actualizar_mantencion" class="button">Actualizar</button>
    </form>
</div>

</body>
</html>
