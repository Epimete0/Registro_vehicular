<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

// Conexión a la base de datos (Usando PDO)
try {
    $db = new PDO('sqlite:' . __DIR__ . '/mibase.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener el id del usuario logueado
    $user_id = $_SESSION['user_id'];

    // Obtener los datos del usuario logueado
    $stmt = $db->prepare("SELECT nombre, correo FROM Usuarios WHERE id_usuario = :id_usuario");
    $stmt->bindParam(':id_usuario', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Procesar el formulario si se envía
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $año = $_POST['año'];
        $matricula = $_POST['matricula'];
        $imagen = $_POST['imagen']; // Imagen puede ser una URL o nombre de archivo

        // Validar campos
        if (empty($marca) || empty($modelo) || empty($año) || empty($matricula)) {
            $error = 'Todos los campos son requeridos.';
        } else {
            // Insertar el nuevo vehículo en la base de datos
            $stmt = $db->prepare("INSERT INTO Vehiculos (id_usuario, marca, modelo, año, matricula, imagen) VALUES (:id_usuario, :marca, :modelo, :año, :matricula, :imagen)");
            $stmt->bindParam(':id_usuario', $user_id);
            $stmt->bindParam(':marca', $marca);
            $stmt->bindParam(':modelo', $modelo);
            $stmt->bindParam(':año', $año);
            $stmt->bindParam(':matricula', $matricula);
            $stmt->bindParam(':imagen', $imagen);

            if ($stmt->execute()) {
                $success = 'Vehículo agregado con éxito.';
            } else {
                $error = 'Hubo un problema al agregar el vehículo.';
            }
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
    <title>Agregar Vehículo - Sistema de Vehículos</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css\agregar_vehiculo.css">
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</head>
<body>

<div class="app-bar">
    <button id="menu-toggle" class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>
    <h1>Agregar Vehículo</h1>
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
        <a href="ver_documentos.php" class="drawer-item"><i class="fas fa-file-alt"></i> Ver Documentos</a>
        <a href="login.php" class="drawer-item"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </div>
</nav>

<div class="content">
    <div class="card">
        <h2>Detalles del Vehículo</h2>

        <!-- Mostrar mensajes de error o éxito -->
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <!-- Formulario de agregar vehículo -->
        <form method="POST">
            <div class="input-group">
                <label for="marca">Marca</label>
                <input type="text" id="marca" name="marca" required>
            </div>

            <div class="input-group">
                <label for="modelo">Modelo</label>
                <input type="text" id="modelo" name="modelo" required>
            </div>

            <div class="input-group">
                <label for="año">Año</label>
                <input type="number" id="año" name="año" required>
            </div>

            <div class="input-group">
                <label for="matricula">Matrícula</label>
                <input type="text" id="matricula" name="matricula" required>
            </div>

            <div class="input-group">
                <label for="imagen">Imagen (URL)</label>
                <input type="text" id="imagen" name="imagen">
            </div>

            <button type="submit" class="button">Agregar Vehículo</button>
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
