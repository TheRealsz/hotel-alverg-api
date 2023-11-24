<?php
require_once '../config.php';

try {
    $sql = "SELECT * FROM reservas";
    $stmt = $conn->query($sql);

    $response = [
        'success' => true,
        'status' => 200,
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'status' => 500,
        'message' => "Erro ao buscar as reservas: " . $e->getMessage()
    ];
}

echo json_encode($response);

$conn = null;
