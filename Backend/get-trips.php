<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

try {
    include 'db_connect.php';
    $database_connected = true;
} catch (Exception $e) {
    $database_connected = false;
    $conn = null;
}

if (!$database_connected || !$conn || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Conexión fallida a la base de datos."]);
    exit;
}

$origin = isset($_GET['origin']) ? intval($_GET['origin']) : null;
$destination = isset($_GET['destination']) ? intval($_GET['destination']) : null;
$departureDate = isset($_GET['datedeparture']) ? $_GET['datedeparture'] : null;
$returnDate = isset($_GET['datereturn']) ? $_GET['datereturn'] : null;

if (!$origin || !$destination || !$departureDate) {
    http_response_code(400);
    echo json_encode(["error" => "Faltan parámetros requeridos: origin, destination, datedeparture"]);
    exit;
}

// CONSULTA DE IDA
$sql = "
    SELECT 
        v.Id_Viaje,
        co.Nombre AS Origen,
        cl.Nombre AS Llegada,
        v.Hora_salida,
        v.Hora_llegada,
        v.Fecha_salida,
        v.Fecha_llegada,
        b.Servicio,
        r.Duracion,
        s.Ubicacion AS Ubicacion_Sede,
        vd1.Precio AS Precio_1_Piso,
        (SELECT COUNT(*) FROM Asiento a 
            LEFT JOIN Pasaje p ON p.Id_Asiento = a.Id_Asiento AND p.Id_Viaje = v.Id_Viaje 
            WHERE a.Piso = 1 AND a.Id_Bus = b.Id_Bus AND (p.Id_Pasaje IS NULL OR p.Estado = 0)
        ) AS Asientos_1_Disponibles,
        vd2.Precio AS Precio_2_Piso,
        (SELECT COUNT(*) FROM Asiento a 
            LEFT JOIN Pasaje p ON p.Id_Asiento = a.Id_Asiento AND p.Id_Viaje = v.Id_Viaje 
            WHERE a.Piso = 2 AND a.Id_Bus = b.Id_Bus AND (p.Id_Pasaje IS NULL OR p.Estado = 0)
        ) AS Asientos_2_Disponibles
    FROM Viaje v
    INNER JOIN Ruta r ON v.Id_Ruta = r.Id_Ruta
    INNER JOIN Ciudad co ON r.Id_Origen = co.Id_Ciudad
    INNER JOIN Ciudad cl ON r.Id_Llegada = cl.Id_Ciudad
    INNER JOIN Bus b ON v.Id_Bus = b.Id_Bus
    INNER JOIN Sede s ON b.Id_Sede = s.Id_Sede
    LEFT JOIN ViajeDetalle vd1 ON vd1.Id_Viaje = v.Id_Viaje AND vd1.N_Piso = 1
    LEFT JOIN ViajeDetalle vd2 ON vd2.Id_Viaje = v.Id_Viaje AND vd2.N_Piso = 2
    WHERE r.Id_Origen = ? AND r.Id_Llegada = ? AND v.Fecha_salida = ?
    ORDER BY v.Hora_salida ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $origin, $destination, $departureDate);
$stmt->execute();
$result = $stmt->get_result();

$ida = array();
while ($row = $result->fetch_assoc()) {
    $ida[] = $row;
}
$stmt->close();

// CONSULTA DE RETORNO (si se pasó fecha de retorno)
$retorno = array();
if ($returnDate !== null && $returnDate !== '') {
    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param("iis", $destination, $origin, $returnDate);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    while ($row = $result2->fetch_assoc()) {
        $retorno[] = $row;
    }
    $stmt2->close();
}

echo json_encode([
    "ida" => $ida,
    "retorno" => $retorno
]);

$conn->close();
?>
