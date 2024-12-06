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

    // Verificar si se pasó el id_vehiculo
    if (isset($_GET['id_vehiculo']) && !empty($_GET['id_vehiculo'])) {
        $id_vehiculo = $_GET['id_vehiculo'];
    } else {
        header("Location: ver_vehiculos.php");
        exit;
    }

    // Procesar el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fecha = $_POST['fecha'];
        $tipo = $_POST['tipo'];
        $kilometraje = $_POST['kilometraje'];
        $costo = $_POST['costo'];
        $taller = $_POST['taller'];

        $stmt = $db->prepare("INSERT INTO Mantenciones (id_vehiculo, fecha, tipo, kilometraje, costo, taller) 
                              VALUES (:id_vehiculo, :fecha, :tipo, :kilometraje, :costo, :taller)");
        $stmt->bindParam(':id_vehiculo', $id_vehiculo);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':kilometraje', $kilometraje);
        $stmt->bindParam(':costo', $costo);
        $stmt->bindParam(':taller', $taller);

        if ($stmt->execute()) {
            header("Location: ver_mantenciones.php?id_vehiculo=" . htmlspecialchars($id_vehiculo));
            exit;
        } else {
            $error = "Error al guardar la mantención.";
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
    <title>Agregar Mantención</title>
    <link rel="stylesheet" href="css/mantencion.css">
</head>
<body>

<div class="content">
    <h2>Agregar Mantención</h2>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form action="" method="POST">
        <label for="fecha">Fecha:</label>
        <input type="date" id="fecha" name="fecha" required>
        
        <label for="tipo">Tipo:</label>
        <input type="text" id="tipo" name="tipo" placeholder="Ejemplo: Cambio de aceite" required>
        
        <label for="kilometraje">Kilometraje:</label>
        <input type="number" id="kilometraje" name="kilometraje" placeholder="Ejemplo: 50000" required>
        
        <label for="costo">Costo:</label>
        <input type="number" id="costo" name="costo" placeholder="Ejemplo: 15000" required>
        
        <label for="taller">Taller:</label>
        <input type="text" id="taller" name="taller" placeholder="Nombre del taller" required>
        
        <button type="submit">Guardar Mantención</button>
        <a href="ver_mantenciones.php?id_vehiculo=<?php echo htmlspecialchars($id_vehiculo); ?>" class="button">Cancelar</a>
    </form>
</div>

</body>
</html>
