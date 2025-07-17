<?php
set_time_limit(0);
ini_set('memory_limit', '512M');

include 'db_connect.php'; 

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"), true);

// Función para generar un DNI único y ficticio
function generarDNI($existentes) {
    do {
        $dni = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
    } while (in_array($dni, $existentes));
    return $dni;
}

// Función para generar nombres ficticios
function generarNombre($sexo) {
    $nombres_hombres = ['Luis', 'Carlos', 'Antonio', 'Andrés', 'José'];
    $nombres_mujeres = ['María', 'Ana', 'Sandra', 'Lucía', 'Carla'];
    $apellidos = ['González', 'Fernández', 'Pérez', 'Rodríguez', 'Alfaro', 'Choque'];
    $nombre = $sexo == 'M' ? $nombres_hombres[array_rand($nombres_hombres)] : $nombres_mujeres[array_rand($nombres_mujeres)];
    $apellido1 = $apellidos[array_rand($apellidos)];
    $apellido2 = $apellidos[array_rand($apellidos)];
    return [$nombre, $apellido1, $apellido2];
}

// Verificar viajes existentes
$sql_viajes = "SELECT Id_Viaje, Fecha_salida, Hora_salida, Id_Bus FROM Viaje WHERE Fecha_salida BETWEEN '2025-07-01' AND '2025-07-31'";
$result_viajes = $conn->query($sql_viajes);
$viajes = [];
while ($viaje = $result_viajes->fetch_assoc()) {
    $viajes[] = $viaje;
}

if (count($viajes) == 0) {
    die("No hay viajes disponibles en julio para asignar.");
}

// Generar clientes y asignar viajes
$clientes_existentes = [];
$sql_clientes = "SELECT DNI_Cliente FROM Cliente";
$result_clientes = $conn->query($sql_clientes);
while ($cliente = $result_clientes->fetch_assoc()) {
    $clientes_existentes[] = $cliente['DNI_Cliente'];
}

$metodo_pago = 'Tarjeta';
$clientes_generados = 100;

for ($i = 0; $i < $clientes_generados; $i++) {
    // Generar cliente ficticio
    $dni = generarDNI($clientes_existentes);
    $clientes_existentes[] = $dni;
    $sexo = rand(0, 1) == 0 ? 'M' : 'F';
    list($nombre, $apellido1, $apellido2) = generarNombre($sexo);
    $anio_nacimiento = rand(2005, 2025); // Si <18 será niño
    $categoria = (date('Y') - $anio_nacimiento < 18) ? 'Niño' : ($sexo == 'M' ? 'Hombre' : 'Mujer');
    $correo = strtolower("$nombre.$apellido1.$apellido2") . "@example.com";
    $telefono = '9' . rand(10000000, 99999999);

    // Insertar datos en Persona
    $sql_persona = "INSERT INTO Persona (DNI, Nombre, Apellido, Sexo, Correo, Telefono) 
                    VALUES (?, ?, CONCAT(?, ' ', ?), ?, ?, ?)";
    $stmt = $conn->prepare($sql_persona);
    $stmt->bind_param("sssssss", $dni, $nombre, $apellido1, $apellido2, $sexo, $correo, $telefono);
    $stmt->execute();
    $stmt->close();

    // Insertar datos en Cliente
    $sql_cliente = "INSERT INTO Cliente (DNI_Cliente, Categoria, Estado_Cliente) 
                    VALUES (?, ?, 'Activo')";
    $stmt = $conn->prepare($sql_cliente);
    $stmt->bind_param("ss", $dni, $categoria);
    $stmt->execute();
    $stmt->close();

    // Asignar viajes
    $num_viajes = rand(1, 3); // Entre 1 y 3 viajes por cliente
    $viajes_asignados = array_rand($viajes, $num_viajes);

    foreach ((array)$viajes_asignados as $index) {
        $viaje = $viajes[$index];
        $id_viaje = $viaje['Id_Viaje'];

        // Seleccionar asiento disponible
        $sql_asiento = "SELECT Id_Asiento FROM Asiento WHERE Id_Bus = ? 
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

        // Insertar pasaje
        $codigoQR = uniqid($dni . $id_viaje . $id_asiento);
        $sql_pasaje = "INSERT INTO Pasaje (Estado, FechaCompra, Codigo_QR, Id_Viaje, Id_Cliente, Id_Asiento) 
                       VALUES (1, NOW(), ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_pasaje);
        $stmt->bind_param("ssii", $codigoQR, $id_viaje, $dni, $id_asiento);
        $stmt->execute();
        $id_pasaje = $stmt->insert_id;
        $stmt->close();

        // Insertar boleta
        $precio = rand(30, 100); // Precio ficticio entre 30 y 100
        $sql_boleta = "INSERT INTO Boleta (Id_Pasaje, Estado_Pago, Medio_Pago, Monto_Total) 
                       VALUES (?, 1, ?, ?)";
        $stmt = $conn->prepare($sql_boleta);
        $stmt->bind_param("isd", $id_pasaje, $metodo_pago, $precio);
        $stmt->execute();
        $stmt->close();
    }
}
file_put_contents("log_respuesta.txt", print_r($_POST, true));

echo "Clientes y viajes generados correctamente.";

$conn->close();
?>