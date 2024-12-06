¡Bienvenido al Sistema de Gestión de Vehículos!
¡Hola! Este es un proyecto que te permitirá gestionar y controlar todos los aspectos relacionados con tus vehículos de manera  sencilla. Ya sea que seas un usuario con un vehículo o un administrador, nuestra plataforma te proporciona las herramientas necesarias para tener todo bajo control.

🚗 ¿Qué puedes hacer con este sistema?
Como usuario:
Registrar tu vehículo: Agrega tu vehículo a la plataforma con todos los detalles necesarios como marca, modelo, año y matrícula.
Ver mantenimientos: Consulta los mantenimientos realizados a tu vehículo y agrega nuevos registros con fecha, tipo de mantenimiento, coste y taller.
Gestionar tu información: Puedes ver y actualizar tu perfil personal.

⚙️ Requisitos
PHP 7.4 o superior
XAMPP: Utilizado para levantar el servidor localmente
SQLite: Base de datos para almacenar la información
Navegador moderno: Para una experiencia óptima

🛠️ Instalación
1. Clonar el repositorio
Clona este repositorio en tu máquina local:
bash
Copiar código
git clone https://github.com/tu-usuario/gestion-vehiculos.git

2. Levantar el servidor con XAMPP
Descarga e instala XAMPP si aún no lo tienes instalado.
Abre XAMPP y en el panel de control, inicia Apache
Coloca los archivos del proyecto en la carpeta htdocs de tu instalación de XAMPP (por defecto suele estar en C:\xampp\htdocs).
3. Crear la base de datos SQLite
Dentro de la carpeta del proyecto, verás un archivo llamado inicioDB.php. Este archivo contiene el código necesario para generar la base de datos SQLite (mibase.db).
Abre el archivo inicioDB.php en tu navegador, por ejemplo: http://localhost/gestion-vehiculos/inicioDB.php.
Este script creará la base de datos mibase.db automáticamente en la carpeta raíz del proyecto.
4. Acceder a la aplicación
Una vez que la base de datos esté creada, puedes acceder a la aplicación a través de la página de login. Abre tu navegador y accede a:

bash
Copiar código
http://localhost/gestion-vehiculos/login.php

¡Gracias por usar nuestro sistema de gestión de vehículos! 🚗💨
