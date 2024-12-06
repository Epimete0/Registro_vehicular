<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

try {
    // Conexión a la base de datos
    $db = new PDO('sqlite:' . __DIR__ . '/mibase.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener los datos del usuario logueado
    $user_id = $_SESSION['user_id'];
    $stmt_user = $db->prepare("SELECT nombre, correo FROM Usuarios WHERE id_usuario = :id_usuario");
    $stmt_user->bindParam(':id_usuario', $user_id);
    $stmt_user->execute();
    $user = $stmt_user->fetch(PDO::FETCH_ASSOC);

    // Verificar si se ha pasado el parámetro id_vehiculo
    if (isset($_GET['id_vehiculo'])) {
        $id_vehiculo = $_GET['id_vehiculo'];

        // Obtener los datos del vehículo
        $stmt_vehiculo = $db->prepare("SELECT * FROM Vehiculos WHERE id_vehiculo = :id_vehiculo AND id_usuario = :id_usuario");
        $stmt_vehiculo->bindParam(':id_vehiculo', $id_vehiculo);
        $stmt_vehiculo->bindParam(':id_usuario', $user_id);
        $stmt_vehiculo->execute();
        $vehiculo = $stmt_vehiculo->fetch(PDO::FETCH_ASSOC);

        // Verificar si el vehículo existe
        if (!$vehiculo) {
            header("Location: ver_vehiculos.php");
            exit;
        }

        // Si se ha enviado el formulario para modificar el vehículo
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $marca = $_POST['marca'];
            $modelo = $_POST['modelo'];
            $año = $_POST['año'];
            $matricula = $_POST['matricula'];

            // Validar que los campos no estén vacíos
            if (!empty($marca) && !empty($modelo) && !empty($año) && !empty($matricula)) {
                // Actualizar los datos del vehículo en la base de datos
                $stmt_update = $db->prepare("UPDATE Vehiculos SET marca = :marca, modelo = :modelo, año = :año, matricula = :matricula WHERE id_vehiculo = :id_vehiculo AND id_usuario = :id_usuario");
                $stmt_update->bindParam(':marca', $marca);
                $stmt_update->bindParam(':modelo', $modelo);
                $stmt_update->bindParam(':año', $año);
                $stmt_update->bindParam(':matricula', $matricula);
                $stmt_update->bindParam(':id_vehiculo', $id_vehiculo);
                $stmt_update->bindParam(':id_usuario', $user_id);
                $stmt_update->execute();

                // Redirigir a la página de vehículos después de la actualización
                header("Location: ver_vehiculos.php");
                exit;
            } else {
                $error_message = "Todos los campos son obligatorios.";
            }
        }
    } else {
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
    <title>Modificar Vehículo</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="app-bar">
    <button id="menu-toggle" class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>
    <h1>Modificar Vehículo</h1>
</div>

<nav class="drawer" id="drawer">
    <div class="drawer-header">
        <h2><?php echo htmlspecialchars($user['nombre']); ?></h2>
        <p><?php echo htmlspecialchars($user['correo']); ?></p>
    </div>
    <div class="drawer-content">
        <a href="dashboard.php" class="drawer-item"><i class="fas fa-home"></i> Inicio</a>
        <a href="ver_vehiculos.php" class="drawer-item"><i class="fas fa-car"></i> Ver Vehículos</a>
        <a href="agregar_vehiculo.php" class="drawer-item"><i class="fas fa-plus-circle"></i> Agregar Vehículo</a>
        <a href="ver_mantenciones.php" class="drawer-item"><i class="fas fa-cogs"></i> Mantenciones</a>
        <a href="ver_documentos.php" class="drawer-item"><i class="fas fa-file-alt"></i> Ver Documentos</a>
        <a href="login.php" class="drawer-item"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </div>
</nav>

<div class="content">
    <div class="card">
        <h2>Modificar Vehículo</h2>

        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Formulario para modificar el vehículo -->
        <form action="modificar_vehiculo.php?id_vehiculo=<?php echo $id_vehiculo; ?>" method="POST">
            <div class="form-group">
                <label for="marca">Marca:</label>
                <input type="text" id="marca" name="marca" value="<?php echo htmlspecialchars($vehiculo['marca']); ?>" required>
            </div>
            <div class="form-group">
                <label for="modelo">Modelo:</label>
                <input type="text" id="modelo" name="modelo" value="<?php echo htmlspecialchars($vehiculo['modelo']); ?>" required>
            </div>
            <div class="form-group">
                <label for="año">Año:</label>
                <input type="number" id="año" name="año" value="<?php echo htmlspecialchars($vehiculo['año']); ?>" required>
            </div>
            <div class="form-group">
                <label for="matricula">Matrícula:</label>
                <input type="text" id="matricula" name="matricula" value="<?php echo htmlspecialchars($vehiculo['matricula']); ?>" required>
            </div>
            <button type="submit" class="button">Actualizar Vehículo</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const drawer = document.getElementById('drawer');
        const menuToggle = document.getElementById('menu-toggle');

        menuToggle.addEventListener('click', () => {
            drawer.classList.toggle('open');
        });

        document.addEventListener('click', (event) => {
            if (!drawer.contains(event.target) && !menuToggle.contains(event.target)) {
                drawer.classList.remove('open');
            }
        });
    });
</script>

</body>
</html>
