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

    // Obtener los vehículos registrados por el usuario
    $stmt_vehiculos = $db->prepare("SELECT marca, modelo, matricula FROM Vehiculos WHERE id_usuario = :id_usuario");
    $stmt_vehiculos->bindParam(':id_usuario', $user_id);
    $stmt_vehiculos->execute();
    $vehiculos = $stmt_vehiculos->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Dashboard - Sistema de Vehículos</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css\dashboard.css">
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="app-bar">
    <button id="menu-toggle" class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>
    <h1>Dashboard</h1>
</div>

<nav class="drawer" id="drawer">
    <div class="drawer-header">
        <h2><?php echo htmlspecialchars($user['nombre']); ?></h2>
        <p><?php echo htmlspecialchars($user['correo']); ?></p>
    </div>
    <div class="drawer-content">
        <a href="dashboard.php" class="drawer-item"><i class="fas fa-home"></i> Inicio</a>
        <a href="perfil.php" class="drawer-item"><i class="fas fa-car"></i> Perfil</a>
        <a href="ver_vehiculos.php" class="drawer-item"><i class="fas fa-car"></i> Ver Vehículos</a>
        <a href="agregar_vehiculo.php" class="drawer-item"><i class="fas fa-plus-circle"></i> Agregar Vehículo</a>
        <a href="ver_mantenciones.php" class="drawer-item"><i class="fas fa-plus-circle"></i> Mantenciones</a>
        <a href="ver_usuario.php" class="drawer-item"><i class="fas fa-car"></i> Mostrar Usuarios</a>

        <a href="ver_documentos.php" class="drawer-item"><i class="fas fa-file-alt"></i> Ver Documentos</a>
        <a href="login.php" class="drawer-item"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </div>
</nav>

<div class="content">
    <div class="card">
        <h2>Bienvenid, <?php echo htmlspecialchars($user['nombre']); ?>!</h2>
        <p>Aquí tienes un resumen de tus vehículos registrados.</p>
    </div>

    <div class="card">
        <h2>Vehículos Registrados</h2>
        <?php if (count($vehiculos) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Matrícula</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehiculos as $vehiculo): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($vehiculo['marca']); ?></td>
                            <td><?php echo htmlspecialchars($vehiculo['modelo']); ?></td>
                            <td><?php echo htmlspecialchars($vehiculo['matricula']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tienes vehículos registrados.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const drawer = document.getElementById('drawer');
        const menuToggle = document.getElementById('menu-toggle');

        menuToggle.addEventListener('click', () => {
            drawer.classList.toggle('open');
        });

        // Cierra el drawer si se hace clic fuera de él
        document.addEventListener('click', (event) => {
            if (!drawer.contains(event.target) && !menuToggle.contains(event.target)) {
                drawer.classList.remove('open');
            }
        });
    });
</script>

</body>
</html>

