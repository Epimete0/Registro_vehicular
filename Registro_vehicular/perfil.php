<?php
session_start();

try {
    // Conexión a SQLite
    $db = new PDO('sqlite:' . __DIR__ . '/mibase.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $error = ''; // Variable para manejar el mensaje de error

    // Verificar si el usuario está logueado
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    // Obtener los datos del usuario logueado
    $user_id = $_SESSION['user_id'];
    $stmt = $db->prepare("SELECT nombre, correo, contraseña FROM Usuarios WHERE id_usuario = :id_usuario");
    $stmt->bindParam(':id_usuario', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Procesar la actualización de la contraseña
    if (isset($_POST['actualizar_contrasena'])) {
        $contrasena_actual = $_POST['contrasena_actual'];
        $nueva_contrasena = $_POST['nueva_contrasena'];
        $confirmar_contrasena = $_POST['confirmar_contrasena'];

        // Asegurarnos de que no haya espacios extra o saltos de línea al comparar las contraseñas
        $contrasena_actual = trim($contrasena_actual);
        $user_contrasena = trim($user['contraseña']);

        // Verificar que la contraseña actual sea correcta
        if ($contrasena_actual === $user_contrasena) {
            // Verificar que las contraseñas coincidan
            if ($nueva_contrasena === $confirmar_contrasena) {
                // Asegurarse también de que no haya espacios o saltos en las nuevas contraseñas
                $nueva_contrasena = trim($nueva_contrasena);

                // Actualizar la contraseña en la base de datos
                $stmt = $db->prepare("UPDATE Usuarios SET contraseña = :nueva_contrasena WHERE id_usuario = :id_usuario");
                $stmt->bindParam(':nueva_contrasena', $nueva_contrasena);
                $stmt->bindParam(':id_usuario', $user_id);
                $stmt->execute();

                $error = "Contraseña actualizada exitosamente.";
            } else {
                $error = "Las contraseñas no coinciden.";
            }
        } else {
            $error = "Contraseña actual incorrecta.";
        }
    }

    // Obtener el id_vehiculo del usuario
    $stmt_vehiculos = $db->prepare("SELECT id_vehiculo FROM Vehiculos WHERE id_usuario = :id_usuario");
    $stmt_vehiculos->bindParam(':id_usuario', $user_id);
    $stmt_vehiculos->execute();
    $vehiculos = $stmt_vehiculos->fetchAll(PDO::FETCH_ASSOC);

    // Procesar la subida de documentos (Licencia de conducir)
    if (isset($_FILES['licencia'])) {
        if ($_FILES['licencia']['error'] == 0) {
            // Validar y guardar el archivo
            $licencia = $_FILES['licencia'];
            $licencia_nombre = uniqid() . "_" . basename($licencia['name']);
            $upload_dir = "uploads/";

            if (move_uploaded_file($licencia['tmp_name'], $upload_dir . $licencia_nombre)) {
                // Insertar el documento en la base de datos
                foreach ($vehiculos as $vehiculo) {
                    $stmt = $db->prepare("INSERT INTO Documentos (id_vehiculo, tipo, archivo) VALUES (:id_vehiculo, 'Licencia de Conducir', :archivo)");
                    $stmt->bindParam(':id_vehiculo', $vehiculo['id_vehiculo']);
                    $stmt->bindParam(':archivo', $licencia_nombre);
                    $stmt->execute();
                }

                $error = "Licencia de conducir agregada exitosamente.";
            } else {
                $error = "Error al subir la licencia.";
            }
        } else {
            $error = "Error al subir el archivo.";
        }
    }

} catch (PDOException $e) {
    echo "Error al conectar con la base de datos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="css/perfil.css">
    <!-- Agregar FontAwesome para los íconos -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

    <!-- Barra de Aplicación -->
    <div class="app-bar">
        <button id="menu-toggle" class="menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <h1>Dashboard</h1>
    </div>

    <!-- Barra Lateral -->
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
            <a href="ver_documentos.php" class="drawer-item"><i class="fas fa-file-alt"></i> Ver Documentos</a>
            <a href="login.php" class="drawer-item"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="content">
        <div class="card">
        </div>

        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>

        <!-- Formulario de actualización de contraseña -->
        <h3>Actualizar Contraseña</h3>
        <form method="POST">
            <label for="contrasena_actual">Contraseña Actual:</label>
            <input type="password" id="contrasena_actual" name="contrasena_actual" required>
            
            <label for="nueva_contrasena">Nueva Contraseña:</label>
            <input type="password" id="nueva_contrasena" name="nueva_contrasena" required>
            
            <label for="confirmar_contrasena">Confirmar Nueva Contraseña:</label>
            <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>
            
            <button type="submit" name="actualizar_contrasena">Actualizar Contraseña</button>
        </form>

        <!-- Formulario para subir licencia -->
        <h3>Subir Licencia de Conducir</h3>
        <form method="POST" enctype="multipart/form-data">
            <label for="licencia">Licencia de Conducir (Archivo PDF o Imagen):</label>
            <input type="file" name="licencia" id="licencia" required>
            <button type="submit">Subir Licencia</button>
        </form>

        <!-- Documentos Subidos -->
        <h3>Documentos Subidos</h3>
        <?php
        // Obtener los documentos del usuario
        $stmt_documentos = $db->prepare("SELECT id_documento, tipo, archivo FROM Documentos WHERE id_vehiculo IN (SELECT id_vehiculo FROM Vehiculos WHERE id_usuario = :id_usuario)");
        $stmt_documentos->bindParam(':id_usuario', $user_id);
        $stmt_documentos->execute();
        $documentos = $stmt_documentos->fetchAll(PDO::FETCH_ASSOC);

        if (count($documentos) > 0):
            echo "<ul>";
            foreach ($documentos as $documento):
                echo "<li>" . htmlspecialchars($documento['tipo']) . " - <a href='uploads/" . htmlspecialchars($documento['archivo']) . "' target='_blank'>Ver Documento</a></li>";
            endforeach;
            echo "</ul>";
        else:
            echo "<p>No tienes documentos subidos.</p>";
        endif;
        ?>
    </div>

    <!-- Agregar el script para manejar el toggle del menú lateral -->
    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const drawer = document.getElementById('drawer');

        menuToggle.addEventListener('click', () => {
            drawer.classList.toggle('open');
        });
    </script>

</body>
</html>
