<?php
set_time_limit(0);
ini_set('memory_limit', '512M');

include 'db_connect.php'; 

// Elimina los pasajes existentes para los viajes en julio
$sql_borrar_pasajes = "DELETE FROM Pasaje WHERE Id_Viaje IN (SELECT Id_Viaje FROM Viaje WHERE Fecha_salida BETWEEN '2025-07-01' AND '2025-07-31')";
$conn->query($sql_borrar_pasajes);

// Verificar viajes disponibles en julio
$sql_viajes = "SELECT v.Id_Viaje, v.Fecha_salida, v.Hora_salida, v.Id_Bus, vd1.Precio AS Precio_1_Piso, vd2.Precio AS Precio_2_Piso
               FROM Viaje v
               LEFT JOIN ViajeDetalle vd1 ON vd1.Id_Viaje = v.Id_Viaje AND vd1.N_Piso = 1
               LEFT JOIN ViajeDetalle vd2 ON vd2.Id_Viaje = v.Id_Viaje AND vd2.N_Piso = 2
               WHERE v.Fecha_salida BETWEEN '2025-07-01' AND '2025-07-31'";
$result_viajes = $conn->query($sql_viajes);
$viajes = [];
while ($viaje = $result_viajes->fetch_assoc()) {
    $viajes[] = $viaje;
}

if (count($viajes) == 0) {
    die("No hay viajes disponibles en julio para asignar.");
}

// Obtener todos los clientes activos
$sql_clientes = "SELECT DNI_Cliente FROM Cliente WHERE Estado_Cliente = 'Activo'";
$result_clientes = $conn->query($sql_clientes);
$clientes = [];
while ($cliente = $result_clientes->fetch_assoc()) {
    $clientes[] = $cliente['DNI_Cliente'];
}

if (count($clientes) == 0) {
    die("No hay clientes activos para asignar pasajes.");
}

// Método de pago
$metodo_pago = 'Tarjeta';

// Asignar pasajes para cada cliente
foreach ($clientes as $dni_cliente) {
    // Número de viajes aleatorios por cliente (entre 1 y 3 por mes)
    $num_viajes = rand(1, 3);
    $viajes_asignados = array_rand($viajes, $num_viajes);

    foreach ((array)$viajes_asignados as $index) {
        $viaje = $viajes[$index];
        $id_viaje = $viaje['Id_Viaje'];

        // Seleccionar asiento disponible
        $sql_asiento = "SELECT Id_Asiento, Piso FROM Asiento WHERE Id_Bus = ? 
                        AND Id_Asiento NOT IN (SELECT Id_Asiento FROM Pasaje WHERE Id_Viaje = ?)
                        LIMIT 1";
        $stmt = $conn->prepare($sql_asiento);
        $stmt->bind_param("ii", $viaje['Id_Bus'], $id_viaje);
        $stmt->execute();
        $result_asiento = $stmt->get_result();
        $asiento = $result_asiento->fetch_assoc();
        $stmt->close();

        if (!$asiento) {
            continue; // No hay asientos disponibles para este viaje
        }

        $id_asiento = $asiento['Id_Asiento'];
        $piso = $asiento['Piso'];

        // Determinar el precio basado en el piso del asiento
        $precio = ($piso == 1) ? $viaje['Precio_1_Piso'] : $viaje['Precio_2_Piso'];

        // Insertar pasaje
        $codigoQR = uniqid($dni_cliente . $id_viaje . $id_asiento);
        $sql_pasaje = "INSERT INTO Pasaje (Estado, FechaCompra, Codigo_QR, Id_Viaje, Id_Cliente, Id_Asiento) 
                       VALUES (1, NOW(), ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_pasaje);
        $stmt->bind_param("ssii", $codigoQR, $id_viaje, $dni_cliente, $id_asiento);
        $stmt->execute();
        $id_pasaje = $stmt->insert_id;
        $stmt->close();

        // Insertar boleta
        $sql_boleta = "INSERT INTO Boleta (Id_Pasaje, Estado_Pago, Medio_Pago, Monto_Total) 
                       VALUES (?, 1, ?, ?)";
        $stmt = $conn->prepare($sql_boleta);
        $stmt->bind_param("isd", $id_pasaje, $metodo_pago, $precio);
        $stmt->execute();
        $stmt->close();
    }
}

echo "Pasajes regenerados correctamente para el mes de julio.";

$conn->close();
?>