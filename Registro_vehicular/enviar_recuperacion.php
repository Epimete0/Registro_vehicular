<?php
session_start();

// Verificar si el correo ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el correo desde el formulario
    $correo = $_POST['correo'];

    try {
        // Conexión a la base de datos
        $db = new PDO('sqlite:' . __DIR__ . '/mibase.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Verificar si el correo existe en la base de datos
        $stmt = $db->prepare("SELECT id_usuario FROM Usuarios WHERE correo = :correo");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Usuario encontrado, generar una nueva contraseña
            $nueva_contraseña = "nueva_contraseña_" . uniqid();  // Esta es solo una ejemplo, puedes cambiarla por algo más seguro

            // Actualizar la contraseña en la base de datos (sin hacer hash)
            $stmt_update = $db->prepare("UPDATE Usuarios SET contraseña = :contraseña WHERE correo = :correo");
            $stmt_update->bindParam(':contraseña', $nueva_contraseña);
            $stmt_update->bindParam(':correo', $correo);
            $stmt_update->execute();

            // Mostrar mensaje de éxito
            echo "<p>Tu contraseña ha sido actualizada correctamente. Tu nueva contraseña es: <strong>" . $nueva_contraseña . "</strong></p>";
            echo "<p><a href='login.php'>Volver al inicio de sesión</a></p>";
        } else {
            // Si el correo no está registrado
            echo "<p>El correo proporcionado no está registrado en el sistema.</p>";
            echo "<p><a href='recuperar_contraseña.php'>Intentar de nuevo</a></p>";
        }

    } catch (PDOException $e) {
        echo "Error al conectar con la base de datos: " . $e->getMessage();
    }
} else {
    // Si no es una solicitud POST, redirigir a la página de recuperación de contraseña
    header("Location: recuperar_contraseña.php");
    exit;
}
?>
