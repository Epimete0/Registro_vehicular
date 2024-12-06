<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <h1>Recuperar Contraseña</h1>
        <form method="POST" action="enviar_recuperacion.php">
            <div class="form-group">
                <label for="correo">Correo electrónico</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div class="form-actions">
                <button type="submit">Enviar Enlace</button>
            </div>
        </form>
        <p><a href="login.php">Volver al inicio de sesión</a></p>
    </div>
</body>
</html>
