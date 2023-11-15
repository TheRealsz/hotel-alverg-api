<?php
require_once '../config.php';

$sql = "SELECT * FROM reservas";
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
        'message' => 'Erro ao buscar as reservas.'
    ];
}

echo json_encode($response);

$conn = null;