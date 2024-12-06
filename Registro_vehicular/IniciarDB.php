<?php
try {
    // Conexión a SQLite
    $db = new PDO('sqlite:' . __DIR__ . '/mibase.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Crear las tablas si no existen
    $schema = "
        CREATE TABLE IF NOT EXISTS Usuarios (
            id_usuario INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            correo TEXT NOT NULL,
            contraseña TEXT NOT NULL,
            fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS Vehiculos (
            id_vehiculo INTEGER PRIMARY KEY AUTOINCREMENT,
            id_usuario INTEGER NOT NULL,
            marca TEXT NOT NULL,
            modelo TEXT NOT NULL,
            año INTEGER NOT NULL,
            matricula TEXT UNIQUE NOT NULL,
            imagen TEXT,
            FOREIGN KEY (id_usuario) REFERENCES Usuarios (id_usuario) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS Documentos (
            id_documento INTEGER PRIMARY KEY AUTOINCREMENT,
            id_vehiculo INTEGER NOT NULL,
            tipo TEXT NOT NULL,
            fecha_vencimiento DATE NOT NULL,
            archivo TEXT,
            historial_versiones TEXT,
            FOREIGN KEY (id_vehiculo) REFERENCES Vehiculos (id_vehiculo) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS Mantenciones (
            id_mantencion INTEGER PRIMARY KEY AUTOINCREMENT,
            id_vehiculo INTEGER NOT NULL,
            kilometraje INTEGER NOT NULL,
            fecha DATE NOT NULL,
            tipo TEXT NOT NULL,
            costo REAL NOT NULL,
            taller TEXT,
            FOREIGN KEY (id_vehiculo) REFERENCES Vehiculos (id_vehiculo) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS Conductores (
            id_conductor INTEGER PRIMARY KEY AUTOINCREMENT,
            id_vehiculo INTEGER NOT NULL,
            nombre TEXT NOT NULL,
            contacto TEXT,
            FOREIGN KEY (id_vehiculo) REFERENCES Vehiculos (id_vehiculo) ON DELETE CASCADE
        );
    ";

    // Ejecutar esquema
    $db->exec($schema);

    // Insertar datos iniciales en la tabla Usuarios
    $insertUsuarios = "
        INSERT INTO Usuarios (nombre, correo, contraseña) VALUES 
        ('Juan Pérez', 'juan.perez@example.com', 'admin2'), 
        ('María Gómez', 'maria.gomez@example.com', 'admin');
    ";
    $db->exec($insertUsuarios);

    // Insertar datos iniciales en la tabla Vehiculos
    $insertVehiculos = "
        INSERT INTO Vehiculos (id_usuario, marca, modelo, año, matricula, imagen) VALUES
        (1, 'Toyota', 'Corolla', 2020, 'ABC123', 'toyota_corolla.png'),
        (2, 'Hyundai', 'Tucson', 2019, 'XYZ789', 'hyundai_tucson.png');
    ";
    $db->exec($insertVehiculos);

    // Insertar datos iniciales en la tabla Documentos
    $insertDocumentos = "
        INSERT INTO Documentos (id_vehiculo, tipo, fecha_vencimiento, archivo, historial_versiones) VALUES
        (1, 'Permiso de circulación', '2025-03-15', 'permiso_ABC123.pdf', 'v1.0'),
        (2, 'Revisión técnica', '2025-06-20', 'revision_XYZ789.pdf', 'v1.0');
    ";
    $db->exec($insertDocumentos);

    // Insertar datos iniciales en la tabla Mantenciones
    $insertMantenciones = "
        INSERT INTO Mantenciones (id_vehiculo, kilometraje, fecha, tipo, costo, taller) VALUES
        (1, 15000, '2024-06-10', 'Cambio de aceite', 30000, 'Taller Toyota'),
        (2, 22000, '2024-08-05', 'Reemplazo de frenos', 50000, 'Taller Hyundai');
    ";
    $db->exec($insertMantenciones);

    // Insertar datos iniciales en la tabla Conductores
    $insertConductores = "
        INSERT INTO Conductores (id_vehiculo, nombre, contacto) VALUES
        (1, 'Carlos Soto', '912345678'),
        (2, 'Ana Pérez', '987654321');
    ";
    $db->exec($insertConductores);

    echo "Base de datos inicializada correctamente con los datos.";

} catch (PDOException $e) {
    echo "Error al inicializar la base de datos: " . $e->getMessage();
}
?>
