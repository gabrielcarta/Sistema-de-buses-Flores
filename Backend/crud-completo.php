<?php
include 'db_connect.php'; 

// Verificar conexión
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Conexión fallida: " . $conn->connect_error]);
    exit;
}

/***********************
 * CRUD BUS
 ***********************/

// Crear Bus y generar asientos (reglas según servicio)
function insertarBus($conn, $placa, $servicio, $n_pisos, $n_asientos, $id_sede) {
    $servicio = strtolower(trim($servicio));
    $servicios_grandes = ["economico", "ejecutivo", "imperial dorado"];
    $servicios_premium = ["bus cama", "dorado vip", "dorado vip 160"];

    if (in_array($servicio, $servicios_grandes)) {
        $n_pisos = 2;
        if ($n_asientos < 52) $n_asientos = 52;
        elseif ($n_asientos > 58) $n_asientos = 58;
        $n_asientos = ($n_asientos % 2 === 0) ? $n_asientos : $n_asientos + 1;
    } elseif (in_array($servicio, $servicios_premium)) {
        if ($n_pisos < 1) $n_pisos = 1;
        elseif ($n_pisos > 2) $n_pisos = 2;
        if ($n_asientos < 42) $n_asientos = 42;
        elseif ($n_asientos > 50) $n_asientos = 50;
        $n_asientos = ($n_asientos % 2 === 0) ? $n_asientos : $n_asientos + 1;
    } else {
        $n_pisos = 2;
        $n_asientos = 52;
    }
    $asientos_primer_piso = 12;
    $asientos_segundo_piso = ($n_pisos == 2) ? ($n_asientos - $asientos_primer_piso) : 0;

    $sql = "INSERT INTO Bus (Placa, N_Pisos, Servicio, N_asientos, Id_Sede) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisii", $placa, $n_pisos, $servicio, $n_asientos, $id_sede);
    $stmt->execute();
    $id_bus = $stmt->insert_id;
    $stmt->close();

    for ($i = 1; $i <= $asientos_primer_piso; $i++) {
        $sql_asiento = "INSERT INTO Asiento (Piso, Numero, Id_Bus) VALUES (1, ?, ?)";
        $stmt_asiento = $conn->prepare($sql_asiento);
        $stmt_asiento->bind_param("ii", $i, $id_bus);
        $stmt_asiento->execute();
        $stmt_asiento->close();
    }
    if ($n_pisos == 2) {
        for ($j = 1; $j <= $asientos_segundo_piso; $j++) {
            $sql_asiento = "INSERT INTO Asiento (Piso, Numero, Id_Bus) VALUES (2, ?, ?)";
            $stmt_asiento = $conn->prepare($sql_asiento);
            $stmt_asiento->bind_param("ii", $j, $id_bus);
            $stmt_asiento->execute();
            $stmt_asiento->close();
        }
    }
    return $id_bus;
}

// Leer Buses
function obtenerBuses($conn) {
    $sql = "SELECT * FROM Bus";
    $resultado = $conn->query($sql);
    $buses = [];
    while ($row = $resultado->fetch_assoc()) $buses[] = $row;
    return $buses;
}

// Actualizar Bus (no modifica asientos, solo datos del bus)
function actualizarBus($conn, $id_bus, $placa, $servicio, $n_pisos, $n_asientos, $id_sede) {
    $servicio = strtolower(trim($servicio));
    $sql = "UPDATE Bus SET Placa=?, N_Pisos=?, Servicio=?, N_asientos=?, Id_Sede=? WHERE Id_Bus=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisiii", $placa, $n_pisos, $servicio, $n_asientos, $id_sede, $id_bus);
    $stmt->execute();
    $stmt->close();
}

// Eliminar Bus (y sus asientos)
function eliminarBus($conn, $id_bus) {
    $conn->query("DELETE FROM Asiento WHERE Id_Bus = $id_bus");
    $conn->query("DELETE FROM Bus WHERE Id_Bus = $id_bus");
}

// Leer asientos de un bus
function obtenerAsientosBus($conn, $id_bus, $piso = null) {
    if ($piso === null) {
        $sql = "SELECT * FROM Asiento WHERE Id_Bus = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_bus);
    } else {
        $sql = "SELECT * FROM Asiento WHERE Id_Bus = ? AND Piso = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $id_bus, $piso);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $asientos = [];
    while ($row = $result->fetch_assoc()) $asientos[] = $row;
    $stmt->close();
    return $asientos;
}

/***********************
 * CRUD RUTA
 ***********************/

// Crear Ruta
function insertarRuta($conn, $duracion, $id_origen, $id_llegada) {
    $sql = "INSERT INTO Ruta (Duracion, Id_Origen, Id_Llegada) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $duracion, $id_origen, $id_llegada);
    $stmt->execute();
    $id_ruta = $stmt->insert_id;
    $stmt->close();
    return $id_ruta;
}

// Leer Rutas
function obtenerRutas($conn) {
    $sql = "SELECT r.*, co.Nombre AS Origen, cl.Nombre AS Llegada
            FROM Ruta r
            INNER JOIN Ciudad co ON r.Id_Origen = co.Id_Ciudad
            INNER JOIN Ciudad cl ON r.Id_Llegada = cl.Id_Ciudad";
    $resultado = $conn->query($sql);
    $rutas = [];
    while ($row = $resultado->fetch_assoc()) $rutas[] = $row;
    return $rutas;
}

// Actualizar Ruta
function actualizarRuta($conn, $id_ruta, $duracion, $id_origen, $id_llegada) {
    $sql = "UPDATE Ruta SET Duracion=?, Id_Origen=?, Id_Llegada=? WHERE Id_Ruta=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siii", $duracion, $id_origen, $id_llegada, $id_ruta);
    $stmt->execute();
    $stmt->close();
}

