<?php
include 'db_connect.php'; 
include 'crud-completo.php';
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ... tu código normal para insertar/editar/eliminar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Insertar bus
if (isset($_POST['accion']) && $_POST['accion'] == 'insertar_bus') {
    $placa = $_POST['placa'] ?? '';
    $servicio = $_POST['servicio'] ?? '';
    $n_pisos = intval($_POST['n_pisos'] ?? 2);
    $n_asientos = intval($_POST['n_asientos'] ?? 52);
    $id_sede = intval($_POST['id_sede'] ?? 1);

    $id_bus = insertarBus($conn, $placa, $servicio, $n_pisos, $n_asientos, $id_sede);
    echo "Bus insertado con ID: $id_bus";
    exit;
}

// Actualizar bus
if (isset($_POST['accion']) && $_POST['accion'] == 'actualizar_bus') {
    $id_bus = intval($_POST['Id_bus']);
    $placa = $_POST['nuevaPlaca'] ?? '';
    $servicio = $_POST['nuevoServicio'] ?? '';
    $n_pisos = intval($_POST['N_pisos'] ?? 2);
    $n_asientos = intval($_POST['N_asientos'] ?? 52);
    $id_sede = intval($_POST['Id_Sede'] ?? 1);

    actualizarBus($conn, $Id_bus, $nuevaPlaca, $nuevoServicio, $N_pisos, $N_asientos, $Id_Sede);
    echo "Bus actualizado con ID: $Id_bus";
    exit;
}

// Eliminar bus
if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar_bus') {
    $id_bus = intval($_POST['Id_Bus']);
    file_put_contents("debug_eliminar.log", "Intentando eliminar ID: " . $Id_Bus);
    eliminarBus($conn, $Id_Bus);
    echo "Bus eliminado con ID: $Id_Bus";
    exit;
}

// Insertar ruta
if (isset($_POST['accion']) && $_POST['accion'] == 'insertar_ruta') {
    $duracion = $_POST['duracion'] ?? '';
    $id_origen = intval($_POST['id_origen'] ?? 1);
    $id_llegada = intval($_POST['id_llegada'] ?? 2);

    $id_ruta = insertarRuta($conn, $duracion, $id_origen, $id_llegada);
    echo "Ruta insertada con ID: $id_ruta";
    exit;
}

// Actualizar ruta
if (isset($_POST['accion']) && $_POST['accion'] == 'actualizar_ruta') {
    $id_ruta = intval($_POST['id_ruta']);
    $duracion = $_POST['duracion'] ?? '';
    $id_origen = intval($_POST['id_origen'] ?? 1);
    $id_llegada = intval($_POST['id_llegada'] ?? 2);

    actualizarRuta($conn, $id_ruta, $duracion, $id_origen, $id_llegada);
    echo "Ruta actualizada con ID: $id_ruta";
    exit;
}

// Eliminar ruta
if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar_ruta') {
    $id_ruta = intval($_POST['id_ruta']);
    eliminarRuta($conn, $id_ruta);
    echo "Ruta eliminada con ID: $id_ruta";
    exit;
}

// Insertar viaje y detalle
if (isset($_POST['accion']) && $_POST['accion'] == 'insertar_viaje') {
    $hora_salida = $_POST['hora_salida'] ?? '';
    $hora_llegada = $_POST['hora_llegada'] ?? '';
    $fecha_salida = $_POST['fecha_salida'] ?? '';
    $fecha_llegada = $_POST['fecha_llegada'] ?? '';
    $id_bus = intval($_POST['id_bus'] ?? 1);
    $id_ruta = intval($_POST['id_ruta'] ?? 1);
    $precio_piso1 = floatval($_POST['precio_piso1'] ?? 0);
    $precio_piso2 = floatval($_POST['precio_piso2'] ?? 0);
    $precios_por_piso = [
        1 => $precio_piso1,
        2 => $precio_piso2
    ];

    $id_viaje = insertarViaje($conn, $hora_salida, $hora_llegada, $fecha_salida, $fecha_llegada, $id_bus, $id_ruta, $precios_por_piso);
    echo "Viaje insertado con ID: $id_viaje";
    exit;
}

// Actualizar viaje y detalle
if (isset($_POST['accion']) && $_POST['accion'] == 'actualizar_viaje') {
    $id_viaje = intval($_POST['id_viaje']);
    $hora_salida = $_POST['hora_salida'] ?? '';
    $hora_llegada = $_POST['hora_llegada'] ?? '';
    $fecha_salida = $_POST['fecha_salida'] ?? '';
    $fecha_llegada = $_POST['fecha_llegada'] ?? '';
    $id_bus = intval($_POST['id_bus'] ?? 1);
    $id_ruta = intval($_POST['id_ruta'] ?? 1);
    $precio_piso1 = floatval($_POST['precio_piso1'] ?? 0);
    $precio_piso2 = floatval($_POST['precio_piso2'] ?? 0);
    $precios_por_piso = [
        1 => $precio_piso1,
        2 => $precio_piso2
    ];

    actualizarViaje($conn, $id_viaje, $hora_salida, $hora_llegada, $fecha_salida, $fecha_llegada, $id_bus, $id_ruta, $precios_por_piso);
    echo "Viaje actualizado con ID: $id_viaje";
    exit;
}

// Eliminar viaje
if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar_viaje') {
    $id_viaje = intval($_POST['id_viaje']);
    eliminarViaje($conn, $id_viaje);
    echo "Viaje eliminado con ID: $id_viaje";
    exit;
}

echo "Acción no válida.";
exit;

?>