<?php
$servername = "127.0.0.1"; // Tu servidor de base de datos
$username = "gab"; // Tu usuario de base de datos
$password = "19377391"; // Tu contraseña de base de datos
$dbname = "sistemabuses1"; // Tu nombre de base de datos
$puerto = 3305;

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname, $puerto);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>