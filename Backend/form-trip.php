<?php
// Permitir solicitudes desde cualquier origen (React, etc.)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Conexión a la base de datos 
include 'db_connect.php'; 

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Conexión fallida: " . $conn->connect_error]);
    exit;
}

// Consulta para obtener ID y Nombre de las ciudades
$sql = "SELECT Id_Ciudad, Nombre FROM ciudad ORDER BY Nombre ASC";
$result = $conn->query($sql);

$cities = array();

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $cities[] = [
            "Id_Ciudad" => $row['Id_Ciudad'],
            "Nombre" => $row['Nombre']
        ];
    }
    echo json_encode($cities);
} else {
    echo json_encode([]);
}

$conn->close();
?>
