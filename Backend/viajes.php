<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

include 'db_connect.php';
include 'crud-completo.php';

// Leer parámetros GET para paginación
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 20;
$offset = ($page - 1) * $limit;

try {
    $viajes = obtenerViajes($conn, $limit, $offset);
    echo json_encode($viajes);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
