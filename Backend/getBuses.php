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

// Obtener parámetros de la solicitud
$origin = isset($_GET['origin']) ? $_GET['origin'] : '';
$destination = isset($_GET['destination']) ? $_GET['destination'] : '';
$departureDate = isset($_GET['datedeparture']) ? $_GET['datedeparture'] : '';
$returnDate = isset($_GET['datereturn']) ? $_GET['datereturn'] : '';

$buses = array();

if (!empty($origin) && !empty($destination) && !empty($departureDate)) {
    // Consulta para obtener buses disponibles
    // Basado en el esquema existente, adaptamos la consulta para una tabla hipotética de buses
    $sql = "SELECT 
                b.id,
                b.origen,
                b.destino,
                b.fecha_salida,
                b.hora_salida,
                b.precio,
                b.asientos_disponibles,
                b.empresa
            FROM Bus b 
            WHERE b.origen = ? AND b.destino = ? AND b.fecha_salida >= ? 
            ORDER BY b.fecha_salida ASC, b.hora_salida ASC";
    
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sss", $origin, $destination, $departureDate);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $buses[] = $row;
            }
        }
        
        $stmt->close();
    } else {
        // Si la tabla no existe, devolver datos de ejemplo para pruebas
        $buses[] = [
            "id" => 1,
            "origen" => $origin,
            "destino" => $destination,
            "fecha_salida" => $departureDate,
            "hora_salida" => "08:00",
            "precio" => 25.50,
            "asientos_disponibles" => 15,
            "empresa" => "Flores Express"
        ];
        $buses[] = [
            "id" => 2,
            "origen" => $origin,
            "destino" => $destination,
            "fecha_salida" => $departureDate,
            "hora_salida" => "14:30",
            "precio" => 28.00,
            "asientos_disponibles" => 8,
            "empresa" => "Flores Premium"
        ];
    }
} else {
    // Si faltan parámetros, devolver error
    http_response_code(400);
    echo json_encode(["error" => "Parámetros requeridos: origin, destination, datedeparture"]);
    exit;
}

// Siempre devolver JSON, incluso si no hay resultados
echo json_encode($buses);

$conn->close();
?>