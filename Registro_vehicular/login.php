<?php
session_start();

try {
    // Conexión a SQLite
    $db = new PDO('sqlite:' . __DIR__ . '/mibase.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $error = ''; // Variable para manejar el mensaje de error

    // Verificar si se envió el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $correo = $_POST['correo'];
        $contrasena = $_POST['contrasena'];

        // Consulta para obtener el usuario basado en el correo
        $sql = "SELECT id_usuario, nombre, correo, contraseña FROM Usuarios WHERE correo = :correo";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();

        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Comparar la contraseña ingresada con la que está en la base de datos
            if ($contrasena == $row['contraseña']) {
                // Login exitoso
                $_SESSION['user_id'] = $row['id_usuario'];
                $_SESSION['user_name'] = $row['nombre'];

                // Redirigir al dashboard
                header("Location: dashboard.php");
                exit;
            } else {
                // Contraseña incorrecta
                $error = "Contraseña incorrecta.";
            }
        } else {
            // Usuario no encontrado
            $error = "Correo electrónico no encontrado.";
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
    <title>Login</title>
    <link rel="stylesheet" href="css\login.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($error)) echo "<p class='error-message'>$error</p>"; ?>
        <form method="POST">
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required>
            
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>
            
            <button type="submit">Iniciar Sesión</button>
        </form>
        <div class="additional-options">
            <p>¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
            <p>¿Olvidaste tu contraseña? <a href="recuperar_password.php">Recupérala aquí</a></p>
        </div>
    </div>
</body>
</html>