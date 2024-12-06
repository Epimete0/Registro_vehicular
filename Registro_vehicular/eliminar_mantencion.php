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

    // Verificar si se pasó el parámetro id_mantencion
    if (isset($_GET['id_mantencion']) && !empty($_GET['id_mantencion'])) {
        $id_mantencion = $_GET['id_mantencion'];

        // Eliminar la mantención
        $stmt = $db->prepare("DELETE FROM Mantenciones WHERE id_mantencion = :id_mantencion");
        $stmt->bindParam(':id_mantencion', $id_mantencion);
        $stmt->execute();

        // Redirigir de vuelta a la página de mantenciones con un mensaje opcional
        header("Location: ver_mantenciones.php?id_vehiculo=" . htmlspecialchars($_GET['id_vehiculo']) . "&success=1");
        exit;
    } else {
        // Si no se pasó el ID, redirigir con error
        header("Location: ver_vehiculos.php?error=1");
        exit;
    }

} catch (PDOException $e) {
    echo "Error al conectar con la base de datos: " . $e->getMessage();
    exit;
}
?>
