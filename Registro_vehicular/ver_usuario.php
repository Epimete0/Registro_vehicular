<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Conexión a la base de datos
try {
    $db = new PDO('sqlite:' . __DIR__ . '/mibase.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener los datos del usuario logueado
    $user_id = $_SESSION['user_id'];

    // Obtener todos los usuarios que son conductores y excluir al usuario principal
    $stmt = $db->prepare("SELECT u.id_usuario, u.nombre, u.correo, v.marca || ' ' || v.modelo AS auto
                          FROM Usuarios u
                          LEFT JOIN Vehiculos v ON u.id_usuario = v.id_usuario
                          WHERE u.id_usuario != :user_id");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Ver Usuarios</title>
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
        <h2>Bienvenido</h2>
        <p><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
    </div>
    <div class="drawer-content">
        <a href="dashboard.php" class="drawer-item"><i class="fas fa-home"></i> Inicio</a>
        <a href="ver_vehiculos.php" class="drawer-item"><i class="fas fa-car"></i> Ver Vehículos</a>
        <a href="agregar_vehiculo.php" class="drawer-item"><i class="fas fa-plus-circle"></i> Agregar Vehículo</a>
        <a href="ver_documentos.php" class="drawer-item"><i class="fas fa-file-alt"></i> Ver Documentos</a>
        <a href="ver_usuario.php" class="drawer-item"><i class="fas fa-users"></i> Ver Usuarios</a>
        <a href="login.php" class="drawer-item"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </div>
</nav>

<!-- Contenido principal -->
<div class="content">
    <h2>Usuarios Registrados</h2>

    <?php if (isset($usuarios) && count($usuarios) > 0): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Auto Asociado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['auto'] ?? 'Sin Auto Asociado'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se han encontrado usuarios registrados.</p>
    <?php endif; ?>
</div>

</body>
</html>
