<?php
require_once '../config.php';

$sql = "SELECT * FROM clientes";
$stmt = $conn->query($sql);

if ($stmt) {
    $response = [
        'success' => true,
        'status' => 200,
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ];
} else {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => 'Erro ao buscar os clientes.'
    ];
}

echo json_encode($response);

$conn = null;