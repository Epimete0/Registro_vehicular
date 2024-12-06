<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $db = new PDO('sqlite:' . __DIR__ . '/mibase.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $password = $_POST['password']; // Usar la contraseña tal cual como se ingresa

        // Nota que no estamos utilizando password_hash() para esta versión
        $stmt = $db->prepare("INSERT INTO Usuarios (nombre, correo, contraseña) VALUES (:nombre, :correo, :contraseña)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':contraseña', $password);
        $stmt->execute();

        header("Location: login.php?registro_exitoso=1");
        exit;
    } catch (PDOException $e) {
        $error = "Error al registrar usuario: " . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <h1>Registro</h1>
        <?php if (isset($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="registro.php">
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-actions">
                <button type="submit">Registrar</button>
            </div>
        </form>
    </div>
</body>
</html>
