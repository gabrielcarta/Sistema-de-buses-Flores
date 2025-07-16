<?php
set_time_limit(0);
ini_set('memory_limit', '512M');

$servername = "127.0.0.1"; // Tu servidor de base de datos
$username = "gab"; // Tu usuario de base de datos
$password = "19377391"; // Tu contraseña de base de datos
$dbname = "sistemabuses"; // Tu nombre de base de datos
$puerto = 3305;

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname, $puerto);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql_buses = "SELECT Id_Bus, N_asientos, N_pisos, Servicio FROM Bus";
$result_buses = $conn->query($sql_buses);

if ($result_buses->num_rows > 0) {
    while ($bus = $result_buses->fetch_assoc()) {
        $id_bus = $bus['Id_Bus'];
        $n_asientos = intval($bus['N_asientos']);
        $n_pisos = intval($bus['N_pisos']);

        echo "Procesando bus: $id_bus<br>";
        $asiento_num = 1;
        if ($n_pisos == 2) {
            $primer_piso = min(12, $n_asientos);
            for ($i = 1; $i <= $primer_piso; $i++, $asiento_num++) {
                $sql_insert = "INSERT INTO Asiento (Numero, Piso, Id_Bus) VALUES ($asiento_num, 1, $id_bus)";
                if (!$conn->query($sql_insert)) {
                    echo "Error en bus $id_bus, asiento $asiento_num: " . $conn->error . "<br>";
                }
            }
            for ($i = $asiento_num; $i <= $n_asientos; $i++, $asiento_num++) {
                $sql_insert = "INSERT INTO Asiento (Numero, Piso, Id_Bus) VALUES ($asiento_num, 2, $id_bus)";
                if (!$conn->query($sql_insert)) {
                    echo "Error en bus $id_bus, asiento $asiento_num: " . $conn->error . "<br>";
                }
            }
        } else {
            for ($i = 1; $i <= $n_asientos; $i++, $asiento_num++) {
                $sql_insert = "INSERT INTO Asiento (Numero, Piso, Id_Bus) VALUES ($asiento_num, 1, $id_bus)";
                if (!$conn->query($sql_insert)) {
                    echo "Error en bus $id_bus, asiento $asiento_num: " . $conn->error . "<br>";
                }
            }
        }
        flush();
    }
    echo "Asientos insertados correctamente.";
} else {
    echo "No se encontraron buses.";
}

$conn->close();
?>