<?php

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Logs de depuración
file_put_contents("debug_eliminar.log", "LLEGÓ AL PHP\n", FILE_APPEND);
file_put_contents("debug_eliminar.log", "RAW: " . file_get_contents("php://input") . "\n", FILE_APPEND);

// Headers CORS
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'db_connect.php'; 
include 'crud-completo.php';

// Procesar JSON recibido
$data = json_decode(file_get_contents("php://input"), true);

// Utilizar datos del cuerpo JSON si existen, de lo contrario usar $_POST
$input = is_array($data) && !empty($data) ? $data : $_POST;

$accion = $input['accion'] ?? '';

switch ($accion) {
    case 'insertar_bus':
        $placa = $input['placa'] ?? '';
        $servicio = $input['servicio'] ?? '';
        $n_pisos = intval($input['n_pisos'] ?? 2);
        $n_asientos = intval($input['n_asientos'] ?? 52);
        $id_sede = intval($input['id_sede'] ?? 1);

        $id_bus = insertarBus($conn, $placa, $servicio, $n_pisos, $n_asientos, $id_sede);
        echo "Bus insertado con ID: $id_bus";
        break;

    case 'actualizar_bus':
        $id_bus = intval($input['Id_bus'] ?? 0);
        $placa = $input['nuevaPlaca'] ?? '';
        $servicio = $input['nuevoServicio'] ?? '';
        $n_pisos = intval($input['N_pisos'] ?? 2);
        $n_asientos = intval($input['N_asientos'] ?? 52);
        $id_sede = intval($input['Id_Sede'] ?? 1);

        actualizarBus($conn, $id_bus, $placa, $servicio, $n_pisos, $n_asientos, $id_sede);
        echo "Bus actualizado con ID: $id_bus";
        break;

    case 'eliminar_bus':
        $Id_Bus = intval($input['Id_Bus'] ?? 0);
        eliminarBus($conn, $Id_Bus);
        echo "Bus eliminado con ID: $Id_Bus";
        break;

    case 'insertar_ruta':
        $duracion = $input['duracion'] ?? '';
        $id_origen = intval($input['id_origen'] ?? 1);
        $id_llegada = intval($input['id_llegada'] ?? 2);

        $id_ruta = insertarRuta($conn, $duracion, $id_origen, $id_llegada);
        echo "Ruta insertada con ID: $id_ruta";
        break;

    case 'actualizar_ruta':
        $id_ruta = intval($input['id_ruta'] ?? 0);
        $duracion = $input['duracion'] ?? '';
        $id_origen = intval($input['id_origen'] ?? 1);
        $id_llegada = intval($input['id_llegada'] ?? 2);

        actualizarRuta($conn, $id_ruta, $duracion, $id_origen, $id_llegada);
        echo "Ruta actualizada con ID: $id_ruta";
        break;

    case 'eliminar_ruta':
        $id_ruta = intval($input['id_ruta'] ?? 0);
        eliminarRuta($conn, $id_ruta);
        echo "Ruta eliminada con ID: $id_ruta";
        break;

    case 'insertar_viaje':
        $hora_salida = $input['hora_salida'] ?? '';
        $hora_llegada = $input['hora_llegada'] ?? '';
        $fecha_salida = $input['fecha_salida'] ?? '';
        $fecha_llegada = $input['fecha_llegada'] ?? '';
        $id_bus = intval($input['id_bus'] ?? 1);
        $id_ruta = intval($input['id_ruta'] ?? 1);
        $precio_piso1 = floatval($input['precio_piso1'] ?? 0);
        $precio_piso2 = floatval($input['precio_piso2'] ?? 0);
        $precios_por_piso = [1 => $precio_piso1, 2 => $precio_piso2];

        $id_viaje = insertarViaje($conn, $hora_salida, $hora_llegada, $fecha_salida, $fecha_llegada, $id_bus, $id_ruta, $precios_por_piso);
        echo "Viaje insertado con ID: $id_viaje";
        break;

    case 'actualizar_viaje':
        $id_viaje = intval($input['id_viaje'] ?? 0);
        $hora_salida = $input['hora_salida'] ?? '';
        $hora_llegada = $input['hora_llegada'] ?? '';
        $fecha_salida = $input['fecha_salida'] ?? '';
        $fecha_llegada = $input['fecha_llegada'] ?? '';
        $id_bus = intval($input['id_bus'] ?? 1);
        $id_ruta = intval($input['id_ruta'] ?? 1);
        $precio_piso1 = floatval($input['precio_piso1'] ?? 0);
        $precio_piso2 = floatval($input['precio_piso2'] ?? 0);
        $precios_por_piso = [1 => $precio_piso1, 2 => $precio_piso2];

        actualizarViaje($conn, $id_viaje, $hora_salida, $hora_llegada, $fecha_salida, $fecha_llegada, $id_bus, $id_ruta, $precios_por_piso);
        echo "Viaje actualizado con ID: $id_viaje";
        break;

    case 'eliminar_viaje':
        $id_viaje = intval($input['id_viaje'] ?? 0);
        eliminarViaje($conn, $id_viaje);
        echo "Viaje eliminado con ID: $id_viaje";
        break;

    default:
        echo "Acción no válida.";
        break;
}

exit;
