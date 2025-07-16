<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include 'db_connect.php';
include 'crud-completo.php';

try {
    $buses = obtenerBuses($conn);
    echo json_encode($buses);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