// Eliminar Ruta
function eliminarRuta($conn, $id_ruta) {
    $conn->query("DELETE FROM Ruta WHERE Id_Ruta = $id_ruta");
}

/***********************
 * CRUD VIAJE & DETALLE
 ***********************/

// Crear Viaje y sus detalles de precio por piso
function insertarViaje($conn, $hora_salida, $hora_llegada, $fecha_salida, $fecha_llegada, $id_bus, $id_ruta, $precios_por_piso) {
    $sql = "INSERT INTO Viaje (Hora_salida, Hora_llegada, Fecha_salida, Fecha_llegada, Id_Bus, Id_Ruta) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $hora_salida, $hora_llegada, $fecha_salida, $fecha_llegada, $id_bus, $id_ruta);
    $stmt->execute();
    $id_viaje = $stmt->insert_id;
    $stmt->close();

    foreach ($precios_por_piso as $piso => $precio) {
        $sql_detalle = "INSERT INTO ViajeDetalle (N_Piso, Precio, Id_Viaje) VALUES (?, ?, ?)";
        $stmt_detalle = $conn->prepare($sql_detalle);
        $stmt_detalle->bind_param("idi", $piso, $precio, $id_viaje);
        $stmt_detalle->execute();
        $stmt_detalle->close();
    }
    return $id_viaje;
}

// Leer Viajes
function obtenerViajes($conn, $limit = 20, $offset = 0) {
    $sql = "SELECT v.*, b.Placa, r.Id_Origen, r.Id_Llegada
            FROM Viaje v
            INNER JOIN Bus b ON v.Id_Bus = b.Id_Bus
            INNER JOIN Ruta r ON v.Id_Ruta = r.Id_Ruta
            ORDER BY v.Id_Viaje ASC
            LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $viajes = [];
    while ($row = $resultado->fetch_assoc()) {
        $viajes[] = $row;
    }

    $stmt->close();

    // También obtenemos el total de viajes para saber cuántas páginas hay
    $totalResult = $conn->query("SELECT COUNT(*) AS total FROM Viaje");
    $total = $totalResult->fetch_assoc()['total'];

    return [
        'data' => $viajes,
        'total' => $total
    ];
}

// Actualizar Viaje y su detalle de precios
function actualizarViaje($conn, $id_viaje, $hora_salida, $hora_llegada, $fecha_salida, $fecha_llegada, $id_bus, $id_ruta, $precios_por_piso) {
    $sql = "UPDATE Viaje SET Hora_salida=?, Hora_llegada=?, Fecha_salida=?, Fecha_llegada=?, Id_Bus=?, Id_Ruta=? WHERE Id_Viaje=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiii", $hora_salida, $hora_llegada, $fecha_salida, $fecha_llegada, $id_bus, $id_ruta, $id_viaje);
    $stmt->execute();
    $stmt->close();

    foreach ($precios_por_piso as $piso => $precio) {
        $sql_detalle = "UPDATE ViajeDetalle SET Precio=? WHERE Id_Viaje=? AND N_Piso=?";
        $stmt_detalle = $conn->prepare($sql_detalle);
        $stmt_detalle->bind_param("dii", $precio, $id_viaje, $piso);
        $stmt_detalle->execute();
        $stmt_detalle->close();
    }
}

// Eliminar Viaje y sus detalles
function eliminarViaje($conn, $id_viaje) {
    $conn->query("DELETE FROM ViajeDetalle WHERE Id_Viaje = $id_viaje");
    $conn->query("DELETE FROM Viaje WHERE Id_Viaje = $id_viaje");
}

/***********************
 * AUXILIARES
 ***********************/

// Para desplegables
function obtenerSedes($conn) {
    $resultado = $conn->query("SELECT Id_Sede, Nombre FROM Sede");
    $sedes = [];
    while ($row = $resultado->fetch_assoc()) $sedes[] = $row;
    return $sedes;
}
function obtenerCiudades($conn) {
    $resultado = $conn->query("SELECT Id_Ciudad, Nombre FROM Ciudad");
    $ciudades = [];
    while ($row = $resultado->fetch_assoc()) $ciudades[] = $row;
    return $ciudades;
}

?>