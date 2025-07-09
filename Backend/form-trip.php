<?php
// Permitir solicitudes desde cualquier origen (React, etc.)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Manejar solicitudes OPTIONS (preflight)
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Conexión a la base de datos 
include 'db_connect.php'; 

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Conexión fallida: " . $conn->connect_error]);
    exit;
}

// Consulta para obtener ciudades
$sql = "SELECT Nombre FROM Ciudad ORDER BY Nombre ASC";
$result = $conn->query($sql);

$cities = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cities[] = $row['Nombre'];
    }
    echo json_encode($cities);
} else {
    echo json_encode([]);
}

$conn->close();
?>