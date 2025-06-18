<?php
// Incluye tu archivo de conexión a la base de datos
include 'db_connect.php'; 

// La consulta SQL debe usar el nombre exacto de la columna: 'Nombre'
$sql = "SELECT Nombre FROM Ciudad ORDER BY Nombre ASC"; 
$result = $conn->query($sql);

$cities = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Accede al valor usando la clave exacta: 'Nombre'
        $cities[] = $row['Nombre']; 
    }
}

echo json_encode($cities); // Devuelve las ciudades como un arreglo JSON

$conn->close();
?>