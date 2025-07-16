<?php
include 'db_connect.php';

$dni = $_POST['dni'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$apellido2 = $_POST['apellido2'];
$fecha_nac = $_POST['fecha_nac'];
$sexo = $_POST['sexo'];
$telefono = $_POST['telefono'];
$correo = $_POST['correo'];
$id_viaje = $_POST['id_viaje'];
$id_asiento = $_POST['id_asiento'];
$precio = $_POST['precio'];
$medio_pago = $_POST['medio_pago'];

$anio_nac = intval(substr($fecha_nac, 0, 4));
$anio_actual = intval(date('Y'));
$categoria = ($anio_actual - $anio_nac < 18) ? 'Niño' : (($sexo == 'M') ? 'Hombre' : 'Mujer');

$sql = "INSERT INTO Persona (DNI, Nombre, Apellido, Sexo, Correo, Telefono)
        VALUES (?, ?, CONCAT(?, ' ', ?), ?, ?, ?)
        ON DUPLICATE KEY UPDATE Nombre=VALUES(Nombre), Apellido=VALUES(Apellido), Sexo=VALUES(Sexo), Correo=VALUES(Correo), Telefono=VALUES(Telefono)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $dni, $nombre, $apellido, $apellido2, $sexo, $correo, $telefono);
$stmt->execute();
$stmt->close();

$sql = "INSERT INTO Cliente (DNI_Cliente, Categoria, Estado_Cliente)
        VALUES (?, ?, 'Activo')
        ON DUPLICATE KEY UPDATE Categoria=VALUES(Categoria), Estado_Cliente='Activo'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $dni, $categoria);
$stmt->execute();
$stmt->close();

$fechaCompra = date('Y-m-d');
$codigoQR = uniqid($dni . $id_viaje . $id_asiento);
$sql = "INSERT INTO Pasaje (Estado, FechaCompra, Codigo_QR, Id_Viaje, Id_Cliente, Id_Asiento)
        VALUES (1, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssii", $fechaCompra, $codigoQR, $id_viaje, $dni, $id_asiento);
$stmt->execute();
$id_pasaje = $stmt->insert_id;
$stmt->close();

$sql = "INSERT INTO Boleta (Id_Pasaje, Estado_Pago, Medio_Pago, Monto_Total)
        VALUES (?, 1, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isd", $id_pasaje, $medio_pago, $precio);
$stmt->execute();
$stmt->close();

echo json_encode([
    'success' => true,
    'mensaje' => 'Compra registrada correctamente',
    'codigoQR' => $codigoQR,
    'id_pasaje' => $id_pasaje
]);
?>