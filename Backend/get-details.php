<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db_connect.php';

function obtenerDetalleViaje($conn, $id_viaje) {
    $sql = "
    SELECT
        v.Id_Viaje,
        r.Id_Ruta,
        co.Nombre AS Origen,
        cl.Nombre AS Llegada,
        b.Servicio,
        v.Hora_salida,
        v.Hora_llegada,
        v.Fecha_salida,
        v.Fecha_llegada,
        r.Duracion,
        s.Ubicacion AS Ubicacion_Sede,
        vd1.Precio AS Precio_1_Piso,
        vd2.Precio AS Precio_2_Piso,
        (SELECT COUNT(*) FROM Asiento a 
            LEFT JOIN Pasaje p ON p.Id_Asiento = a.Id_Asiento AND p.Id_Viaje = v.Id_Viaje 
            WHERE a.Piso = 1 AND a.Id_Bus = b.Id_Bus AND (p.Id_Pasaje IS NULL OR p.Estado = 0)
        ) AS Asientos_1_Disponibles,
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
    WHERE v.Id_Viaje = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_viaje);
    $stmt->execute();
    $result = $stmt->get_result();
    $detalle = $result->fetch_assoc();
    $stmt->close();
    return $detalle;
}

// Obtener ID desde el GET
$id_viaje = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_viaje > 0) {
    $data = obtenerDetalleViaje($conn, $id_viaje);
    echo json_encode($data);
} else {
    echo json_encode(["error" => "Falta el parámetro id"]);
}

$conn->close();
