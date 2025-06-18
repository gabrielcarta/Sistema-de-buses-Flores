<?php
$servername = "localhost"; // Tu servidor de base de datos
$username = "admin"; // Tu usuario de base de datos
$password = "admin123"; // Tu contraseña de base de datos
$dbname = "SistemaBuses"; // Tu nombre de base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>