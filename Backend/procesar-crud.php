<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
include 'db_connect.php'; 
include 'crud-completo.php';

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
    $id_bus = intval($_POST['id_bus']);
    $placa = $_POST['placa'] ?? '';
    $servicio = $_POST['servicio'] ?? '';
    $n_pisos = intval($_POST['n_pisos'] ?? 2);
    $n_asientos = intval($_POST['n_asientos'] ?? 52);
    $id_sede = intval($_POST['id_sede'] ?? 1);

    actualizarBus($conn, $id_bus, $placa, $servicio, $n_pisos, $n_asientos, $id_sede);
    echo "Bus actualizado con ID: $id_bus";
    exit;
}

// Eliminar bus
if (isset($_POST['accion']) && $_POST['accion'] == 'eliminar_bus') {
    $id_bus = intval($_POST['id_bus']);
    eliminarBus($conn, $id_bus);
    echo "Bus eliminado con ID: $id_bus";
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    switch ($accion) {
        case 'insertar_bus':
            insertarBus(
                $conn,
                $_POST['placa'],
                $_POST['servicio'],
                (int)$_POST['n_pisos'],
                (int)$_POST['n_asientos'],
                (int)$_POST['id_sede']
            );
            echo "Bus insertado correctamente";
            break;

        case 'actualizar_bus':
            actualizarBus(
                $conn,
                (int)$_POST['id_bus'],
                $_POST['placa'],
                $_POST['servicio'],
                (int)$_POST['n_pisos'],
                (int)$_POST['n_asientos'],
                (int)$_POST['id_sede']
            );
            echo "Bus actualizado correctamente";
            break;

        case 'eliminar_bus':
            eliminarBus($conn, (int)$_POST['id_bus']);
            echo "Bus eliminado correctamente";
            break;

        case 'insertar_ruta':
            insertarRuta(
                $conn,
                $_POST['duracion'],
                (int)$_POST['id_origen'],
                (int)$_POST['id_llegada']
            );
            echo "Ruta insertada correctamente";
            break;

        case 'actualizar_ruta':
            actualizarRuta(
                $conn,
                (int)$_POST['id_ruta'],
                $_POST['duracion'],
                (int)$_POST['id_origen'],
                (int)$_POST['id_llegada']
            );
            echo "Ruta actualizada correctamente";
            break;

        case 'eliminar_ruta':
            eliminarRuta($conn, (int)$_POST['id_ruta']);
            echo "Ruta eliminada correctamente";
            break;

        case 'insertar_viaje':
            insertarViaje(
                $conn,
                $_POST['hora_salida'],
                $_POST['hora_llegada'],
                $_POST['fecha_salida'],
                $_POST['fecha_llegada'],
                (int)$_POST['id_bus'],
                (int)$_POST['id_ruta'],
                [
                    1 => (float)$_POST['precio_piso1'],
                    2 => (float)$_POST['precio_piso2'],
                ]
            );
            echo "Viaje insertado correctamente";
            break;

        case 'actualizar_viaje':
            actualizarViaje(
                $conn,
                (int)$_POST['id_viaje'],
                $_POST['hora_salida'],
                $_POST['hora_llegada'],
                $_POST['fecha_salida'],
                $_POST['fecha_llegada'],
                (int)$_POST['id_bus'],
                (int)$_POST['id_ruta'],
                [
                    1 => (float)$_POST['precio_piso1'],
                    2 => (float)$_POST['precio_piso2'],
                ]
            );
            echo "Viaje actualizado correctamente";
            break;

        case 'eliminar_viaje':
            eliminarViaje($conn, (int)$_POST['id_viaje']);
            echo "Viaje eliminado correctamente";
            break;

        default:
            echo "Acción no válida.";
            break;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json'); // para ver salida clara
    var_dump($_POST);
    exit; // corta la ejecución para ver solo lo que llega
}

echo "Acción no válida.";
exit;

?>