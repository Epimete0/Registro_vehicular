<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

try {
    // Conexión a la base de datos (Usando PDO)
    $db = new PDO('sqlite:' . __DIR__ . '/mibase.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener los datos del usuario logueado
    $user_id = $_SESSION['user_id'];
    $stmt = $db->prepare("SELECT nombre, correo FROM Usuarios WHERE id_usuario = :id_usuario");
    $stmt->bindParam(':id_usuario', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Obtener los vehículos registrados por el usuario
    $stmt_vehiculos = $db->prepare("SELECT id_vehiculo FROM Vehiculos WHERE id_usuario = :id_usuario");
    $stmt_vehiculos->bindParam(':id_usuario', $user_id);
    $stmt_vehiculos->execute();
    $vehiculos = $stmt_vehiculos->fetchAll(PDO::FETCH_ASSOC);

    // Obtener los documentos asociados a los vehículos del usuario
    $documentos = [];
    foreach ($vehiculos as $vehiculo) {
        $stmt_documentos = $db->prepare("SELECT id_documento, tipo, fecha_vencimiento, archivo FROM Documentos WHERE id_vehiculo = :id_vehiculo");
        $stmt_documentos->bindParam(':id_vehiculo', $vehiculo['id_vehiculo']);
        $stmt_documentos->execute();
        $documentos = array_merge($documentos, $stmt_documentos->fetchAll(PDO::FETCH_ASSOC));
    }

    // Procesar la eliminación de documentos
    if (isset($_POST['eliminar_documento'])) {
        $id_documento = $_POST['id_documento'];

        // Eliminar documento de la base de datos
        $stmt = $db->prepare("DELETE FROM Documentos WHERE id_documento = :id_documento");
        $stmt->bindParam(':id_documento', $id_documento);
        $stmt->execute();

        header("Location: ver_documentos.php?eliminado=1");
        exit;
    }

    // Procesar la adición de documentos
    if (isset($_POST['agregar_documento'])) {
        $id_vehiculo = $_POST['id_vehiculo'];
        $tipo = $_POST['tipo'];
        $fecha_vencimiento = $_POST['fecha_vencimiento'];
        $nombre_archivo = "documento_placeholder.pdf"; // Nombre ficticio del archivo

        $stmt = $db->prepare("INSERT INTO Documentos (id_vehiculo, tipo, fecha_vencimiento, archivo, historial_versiones) 
                              VALUES (:id_vehiculo, :tipo, :fecha_vencimiento, :archivo, :historial_versiones)");
        $historial_versiones = json_encode([["version" => 1, "archivo" => $nombre_archivo, "fecha" => date('Y-m-d H:i:s')]]);
        $stmt->bindParam(':id_vehiculo', $id_vehiculo);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':fecha_vencimiento', $fecha_vencimiento);
        $stmt->bindParam(':archivo', $nombre_archivo);
        $stmt->bindParam(':historial_versiones', $historial_versiones);

        if ($stmt->execute()) {
            header("Location: ver_documentos.php?agregado=1");
            exit;
        } else {
            echo "Error al guardar el documento en la base de datos.";
        }
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
    <title>Ver Documentos - Sistema de Vehículos</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="app-bar">
    <button id="menu-toggle" class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>
    <h1>Ver Documentos</h1>
</div>

<nav class="drawer" id="drawer">
    <div class="drawer-header">
        <h2><?php echo htmlspecialchars($user['nombre']); ?></h2>
        <p><?php echo htmlspecialchars($user['correo']); ?></p>
    </div>
    <div class="drawer-content">
        <a href="dashboard.php" class="drawer-item"><i class="fas fa-home"></i> Inicio</a>
        <a href="ver_vehiculos.php" class="drawer-item"><i class="fas fa-car"></i> Ver Vehículos</a>
        <a href="ver_documentos.php" class="drawer-item"><i class="fas fa-file-alt"></i> Ver Documentos</a>
        <a href="login.php" class="drawer-item"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </div>
</nav>

<div class="content">
    <!-- Mensajes de éxito -->
    <?php if (isset($_GET['agregado'])): ?>
        <p class="success">Documento agregado exitosamente.</p>
    <?php elseif (isset($_GET['eliminado'])): ?>
        <p class="success">Documento eliminado exitosamente.</p>
    <?php endif; ?>

    <!-- Listar documentos -->
    <div class="card">
        <h2>Documentos Registrados</h2>
        <?php if (count($documentos) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Fecha Vencimiento</th>
                        <th>Archivo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documentos as $documento): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($documento['tipo']); ?></td>
                            <td><?php echo htmlspecialchars($documento['fecha_vencimiento']); ?></td>
                            <td><a href="uploads/<?php echo htmlspecialchars($documento['archivo']); ?>" target="_blank">Ver Archivo</a></td>
                            <td>
                                <form action="ver_documentos.php" method="POST">
                                    <input type="hidden" name="id_documento" value="<?php echo $documento['id_documento']; ?>">
                                    <button type="submit" name="eliminar_documento" class="button" onclick="return confirm('¿Estás seguro de eliminar este documento?')">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tienes documentos registrados.</p>
        <?php endif; ?>
    </div>

    <!-- Formulario para agregar documento -->
    <div class="card">
        <h2>Agregar Documento</h2>
        <form action="ver_documentos.php" method="POST" enctype="multipart/form-data">
            <label for="vehiculo">Vehículo</label>
            <select name="id_vehiculo" id="vehiculo" required>
                <?php foreach ($vehiculos as $vehiculo): ?>
                    <option value="<?php echo htmlspecialchars($vehiculo['id_vehiculo']); ?>">
                        Vehículo ID: <?php echo htmlspecialchars($vehiculo['id_vehiculo']); ?>
                    </option>
                <?php endforeach; ?>
            </select><br>
            <label for="tipo">Tipo</label>
            <input type="text" name="tipo" id="tipo" required><br>
            <label for="fecha_vencimiento">Fecha Vencimiento</label>
            <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" required><br>
            <label for="archivo">Archivo</label>
            <input type="file" name="archivo" id="archivo" required><br>
            <button type="submit" name="agregar_documento" class="button">Agregar Documento</button>
        </form>
    </div>
</div>

<script>
document.getElementById('menu-toggle').addEventListener('click', function() {
    document.getElementById('drawer').classList.toggle('open');
});
</script>

</body>
</html>
