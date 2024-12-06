<?php
try {
    // Conexión a SQLite
    $db = new PDO('sqlite:' . __DIR__ . '/mibase.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener las tablas
    $query = "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name;";
    $result = $db->query($query);

    $tables = [];
    foreach ($result as $row) {
        $tables[] = $row['name'];
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
    <title>Datos de la Base de Datos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <div class="container">
        <h1>Datos de la Base de Datos</h1>

        <?php foreach ($tables as $table): ?>
            <div class="table-section">
                <h2>Tabla: <?php echo htmlspecialchars($table); ?></h2>

                <?php
                // Obtener las columnas de la tabla
                $columnsQuery = "PRAGMA table_info($table);";
                $columnsResult = $db->query($columnsQuery);
                $columns = [];
                foreach ($columnsResult as $column) {
                    $columns[] = $column['name'];
                }

                // Obtener los datos de la tabla
                $contentQuery = "SELECT * FROM $table;";
                $contentResult = $db->query($contentQuery);
                ?>

                <table>
                    <thead>
                        <tr>
                            <?php foreach ($columns as $column): ?>
                                <th><?php echo htmlspecialchars($column); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contentResult as $row): ?>
                            <tr>
                                <?php foreach ($row as $cell): ?>
                                    <td><?php echo htmlspecialchars($cell); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
